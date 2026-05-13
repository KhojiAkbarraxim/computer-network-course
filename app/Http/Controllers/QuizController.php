<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class QuizController extends Controller
{
    /**
     * Display the first available quiz.
     */
    public function showSample(Request $request): View
    {
        $quiz = $this->quizQuery()
            ->orderBy('id')
            ->first();

        return $this->renderQuizPage($quiz, $request);
    }

    /**
     * Display the selected quiz.
     */
    public function show(Request $request, string $quiz): View
    {
        $quizModel = $this->quizQuery()
            ->whereKey($quiz)
            ->first();

        return $this->renderQuizPage($quizModel, $request);
    }

    /**
     * Build the quiz page payload.
     */
    protected function renderQuizPage(?Quiz $quizModel, Request $request): View
    {
        if ($quizModel === null) {
            return view('quiz-sample', [
                'pageTitle' => 'Namuna nazorat',
                'quiz' => null,
            ]);
        }

        $lesson = $quizModel->lesson;
        $module = $lesson?->module;
        $attempts = $request->user()
            ? QuizAttempt::query()
                ->where('user_id', $request->user()->id)
                ->where('quiz_id', $quizModel->id)
                ->with(['attemptAnswers.quizAnswer'])
                ->orderByDesc('submitted_at')
                ->orderByDesc('id')
                ->get()
            : collect();
        $latestAttempt = $attempts->first();

        return view('quiz-sample', [
            'pageTitle' => $quizModel->title,
            'quiz' => [
                'id' => $quizModel->id,
                'title' => $quizModel->title,
                'description' => $quizModel->description ?: "Ushbu nazorat savollari darsdagi asosiy tushunchalarni takrorlash uchun mo'ljallangan.",
                'lesson_title' => $lesson?->title,
                'module_title' => $module?->title,
                'questions' => $quizModel->questions->map(fn ($question) => [
                    'id' => $question->id,
                    'question' => $question->question_text,
                    'options' => $question->answers->map(fn ($answer) => [
                        'id' => $answer->id,
                        'text' => $answer->answer_text,
                    ])->all(),
                ])->all(),
                'latest_attempt' => $latestAttempt ? $this->latestAttemptPayload($latestAttempt, $quizModel->questions) : null,
                'attempt_history' => $attempts->take(5)->map(fn (QuizAttempt $attempt): array => [
                    'submitted_at' => $attempt->submitted_at?->format('d.m.Y H:i') ?? "Vaqt ma'lum emas",
                    'score' => $attempt->score,
                    'correct_answers' => $attempt->correct_answers,
                    'total_questions' => $attempt->total_questions,
                ])->all(),
                'has_previous_attempts' => $attempts->count() > 1,
                'has_questions' => $quizModel->questions->isNotEmpty(),
                'guest_message' => "Natijani saqlash uchun tizimga kiring.",
            ],
        ]);
    }

    /**
     * Build the latest attempt payload including answer review.
     */
    protected function latestAttemptPayload(QuizAttempt $attempt, Collection $questions): array
    {
        $attemptAnswers = $attempt->attemptAnswers->keyBy('quiz_question_id');
        $reviews = $questions->map(function (QuizQuestion $question) use ($attemptAnswers): array {
            $attemptAnswer = $attemptAnswers->get($question->id);
            $selectedAnswer = $attemptAnswer?->quizAnswer;
            $correctAnswer = $question->answers->firstWhere('is_correct', true);

            return [
                'question' => $question->question_text,
                'selected_answer' => $selectedAnswer?->answer_text ?? "Javob belgilanmagan",
                'is_correct' => (bool) $attemptAnswer?->is_correct,
                'correct_answer' => $correctAnswer?->answer_text,
            ];
        })->all();

        return [
            'total_questions' => $attempt->total_questions,
            'correct_answers' => $attempt->correct_answers,
            'wrong_answers' => max(0, $attempt->total_questions - $attempt->correct_answers),
            'score' => $attempt->score,
            'submitted_at' => $attempt->submitted_at?->format('d.m.Y H:i') ?? "Vaqt ma'lum emas",
            'reviews' => $reviews,
        ];
    }

    /**
     * Base query for published quizzes.
     */
    protected function quizQuery()
    {
        return Quiz::query()
            ->where('is_published', true)
            ->with([
                'lesson' => fn ($query) => $query
                    ->where('is_published', true)
                    ->with([
                        'module' => fn ($moduleQuery) => $moduleQuery->where('is_published', true),
                    ]),
                'questions.answers',
            ]);
    }
}
