<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LessonController extends Controller
{
    /**
     * Display the lesson listing.
     */
    public function index(): View
    {
        return view('admin.lessons.index', [
            'lessons' => Lesson::query()
                ->with(['module.course', 'quiz'])
                ->orderBy('module_id')
                ->orderBy('sort_order')
                ->get(),
        ]);
    }

    /**
     * Show the lesson creation form.
     */
    public function create(): View
    {
        return view('admin.lessons.create', [
            'lesson' => new Lesson(),
            'modules' => Module::query()->with('course')->orderBy('course_id')->orderBy('sort_order')->get(),
            'keyTermsText' => '',
            'importantNote' => '',
        ]);
    }

    /**
     * Store a newly created lesson.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);

        Lesson::query()->create($validated);

        return redirect()
            ->route('admin.lessons.index')
            ->with('status', "Dars muvaffaqiyatli saqlandi.");
    }

    /**
     * Show the lesson edit form.
     */
    public function edit(Lesson $lesson): View
    {
        return view('admin.lessons.edit', [
            'lesson' => $lesson,
            'modules' => Module::query()->with('course')->orderBy('course_id')->orderBy('sort_order')->get(),
            'keyTermsText' => $this->formatKeyTerms($lesson->key_terms),
            'importantNote' => $lesson->important_note_text,
        ]);
    }

    /**
     * Update the selected lesson.
     */
    public function update(Request $request, Lesson $lesson): RedirectResponse
    {
        $validated = $this->validatedData($request, $lesson);

        $lesson->update($validated);

        return redirect()
            ->route('admin.lessons.index')
            ->with('status', "Dars yangilandi.");
    }

    /**
     * Delete the selected lesson.
     */
    public function destroy(Lesson $lesson): RedirectResponse
    {
        $lesson->delete();

        return redirect()
            ->route('admin.lessons.index')
            ->with('status', "Dars o'chirildi.");
    }

    /**
     * Validate and normalize lesson input.
     *
     * @return array<string, mixed>
     */
    protected function validatedData(Request $request, ?Lesson $lesson = null): array
    {
        $moduleId = (int) $request->input('module_id');
        $validated = $request->validate([
            'module_id' => ['required', 'exists:modules,id'],
            'sort_order' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('lessons', 'sort_order')
                    ->where(fn ($query) => $query->where('module_id', $moduleId))
                    ->ignore($lesson?->id),
            ],
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('lessons', 'slug')
                    ->where(fn ($query) => $query->where('module_id', $moduleId))
                    ->ignore($lesson?->id),
            ],
            'short_description' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'important_note' => ['nullable', 'string'],
            'key_terms_text' => ['nullable', 'string'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        return [
            'module_id' => $validated['module_id'],
            'sort_order' => $validated['sort_order'],
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'short_description' => $validated['short_description'] ?? null,
            'content' => $validated['content'] ?? null,
            'important_note_title' => filled($validated['important_note'] ?? null) ? 'Muhim eslatma' : null,
            'important_note_text' => $validated['important_note'] ?? null,
            'key_terms' => $this->parseKeyTerms($validated['key_terms_text'] ?? null),
            'duration_minutes' => $validated['duration_minutes'] ?? null,
            'is_published' => $request->boolean('is_published'),
        ];
    }

    /**
     * Convert textarea input to the expected key_terms structure.
     *
     * @return array<int, array{term: string, definition: string}>
     */
    protected function parseKeyTerms(?string $raw): array
    {
        $lines = preg_split('/\r\n|\r|\n/', (string) $raw) ?: [];

        return array_values(array_filter(array_map(function (string $line): ?array {
            $line = trim($line);

            if ($line === '') {
                return null;
            }

            [$term, $definition] = array_pad(preg_split('/\s*[|:]\s*/', $line, 2) ?: [], 2, '');
            $term = trim($term);
            $definition = trim($definition);

            if ($term === '') {
                return null;
            }

            return [
                'term' => $term,
                'definition' => $definition !== '' ? $definition : "Izoh kiritilmagan.",
            ];
        }, $lines)));
    }

    /**
     * Format stored key terms for textarea editing.
     *
     * @param mixed $keyTerms
     */
    protected function formatKeyTerms(mixed $keyTerms): string
    {
        $items = is_array($keyTerms) ? $keyTerms : [];

        return collect($items)
            ->filter(fn ($item) => is_array($item))
            ->map(fn (array $item) => trim(($item['term'] ?? '').' | '.($item['definition'] ?? '')))
            ->filter()
            ->implode("\n");
    }
}
