<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ModuleController extends Controller
{
    /**
     * Display the module listing.
     */
    public function index(): View
    {
        return view('admin.modules.index', [
            'modules' => Module::query()
                ->withCount('lessons')
                ->with('course')
                ->orderBy('course_id')
                ->orderBy('sort_order')
                ->get(),
        ]);
    }

    /**
     * Show the module creation form.
     */
    public function create(): View
    {
        return view('admin.modules.create', [
            'module' => new Module(),
            'courses' => Course::query()->orderBy('title')->get(),
        ]);
    }

    /**
     * Store a newly created module.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);

        Module::query()->create($validated);

        return redirect()
            ->route('admin.modules.index')
            ->with('status', "Modul muvaffaqiyatli saqlandi.");
    }

    /**
     * Show the module edit form.
     */
    public function edit(Module $module): View
    {
        return view('admin.modules.edit', [
            'module' => $module,
            'courses' => Course::query()->orderBy('title')->get(),
        ]);
    }

    /**
     * Update the selected module.
     */
    public function update(Request $request, Module $module): RedirectResponse
    {
        $validated = $this->validatedData($request, $module);

        $module->update($validated);

        return redirect()
            ->route('admin.modules.index')
            ->with('status', "Modul yangilandi.");
    }

    /**
     * Delete the selected module.
     */
    public function destroy(Module $module): RedirectResponse
    {
        $module->delete();

        return redirect()
            ->route('admin.modules.index')
            ->with('status', "Modul o'chirildi.");
    }

    /**
     * Validate and normalize module input.
     *
     * @return array<string, mixed>
     */
    protected function validatedData(Request $request, ?Module $module = null): array
    {
        $courseId = (int) $request->input('course_id');

        return $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'sort_order' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('modules', 'sort_order')
                    ->where(fn ($query) => $query->where('course_id', $courseId))
                    ->ignore($module?->id),
            ],
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('modules', 'slug')
                    ->where(fn ($query) => $query->where('course_id', $courseId))
                    ->ignore($module?->id),
            ],
            'short_description' => ['nullable', 'string'],
            'difficulty_level' => ['nullable', 'string', 'max:255'],
            'estimated_duration_minutes' => ['nullable', 'integer', 'min:1'],
            'is_published' => ['nullable', 'boolean'],
        ]) + [
            'is_published' => $request->boolean('is_published'),
        ];
    }
}
