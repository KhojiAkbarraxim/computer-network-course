<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Module;
use App\Models\Quiz;
use Illuminate\View\View;
use App\Models\User;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Http\Controllers\Controller;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        $totalUsers = User::query()->count();
        $adminUsers = User::query()->where('is_admin', true)->count();
        $normalUsers = max(0, $totalUsers - $adminUsers);
        $averageQuizScore = QuizAttempt::query()->avg('score');
        $highestQuizScore = QuizAttempt::query()->max('score');

        return view('admin.dashboard', [
            'stats' => [
                ['label' => 'Jami foydalanuvchilar', 'value' => $totalUsers, 'icon' => '👥'],
                ['label' => 'Admin foydalanuvchilar', 'value' => $adminUsers, 'icon' => '🛡️'],
                ['label' => 'Oddiy foydalanuvchilar', 'value' => $normalUsers, 'icon' => '🙋'],
                ['label' => 'Jami kurslar', 'value' => Course::query()->count(), 'icon' => '📚'],
                ['label' => 'Jami modullar', 'value' => Module::query()->count(), 'icon' => '🧩'],
                ['label' => 'Jami darslar', 'value' => Lesson::query()->count(), 'icon' => '🎓'],
                ['label' => 'Tugatilgan darslar soni', 'value' => LessonProgress::query()->count(), 'icon' => '✅'],
                ['label' => 'Jami nazoratlar', 'value' => Quiz::query()->count(), 'icon' => '📝'],
                ['label' => 'Jami savollar', 'value' => QuizQuestion::query()->count(), 'icon' => '❓'],
                ['label' => 'Jami javoblar', 'value' => QuizAnswer::query()->count(), 'icon' => '✅'],
                ['label' => 'Jami quiz urinishlari', 'value' => QuizAttempt::query()->count(), 'icon' => '📈'],
                ['label' => "O'rtacha quiz natijasi", 'value' => $averageQuizScore !== null ? (int) round((float) $averageQuizScore).'%' : '0%', 'icon' => '📊'],
                ['label' => 'Eng yuqori quiz natijasi', 'value' => $highestQuizScore !== null ? (int) $highestQuizScore.'%' : '0%', 'icon' => '🏆'],
            ],
            'recentCompletedLessons' => LessonProgress::query()
                ->with(['user', 'lesson'])
                ->orderByDesc('completed_at')
                ->take(5)
                ->get(),
            'recentAttempts' => QuizAttempt::query()
                ->with(['quiz.lesson', 'user'])
                ->orderByDesc('submitted_at')
                ->take(5)
                ->get(),
            'recentUsers' => User::query()
                ->latest()
                ->take(5)
                ->get(),
            'activeUsers' => User::query()
                ->withCount([
                    'lessonProgress as completed_lessons_count',
                    'quizAttempts as quiz_attempts_count',
                ])
                ->orderByDesc('completed_lessons_count')
                ->orderByDesc('quiz_attempts_count')
                ->orderBy('name')
                ->take(5)
                ->get(),
        ]);
    }
}
