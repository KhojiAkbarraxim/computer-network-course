<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfilePageController extends Controller
{
    /**
     * Display the authenticated user's profile page.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $publishedLessonIds = Lesson::query()
            ->where('is_published', true)
            ->whereHas('module', fn ($query) => $query->where('is_published', true))
            ->pluck('id');

        $totalLessons = $publishedLessonIds->count();
        $completedLessons = $totalLessons > 0
            ? LessonProgress::query()
                ->where('user_id', $user->id)
                ->whereIn('lesson_id', $publishedLessonIds->all())
                ->count()
            : 0;
        $totalQuizAttempts = QuizAttempt::query()
            ->where('user_id', $user->id)
            ->count();
        $bestQuizScore = $totalQuizAttempts > 0
            ? (int) QuizAttempt::query()
                ->where('user_id', $user->id)
                ->max('score')
            : 0;
        $averageQuizScore = $totalQuizAttempts > 0
            ? (int) round((float) QuizAttempt::query()
                ->where('user_id', $user->id)
                ->avg('score'))
            : 0;

        return view('pages.profile', [
            'user' => $user,
            'stats' => [
                'created_at' => $user->created_at?->format('d.m.Y') ?? "Ma'lumot yo'q",
                'completed_lessons' => $completedLessons,
                'total_lessons' => $totalLessons,
                'progress_percentage' => $this->completionPercentage($completedLessons, $totalLessons),
                'has_lessons' => $totalLessons > 0,
                'total_quiz_attempts' => $totalQuizAttempts,
                'best_quiz_score' => $bestQuizScore,
                'average_quiz_score' => $averageQuizScore,
            ],
        ]);
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
