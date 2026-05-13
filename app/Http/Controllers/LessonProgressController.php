<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonProgress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LessonProgressController extends Controller
{
    /**
     * Mark the lesson as completed for the authenticated user.
     */
    public function store(Request $request, Lesson $lesson): RedirectResponse
    {
        $lesson->loadMissing('module');

        abort_unless(
            $lesson->is_published && $lesson->module?->is_published,
            404
        );

        $progress = LessonProgress::query()->firstOrCreate(
            [
                'user_id' => $request->user()->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'completed_at' => now(),
            ],
        );

        return redirect()->back()->with(
            'status',
            $progress->wasRecentlyCreated
                ? 'Dars tugallangan deb belgilandi.'
                : 'Bu dars avval tugatilgan.'
        );
    }
}
