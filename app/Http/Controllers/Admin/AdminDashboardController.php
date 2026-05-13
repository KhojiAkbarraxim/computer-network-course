<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                ['label' => 'Kurslar', 'value' => Course::query()->count(), 'icon' => '📚'],
                ['label' => 'Modullar', 'value' => Module::query()->count(), 'icon' => '🧩'],
                ['label' => 'Darslar', 'value' => Lesson::query()->count(), 'icon' => '🎓'],
                ['label' => 'Quizlar', 'value' => Quiz::query()->count(), 'icon' => '📝'],
                ['label' => 'Foydalanuvchilar', 'value' => User::query()->count(), 'icon' => '👥'],
                ['label' => 'Quiz urinishlari', 'value' => QuizAttempt::query()->count(), 'icon' => '📈'],
            ],
        ]);
    }
}
