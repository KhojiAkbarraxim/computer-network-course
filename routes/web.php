<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.home', [
        'home' => config('demo-course.home'),
        'modules' => array_slice(config('demo-course.modules', []), 0, 4),
        'statistics' => config('demo-course.statistics'),
    ]);
})->name('home');

Route::get('/course', function () {
    return view('pages.course', [
        'modules' => config('demo-course.modules'),
    ]);
})->name('course');

Route::get('/lesson/sample', function () {
    return view('pages.lesson', [
        'lesson' => config('demo-course.lesson'),
        'modules' => config('demo-course.modules'),
    ]);
})->name('lesson.sample');

Route::get('/quiz/sample', function () {
    return view('pages.quiz', [
        'quiz' => config('demo-course.quiz'),
    ]);
})->name('quiz.sample');

Route::get('/dashboard', function () {
    return view('pages.dashboard', [
        'dashboard' => config('demo-course.dashboard'),
        'modules' => config('demo-course.modules'),
    ]);
})->name('dashboard');

Route::get('/about', function () {
    return view('pages.about', [
        'about' => config('demo-course.about'),
    ]);
})->name('about');
