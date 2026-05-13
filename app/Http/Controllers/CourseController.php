<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Module;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * Display the main course and its modules.
     */
    public function index(): View
    {
        $course = Course::query()
            ->where('is_published', true)
            ->with([
                'modules' => fn ($query) => $query
                    ->where('is_published', true)
                    ->withCount('lessons')
                    ->orderBy('sort_order'),
            ])
            ->orderBy('id')
            ->first();

        $modules = $course?->modules?->map(function (Module $module): Module {
            $module->setAttribute('demo_progress', $this->demoProgress($module->sort_order));

            return $module;
        }) ?? collect();

        return view('course', [
            'course' => $course,
            'modules' => $modules,
        ]);
    }

    /**
     * Build a simple demo progress value until real progress tables exist.
     */
    protected function demoProgress(int $sortOrder): int
    {
        return min(100, $sortOrder * 8);
    }
}
