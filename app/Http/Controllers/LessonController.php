<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Module;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LessonController extends Controller
{
    /**
     * Display all published lessons with optional module filtering.
     */
    public function index(Request $request): View
    {
        $modules = Module::query()
            ->where('is_published', true)
            ->whereHas('lessons', fn ($query) => $query->where('is_published', true))
            ->orderBy('sort_order')
            ->get(['id', 'title', 'sort_order']);

        $selectedModule = $modules->firstWhere('id', $request->integer('module'));

        $lessons = Lesson::query()
            ->where('is_published', true)
            ->whereHas('module', fn ($query) => $query->where('is_published', true))
            ->with([
                'module:id,title,sort_order',
                'quiz' => fn ($query) => $query
                    ->where('is_published', true)
                    ->select('id', 'lesson_id'),
            ])
            ->when(
                $selectedModule,
                fn ($query) => $query->where('module_id', $selectedModule->id)
            )
            ->orderBy(
                Module::query()
                    ->select('sort_order')
                    ->whereColumn('modules.id', 'lessons.module_id')
                    ->limit(1)
            )
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $completedLessonIds = $this->completedLessonIds(auth()->id(), $lessons->pluck('id')->all());
        $groupedLessons = $lessons
            ->groupBy('module_id')
            ->map(function (Collection $moduleLessons): array {
                /** @var \App\Models\Lesson $firstLesson */
                $firstLesson = $moduleLessons->first();

                return [
                    'module' => $firstLesson->module,
                    'lessons' => $moduleLessons,
                ];
            })
            ->values()
            ->map(fn (array $group): array => [
                'module' => [
                    'id' => $group['module']->id,
                    'title' => $group['module']->title,
                    'sort_order' => $group['module']->sort_order,
                ],
                'lessons' => $group['lessons']->map(
                    fn (Lesson $lesson) => $this->lessonListItem($lesson, $completedLessonIds)
                )->all(),
            ])
            ->all();

        return view('lessons', [
            'pageTitle' => 'Darslar',
            'modules' => $modules,
            'selectedModule' => $selectedModule,
            'lessonGroups' => $groupedLessons,
            'isGuest' => auth()->guest(),
        ]);
    }

    /**
     * Display the first available lesson.
     */
    public function sample(): View
    {
        $lesson = $this->firstPublishedLesson();

        return $this->renderLessonPage($lesson);
    }

    /**
     * Display the selected lesson.
     */
    public function show(string $lesson): View
    {
        $lessonModel = $this->lessonDetailQuery()
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
        $completedLessonIds = $this->completedLessonIds(auth()->id(), $moduleLessons->pluck('id')->all());
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
                'visual' => $this->visualPayload($lessonModel),
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
     * Base query for published lesson detail pages.
     */
    protected function lessonDetailQuery()
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
     * Resolve the first published lesson in module order.
     */
    protected function firstPublishedLesson(): ?Lesson
    {
        return $this->lessonDetailQuery()
            ->orderBy(
                Module::query()
                    ->select('sort_order')
                    ->whereColumn('modules.id', 'lessons.module_id')
                    ->limit(1)
            )
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();
    }

    /**
     * Fetch completed lesson ids for the current user.
     *
     * @param array<int, int> $lessonIds
     * @return array<int, int>
     */
    protected function completedLessonIds(?int $userId, array $lessonIds): array
    {
        if ($userId === null || $lessonIds === []) {
            return [];
        }

        return LessonProgress::query()
            ->where('user_id', $userId)
            ->whereIn('lesson_id', $lessonIds)
            ->pluck('lesson_id')
            ->all();
    }

    /**
     * Transform lesson model into listing card payload.
     *
     * @param array<int, int> $completedLessonIds
     * @return array<string, mixed>
     */
    protected function lessonListItem(Lesson $lesson, array $completedLessonIds): array
    {
        $previewSource = $lesson->short_description ?: $lesson->content;

        return [
            'id' => $lesson->id,
            'title' => $lesson->title,
            'module_title' => $lesson->module?->title,
            'preview' => $this->lessonPreview($previewSource),
            'duration' => $lesson->duration_minutes ? $this->formatDuration($lesson->duration_minutes) : null,
            'status' => auth()->check()
                ? (in_array($lesson->id, $completedLessonIds, true) ? 'Tugatilgan' : 'Boshlanmagan')
                : 'Progress uchun tizimga kiring',
            'status_variant' => auth()->check()
                ? (in_array($lesson->id, $completedLessonIds, true) ? 'completed' : 'not-started')
                : 'guest',
            'quiz_status' => $lesson->quiz ? 'Nazorat mavjud' : "Nazorat yo'q",
            'quiz_variant' => $lesson->quiz ? 'available' : 'missing',
            'url' => route('lesson.show', $lesson->id),
        ];
    }

    /**
     * Build a short readable preview for listing cards.
     */
    protected function lessonPreview(?string $text): string
    {
        $normalized = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $text)) ?? '');

        if ($normalized === '') {
            return "Qisqacha tavsif hozircha kiritilmagan.";
        }

        return Str::limit($normalized, 140);
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
     * Build the visual explanation payload for the lesson page.
     *
     * @return array<string, mixed>
     */
    protected function visualPayload(Lesson $lesson): array
    {
        $steps = collect(is_array($lesson->visual_steps) ? $lesson->visual_steps : [])
            ->map(fn ($step) => trim((string) $step))
            ->filter()
            ->values()
            ->all();
        $diagramType = $lesson->diagram_type ?: 'default';
        $hasVisualData = filled($lesson->visual_title)
            || filled($lesson->visual_description)
            || $steps !== []
            || filled($lesson->diagram_type);

        return [
            'has_data' => $hasVisualData,
            'title' => $lesson->visual_title ?: $lesson->title,
            'description' => $lesson->visual_description,
            'steps' => $steps,
            'diagram_type' => $diagramType,
            'diagram' => $this->diagramPayload($diagramType),
        ];
    }

    /**
     * Provide a simple diagram payload for the supported visual types.
     *
     * @return array<string, mixed>
     */
    protected function diagramPayload(string $diagramType): array
    {
        return match ($diagramType) {
            'basic-network' => [
                'layout' => 'linear',
                'nodes' => ['Kompyuter', 'Switch', 'Router', 'Internet'],
            ],
            'network-types' => [
                'layout' => 'linear',
                'nodes' => ['LAN', 'MAN', 'WAN'],
            ],
            'osi' => [
                'layout' => 'stacked',
                'nodes' => [
                    '7. Ilova qatlami',
                    '6. Taqdimot qatlami',
                    '5. Sessiya qatlami',
                    '4. Transport qatlami',
                    '3. Tarmoq qatlami',
                    "2. Ma'lumotlar havolasi qatlami",
                    '1. Fizik qatlam',
                ],
            ],
            'tcp-ip' => [
                'layout' => 'linear',
                'nodes' => ['Ilova', 'Transport', 'Internet', "Tarmoqqa kirish"],
            ],
            'ip-subnet' => [
                'layout' => 'linear',
                'nodes' => ['IP manzil', 'Subnet maska', 'Tarmoq qismi', 'Host qismi'],
            ],
            'devices' => [
                'layout' => 'linear',
                'nodes' => ['Hub', 'Switch', 'Router'],
            ],
            'dns-dhcp-nat' => [
                'layout' => 'linear',
                'nodes' => ['DNS', 'DHCP', 'NAT'],
            ],
            'wifi' => [
                'layout' => 'linear',
                'nodes' => ['Qurilma', 'Wi-Fi router', 'Internet'],
            ],
            'security' => [
                'layout' => 'linear',
                'nodes' => ['Foydalanuvchi', 'Firewall', 'Himoyalangan tarmoq'],
            ],
            'lab' => [
                'layout' => 'linear',
                'nodes' => ['Topshiriq', 'Buyruq', 'Tekshirish', 'Natija'],
            ],
            default => [
                'layout' => 'linear',
                'nodes' => ['Tushuncha', 'Jarayon', 'Natija'],
            ],
        };
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
