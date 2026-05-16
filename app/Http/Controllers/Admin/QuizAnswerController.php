<?php

namespace App\Http\Controllers\Admin;

use Illuminate\View\View;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class QuizAnswerController extends Controller
{
    /**
     * Display the answers for the selected question.
     */
    public function index(QuizQuestion $question): View
    {
        $question->load([
            'quiz.lesson.module.course',
            'answers' => fn ($query) => $query->orderBy('sort_order'),
        ]);

        return view('admin.answers.index', [
            'question' => $question,
            'quiz' => $question->quiz,
        ]);
    }

    /**
     * Show the answer creation form.
     */
    public function create(QuizQuestion $question): View
    {
        $question->load('quiz.lesson.module.course');

        return view('admin.answers.create', [
            'question' => $question,
            'quiz' => $question->quiz,
            'answer' => new QuizAnswer([
                'sort_order' => $this->nextSortOrder($question),
                'is_correct' => ! $question->answers()->where('is_correct', true)->exists(),
            ]),
        ]);
    }

    /**
     * Store a newly created answer.
     */
    public function store(Request $request, QuizQuestion $question): RedirectResponse
    {
        $validated = $this->validatedData($request, $question);

        DB::transaction(function () use ($question, $validated): void {
            $answer = QuizAnswer::query()->create($validated);
            $this->syncCorrectAnswer($question, $answer);
        });

        return redirect()
            ->route('admin.questions.answers.index', $question)
            ->with('status', "Javob muvaffaqiyatli saqlandi.");
    }

    /**
     * Show the answer edit form.
     */
    public function edit(QuizAnswer $answer): View
    {
        $answer->load('question.quiz.lesson.module.course');

        return view('admin.answers.edit', [
            'answer' => $answer,
            'question' => $answer->question,
            'quiz' => $answer->question->quiz,
        ]);
    }

    /**
     * Update the selected answer.
     */
    public function update(Request $request, QuizAnswer $answer): RedirectResponse
    {
        $question = $answer->question;
        $validated = $this->validatedData($request, $question, $answer);

        DB::transaction(function () use ($answer, $question, $validated): void {
            $answer->update($validated);
            $this->syncCorrectAnswer($question, $answer->fresh());
        });

        return redirect()
            ->route('admin.questions.answers.index', $question)
            ->with('status', "Javob yangilandi.");
    }

    /**
     * Delete the selected answer.
     */
    public function destroy(QuizAnswer $answer): RedirectResponse
    {
        $question = $answer->question;

        if ($answer->is_correct && ! $question->answers()->whereKeyNot($answer->id)->where('is_correct', true)->exists()) {
            return redirect()
                ->route('admin.questions.answers.index', $question)
                ->with('error', "Savolda kamida bitta to'g'ri javob qolishi kerak.");
        }

        try {
            $answer->delete();
        } catch (QueryException) {
            return redirect()
                ->route('admin.questions.answers.index', $question)
                ->with('error', "Javobni o'chirishda xatolik yuz berdi. Bog'liq ma'lumotlarni tekshirib qayta urinib ko'ring.");
        }

        return redirect()
            ->route('admin.questions.answers.index', $question)
            ->with('status', "Javob o'chirildi.");
    }

    /**
     * Validate and normalize answer input.
     *
     * @return array<string, mixed>
     */
    protected function validatedData(Request $request, QuizQuestion $question, ?QuizAnswer $answer = null): array
    {
        $validated = $request->validate([
            'sort_order' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('quiz_answers', 'sort_order')
                    ->where(fn ($query) => $query->where('quiz_question_id', $question->id))
                    ->ignore($answer?->id),
            ],
            'answer_text' => ['required', 'string', 'max:255'],
            'is_correct' => ['nullable', 'boolean'],
        ]);

        $isCorrect = $request->boolean('is_correct');
        $hasOtherCorrectAnswer = $question->answers()
            ->when($answer !== null, fn ($query) => $query->whereKeyNot($answer->id))
            ->where('is_correct', true)
            ->exists();

        if (! $isCorrect && ! $hasOtherCorrectAnswer) {
            throw ValidationException::withMessages([
                'is_correct' => "Savolda kamida bitta to'g'ri javob bo'lishi kerak.",
            ]);
        }

        return [
            'quiz_question_id' => $question->id,
            'sort_order' => $validated['sort_order'],
            'answer_text' => $validated['answer_text'],
            'is_correct' => $isCorrect,
        ];
    }

    /**
     * Keep a single active correct answer for the question.
     */
    protected function syncCorrectAnswer(QuizQuestion $question, QuizAnswer $answer): void
    {
        if (! $answer->is_correct) {
            return;
        }

        $question->answers()
            ->whereKeyNot($answer->id)
            ->update(['is_correct' => false]);
    }

    /**
     * Guess the next sort number for the question.
     */
    protected function nextSortOrder(QuizQuestion $question): int
    {
        return ((int) $question->answers()->max('sort_order')) + 1;
    }
}
