<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;

class QuizAttemptController extends Controller
{
    /**
     * Store a submitted quiz attempt for the authenticated user.
     */
    public function store(Request $request, Quiz $quiz): RedirectResponse
    {
        if (! $request->user()) {
            return Redirect::route('login')
                ->with('status', "Natijani saqlash uchun tizimga kiring.");
        }

        $quiz->load([
            'lesson.module',
            'questions.answers',
        ]);

        abort_unless(
            $quiz->is_published
                && $quiz->lesson?->is_published
                && $quiz->lesson?->module?->is_published,
            404
        );

        if ($quiz->questions->isEmpty()) {
            return Redirect::to(route('quiz.show', $quiz->id).'#natija')
                ->with('status', 'quiz-empty');
        }

        $rules = ['answers' => ['required', 'array']];

        foreach ($quiz->questions as $question) {
            $rules["answers.{$question->id}"] = [
                'required',
                Rule::exists('quiz_answers', 'id')->where(
                    fn ($query) => $query->where('quiz_question_id', $question->id)
                ),
            ];
        }

        $validated = $request->validate(
            $rules,
            [
                'answers.required' => "Barcha savollarga javob bering.",
                'answers.*.required' => "Har bir savol uchun bittadan javob tanlang.",
            ]
        );

        $selectedAnswers = $validated['answers'];
        $totalQuestions = $quiz->questions->count();
        $correctAnswers = 0;

        DB::transaction(function () use ($quiz, $request, $selectedAnswers, $totalQuestions, &$correctAnswers): void {
            $attempt = QuizAttempt::query()->create([
                'user_id' => $request->user()->id,
                'quiz_id' => $quiz->id,
                'score' => 0,
                'total_questions' => $totalQuestions,
                'correct_answers' => 0,
                'submitted_at' => now(),
            ]);

            foreach ($quiz->questions as $question) {
                $selectedAnswerId = (int) $selectedAnswers[$question->id];
                $selectedAnswer = $question->answers->firstWhere('id', $selectedAnswerId);
                $isCorrect = (bool) $selectedAnswer?->is_correct;

                if ($isCorrect) {
                    $correctAnswers++;
                }

                QuizAttemptAnswer::query()->create([
                    'quiz_attempt_id' => $attempt->id,
                    'quiz_question_id' => $question->id,
                    'quiz_answer_id' => $selectedAnswerId,
                    'is_correct' => $isCorrect,
                ]);
            }

            $attempt->update([
                'correct_answers' => $correctAnswers,
                'score' => $this->scorePercentage($correctAnswers, $totalQuestions),
            ]);
        });

        return Redirect::to(route('quiz.show', ['quiz' => $quiz->id]).'#natija')
            ->with('status', 'quiz-submitted');
    }

    /**
     * Convert correct answers into a percentage score.
     */
    protected function scorePercentage(int $correctAnswers, int $totalQuestions): int
    {
        if ($totalQuestions <= 0) {
            return 0;
        }

        return (int) round(($correctAnswers / $totalQuestions) * 100);
    }
}
