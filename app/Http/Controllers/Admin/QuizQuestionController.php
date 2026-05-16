<?php

namespace App\Http\Controllers\Admin;

use App\Models\Quiz;
use Illuminate\View\View;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\QueryException;

class QuizQuestionController extends Controller
{
    /**
     * Display the questions for the selected quiz.
     */
    public function index(Quiz $quiz): View
    {
        $quiz->load([
            'lesson.module.course',
            'questions' => fn ($query) => $query->withCount('answers')->orderBy('sort_order'),
        ]);

        return view('admin.questions.index', [
            'quiz' => $quiz,
        ]);
    }

    /**
     * Show the question creation form.
     */
    public function create(Quiz $quiz): View
    {
        $quiz->load('lesson.module.course');

        return view('admin.questions.create', [
            'quiz' => $quiz,
            'question' => new QuizQuestion([
                'sort_order' => $this->nextSortOrder($quiz),
            ]),
        ]);
    }

    /**
     * Store a newly created question.
     */
    public function store(Request $request, Quiz $quiz): RedirectResponse
    {
        QuizQuestion::query()->create($this->validatedData($request, $quiz));

        return redirect()
            ->route('admin.quizzes.questions.index', $quiz)
            ->with('status', "Savol muvaffaqiyatli saqlandi.");
    }

    /**
     * Show the question edit form.
     */
    public function edit(QuizQuestion $question): View
    {
        $question->load('quiz.lesson.module.course');

        return view('admin.questions.edit', [
            'quiz' => $question->quiz,
            'question' => $question,
        ]);
    }

    /**
     * Update the selected question.
     */
    public function update(Request $request, QuizQuestion $question): RedirectResponse
    {
        $question->update($this->validatedData($request, $question->quiz, $question));

        return redirect()
            ->route('admin.quizzes.questions.index', $question->quiz)
            ->with('status', "Savol yangilandi.");
    }

    /**
     * Delete the selected question.
     */
    public function destroy(QuizQuestion $question): RedirectResponse
    {
        $quiz = $question->quiz;

        try {
            $question->delete();
        } catch (QueryException) {
            return redirect()
                ->route('admin.quizzes.questions.index', $quiz)
                ->with('error', "Savolni o'chirishda xatolik yuz berdi. Bog'liq ma'lumotlarni tekshirib qayta urinib ko'ring.");
        }

        return redirect()
            ->route('admin.quizzes.questions.index', $quiz)
            ->with('status', "Savol o'chirildi.");
    }

    /**
     * Validate and normalize question input.
     *
     * @return array<string, mixed>
     */
    protected function validatedData(Request $request, Quiz $quiz, ?QuizQuestion $question = null): array
    {
        $validated = $request->validate([
            'sort_order' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('quiz_questions', 'sort_order')
                    ->where(fn ($query) => $query->where('quiz_id', $quiz->id))
                    ->ignore($question?->id),
            ],
            'question_text' => ['required', 'string'],
        ]);

        return [
            'quiz_id' => $quiz->id,
            'sort_order' => $validated['sort_order'],
            'question_text' => $validated['question_text'],
            'question_type' => $question?->question_type ?? 'single_choice',
        ];
    }

    /**
     * Guess the next sort number for the quiz.
     */
    protected function nextSortOrder(Quiz $quiz): int
    {
        return ((int) $quiz->questions()->max('sort_order')) + 1;
    }
}
