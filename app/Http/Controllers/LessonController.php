<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Module;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class LessonController extends Controller
{
    /**
     * Display the first available lesson.
     */
    public function sample(): View
    {
        $lesson = $this->lessonQuery()
            ->orderBy(
                Module::query()
                    ->select('sort_order')
                    ->whereColumn('modules.id', 'lessons.module_id')
                    ->limit(1)
            )
            ->orderBy('sort_order')
            ->first();

        return $this->renderLessonPage($lesson);
    }

    /**
     * Display the selected lesson.
     */
    public function show(string $lesson): View
    {
        $lessonModel = $this->lessonQuery()
            ->whereKey($lesson)
            ->first();

        return $this->renderLessonPage($lessonModel);
    }

    /**
     * Build the lesson view payload.
     */
    protected function renderLessonPage(?Lesson $lessonModel): View
    {
        if ($lessonModel === null) {
            return view('lesson-sample', [
                'lesson' => null,
                'pageTitle' => 'Namuna dars',
            ]);
        }

        $module = $lessonModel->module;
        $moduleLessons = $module->lessons->values();
        $userId = auth()->id();
        $completedLessonIds = $userId
            ? LessonProgress::query()
                ->where('user_id', $userId)
                ->whereIn('lesson_id', $moduleLessons->pluck('id')->all())
                ->pluck('lesson_id')
                ->all()
            : [];
        $currentIndex = $moduleLessons->search(fn (Lesson $lesson) => $lesson->id === $lessonModel->id);
        $previousLesson = $currentIndex !== false ? $moduleLessons->get($currentIndex - 1) : null;
        $nextLesson = $currentIndex !== false ? $moduleLessons->get($currentIndex + 1) : null;
        $progress = $this->completionPercentage(count($completedLessonIds), $moduleLessons->count());

        return view('lesson-sample', [
            'pageTitle' => $lessonModel->title,
            'lesson' => [
                'id' => $lessonModel->id,
                'module_label' => str_pad((string) $module->sort_order, 2, '0', STR_PAD_LEFT).'-modul',
                'module_title' => $module->title,
                'progress' => $progress,
                'current_lesson' => $lessonModel->title,
                'duration' => $this->formatDuration($lessonModel->duration_minutes),
                'is_completed' => in_array($lessonModel->id, $completedLessonIds, true),
                'sidebar_lessons' => $moduleLessons->map(fn (Lesson $lesson) => [
                    'id' => $lesson->id,
                    'title' => $lesson->title,
                    'duration' => $this->formatDuration($lesson->duration_minutes),
                    'active' => $lesson->id === $lessonModel->id,
                ])->all(),
                'paragraphs' => $this->contentParagraphs($lessonModel->content, $lessonModel->short_description),
                'note' => [
                    'title' => $lessonModel->important_note_title ?: 'Muhim eslatma',
                    'text' => $lessonModel->important_note_text ?: "Bu darsdagi asosiy tushunchalarni amaliy misollar bilan birga takrorlab chiqish tavsiya etiladi.",
                ],
                'key_terms' => $this->normalizeKeyTerms($lessonModel->key_terms),
                'previous' => $previousLesson ? [
                    'title' => $previousLesson->title,
                    'url' => route('lesson.show', $previousLesson->id),
                ] : null,
                'next' => $nextLesson ? [
                    'title' => $nextLesson->title,
                    'url' => route('lesson.show', $nextLesson->id),
                ] : null,
            ],
        ]);
    }

    /**
     * Base query for published lessons.
     */
    protected function lessonQuery()
    {
        return Lesson::query()
            ->where('is_published', true)
            ->with([
                'module' => fn ($query) => $query
                    ->where('is_published', true)
                    ->with([
                        'lessons' => fn ($lessonQuery) => $lessonQuery
                            ->where('is_published', true)
                            ->orderBy('sort_order'),
                    ]),
            ]);
    }

    /**
     * Split lesson content into readable paragraphs.
     *
     * @return list<string>
     */
    protected function contentParagraphs(?string $content, ?string $fallback): array
    {
        $paragraphs = preg_split('/\n\s*\n/u', trim((string) $content)) ?: [];
        $paragraphs = array_values(array_filter(array_map('trim', $paragraphs)));

        if ($paragraphs !== []) {
            return $paragraphs;
        }

        if ($fallback !== null && trim($fallback) !== '') {
            return [trim($fallback)];
        }

        return ["Ushbu dars uchun batafsil matn keyinroq to'ldiriladi."];
    }

    /**
     * Ensure the key terms structure is consistent.
     *
     * @param mixed $keyTerms
     * @return array<int, array{term: string, definition: string}>
     */
    protected function normalizeKeyTerms(mixed $keyTerms): array
    {
        $items = is_array($keyTerms) ? $keyTerms : [];

        $normalized = array_values(array_filter(array_map(function (mixed $item): ?array {
            if (! is_array($item)) {
                return null;
            }

            $term = trim((string) Arr::get($item, 'term', ''));
            $definition = trim((string) Arr::get($item, 'definition', ''));

            if ($term === '' || $definition === '') {
                return null;
            }

            return [
                'term' => $term,
                'definition' => $definition,
            ];
        }, $items)));

        if ($normalized !== []) {
            return $normalized;
        }

        return [
            [
                'term' => 'Asosiy tushuncha',
                'definition' => "Dars mazmunini mustahkamlash uchun asosiy terminlar keyinroq kengaytiriladi.",
            ],
        ];
    }

    /**
     * Convert minutes to Uzbek readable duration.
     */
    protected function formatDuration(?int $minutes): string
    {
        if ($minutes === null || $minutes <= 0) {
            return "Vaqt kiritilmagan";
        }

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        if ($hours > 0 && $remainingMinutes > 0) {
            return "{$hours} soat {$remainingMinutes} daqiqa";
        }

        if ($hours > 0) {
            return "{$hours} soat";
        }

        return "{$remainingMinutes} daqiqa";
    }

    /**
     * Build a simple demo progress value until real progress tables exist.
     */
    protected function completionPercentage(int $completedLessons, int $totalLessons): int
    {
        if ($totalLessons <= 0) {
            return 0;
        }

        return (int) round(($completedLessons / $totalLessons) * 100);
    }
}
