<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Module;
use App\Models\QuizAttempt;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard using database-backed course data and real user progress.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $course = Course::query()
            ->where('is_published', true)
            ->with([
                'modules' => fn ($query) => $query
                    ->where('is_published', true)
                    ->with([
                        'lessons' => fn ($lessonQuery) => $lessonQuery
                            ->where('is_published', true)
                            ->orderBy('sort_order')
                            ->with([
                                'lessonProgress' => fn ($progressQuery) => $progressQuery
                                    ->where('user_id', $user->id),
                            ]),
                    ])
                    ->orderBy('sort_order'),
            ])
            ->orderBy('id')
            ->first();

        if ($course === null || $course->modules->isEmpty()) {
            return view('dashboard', [
                'dashboard' => null,
                'emptyMessage' => "Hozircha darslar mavjud emas.",
            ]);
        }

        $modules = $course->modules;
        $lessons = $modules
            ->flatMap(fn (Module $module) => $module->lessons->map(function (Lesson $lesson) use ($module): Lesson {
                $lesson->setRelation('module', $module);

                return $lesson;
            }))
            ->values();

        if ($lessons->isEmpty()) {
            return view('dashboard', [
                'dashboard' => null,
                'emptyMessage' => "Hozircha darslar mavjud emas.",
            ]);
        }

        $completedLessonIds = $lessons
            ->filter(fn (Lesson $lesson) => $lesson->lessonProgress->isNotEmpty())
            ->pluck('id');

        $totalLessons = $lessons->count();
        $completedLessonsCount = $completedLessonIds->count();
        $completionPercentage = $this->completionPercentage($completedLessonsCount, $totalLessons);
        $remainingLessons = max(0, $totalLessons - $completedLessonsCount);
        $continueLesson = $lessons->first(
            fn (Lesson $lesson) => ! $completedLessonIds->contains($lesson->id)
        );
        $firstLesson = $lessons->first();

        $completedLessons = LessonProgress::query()
            ->where('user_id', $user->id)
            ->whereIn('lesson_id', $lessons->pluck('id')->all())
            ->with(['lesson.module'])
            ->latest('completed_at')
            ->limit(5)
            ->get();
        $totalQuizAttempts = QuizAttempt::query()
            ->where('user_id', $user->id)
            ->count();
        $averageQuizScore = $totalQuizAttempts > 0
            ? (int) round((float) QuizAttempt::query()
                ->where('user_id', $user->id)
                ->avg('score'))
            : 0;
        $latestQuizAttempt = QuizAttempt::query()
            ->where('user_id', $user->id)
            ->with(['quiz.lesson'])
            ->orderByDesc('submitted_at')
            ->orderByDesc('id')
            ->first();

        return view('dashboard', [
            'dashboard' => [
                'course_title' => $course->title,
                'course_summary' => "Jami {$totalLessons} ta darsdan {$completedLessonsCount} tasi tugatilgan. Umumiy o'zlashtirish {$completionPercentage}% ni tashkil etadi.",
                'overview' => [
                    ['label' => 'Jami darslar', 'value' => "{$totalLessons} ta", 'icon' => '📚'],
                    ['label' => "Tugallangan darslar", 'value' => "{$completedLessonsCount} ta", 'icon' => '🎯'],
                    ['label' => "Umumiy progress", 'value' => "{$completionPercentage}%", 'icon' => '📈'],
                    ['label' => "Qolgan darslar", 'value' => "{$remainingLessons} ta", 'icon' => '⏳'],
                ],
                'continue' => $this->continueLearningCard($continueLesson, $firstLesson, $completedLessonIds),
                'completed_lessons' => $this->completedLessonCards($completedLessons),
                'quiz_summary' => [
                    'latest_title' => $latestQuizAttempt?->quiz?->title,
                    'lesson_title' => $latestQuizAttempt?->quiz?->lesson?->title,
                    'latest_score' => $latestQuizAttempt?->score,
                    'total_attempts' => $totalQuizAttempts,
                    'average_score' => $totalQuizAttempts > 0 ? $averageQuizScore : null,
                    'submitted_at' => $latestQuizAttempt?->submitted_at?->format('d.m.Y H:i'),
                ],
                'scores' => [
                    [
                        'title' => "Tugatilgan darslar",
                        'score' => "{$completedLessonsCount} ta",
                        'status' => "Siz yakunlagan darslar soni",
                    ],
                    [
                        'title' => 'Jami darslar',
                        'score' => "{$totalLessons} ta",
                        'status' => "Kurs bo'yicha mavjud darslar soni",
                    ],
                    [
                        'title' => "Umumiy progress",
                        'score' => "{$completionPercentage}%",
                        'status' => $remainingLessons > 0
                            ? "{$remainingLessons} ta dars hali tugatilmagan"
                            : "Barcha darslar tugatilgan",
                    ],
                ],
                'module_progress' => $modules->map(function (Module $module): array {
                    $lessonCount = $module->lessons->count();
                    $completedCount = $module->lessons
                        ->filter(fn (Lesson $lesson) => $lesson->lessonProgress->isNotEmpty())
                        ->count();

                    return [
                        'title' => $module->title,
                        'lesson_count' => $lessonCount,
                        'completed_count' => $completedCount,
                        'progress' => $this->completionPercentage($completedCount, $lessonCount),
                    ];
                })->all(),
            ],
        ]);
    }

    /**
     * Build the continue-learning card payload.
     */
    protected function continueLearningCard(?Lesson $continueLesson, ?Lesson $firstLesson, Collection $completedLessonIds): array
    {
        if ($continueLesson) {
            $moduleLessonCount = $continueLesson->module->lessons->count();
            $moduleCompletedCount = $continueLesson->module->lessons
                ->filter(fn (Lesson $lesson) => $completedLessonIds->contains($lesson->id))
                ->count();

            return [
                'module' => $continueLesson->module->title,
                'lesson' => $continueLesson->title,
                'text' => $continueLesson->short_description ?: "Navbatdagi darsni davom ettirib, umumiy progressni oshirishingiz mumkin.",
                'progress' => $this->completionPercentage($moduleCompletedCount, $moduleLessonCount),
                'url' => route('lesson.show', $continueLesson->id),
                'button' => "O'qishni davom ettirish",
                'summary_title' => "Davom etish uchun tayyor",
                'summary_text' => "Hali tugallanmagan birinchi dars shu yerda ko'rsatildi.",
            ];
        }

        return [
            'module' => "Tabriklaymiz!",
            'lesson' => "Barcha darslar tugatilgan",
            'text' => $firstLesson
                ? "Siz kursdagi barcha darslarni yakunladingiz. Istasangiz darslarni qayta ko'rib chiqishingiz mumkin."
                : "Hozircha darslar mavjud emas.",
            'progress' => 100,
            'url' => $firstLesson ? route('lesson.show', $firstLesson->id) : route('course'),
            'button' => $firstLesson ? "Darslarni qayta ko'rish" : "Kursni ko'rish",
            'summary_title' => "Yakunlangan holat",
            'summary_text' => "Progress jadvali to'liq tugallangan darslarga asoslanmoqda.",
        ];
    }

    /**
     * Build completed lesson cards from stored user progress rows.
     *
     * @return array<int, array{title: string, module: string, time: string}>
     */
    protected function completedLessonCards(Collection $progressRows): array
    {
        return $progressRows->values()->map(function (LessonProgress $progress): array {
            $lesson = $progress->lesson;
            $moduleSortOrder = $lesson?->module?->sort_order ?? 0;
            $moduleTitle = $lesson?->module?->title ?? "Modul ma'lum emas";

            return [
                'title' => $lesson?->title ?? "Dars ma'lum emas",
                'module' => str_pad((string) $moduleSortOrder, 2, '0', STR_PAD_LEFT)."-modul • {$moduleTitle}",
                'time' => $this->completionTimeLabel($progress->completed_at),
            ];
        })->all();
    }

    /**
     * Format the completion date in a compact Uzbek-friendly way.
     */
    protected function completionTimeLabel(?CarbonInterface $completedAt): string
    {
        if ($completedAt === null) {
            return "Vaqt ma'lum emas";
        }

        if ($completedAt->isToday()) {
            return 'Bugun';
        }

        if ($completedAt->isYesterday()) {
            return 'Kecha';
        }

        return $completedAt->format('d.m.Y');
    }

    /**
     * Convert lesson counts into a percentage.
     */
    protected function completionPercentage(int $completedLessons, int $totalLessons): int
    {
        if ($totalLessons <= 0) {
            return 0;
        }

        return (int) round(($completedLessons / $totalLessons) * 100);
    }
}
