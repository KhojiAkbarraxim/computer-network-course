<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\LessonController as AdminLessonController;
use App\Http\Controllers\Admin\ModuleController as AdminModuleController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\LessonProgressController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfilePageController;
use App\Http\Controllers\QuizAttemptController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $demoCourse = config('demo-course');

    return view('home', [
        'home' => $demoCourse['home'],
        'statistics' => $demoCourse['statistics'],
        'modules' => Collection::make($demoCourse['modules'])->take(4)->all(),
    ]);
})->name('home');

Route::get('/course', [CourseController::class, 'index'])->name('course');

Route::get('/lesson/sample', [LessonController::class, 'sample'])->name('lesson.sample');
Route::get('/lesson/{lesson}', [LessonController::class, 'show'])->name('lesson.show');

Route::get('/quiz/sample', [QuizController::class, 'showSample'])->name('quiz.sample');
Route::get('/quiz/{quiz}', [QuizController::class, 'show'])->name('quiz.show');
Route::post('/quiz/{quiz}/submit', [QuizAttemptController::class, 'store'])->name('quiz.submit');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::get('/about', function () {
    return view('about', [
        'about' => config('demo-course.about'),
    ]);
})->name('about');

Route::middleware('auth')->group(function () {
    Route::post('/lesson/{lesson}/complete', [LessonProgressController::class, 'store'])->name('lesson.complete');
    Route::get('/profile', [ProfilePageController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function (): void {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('modules', AdminModuleController::class)->except(['show']);
        Route::resource('lessons', AdminLessonController::class)->except(['show']);
    });

require __DIR__.'/auth.php';
