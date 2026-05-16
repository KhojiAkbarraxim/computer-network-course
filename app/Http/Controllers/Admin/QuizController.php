<?php

namespace App\Http\Controllers\Admin;

use App\Models\Quiz;
use App\Models\Lesson;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\QueryException;

class QuizController extends Controller
{
    /**
     * Display the quiz listing.
     */
    public function index(): View
    {
        return view('admin.quizzes.index', [
            'quizzes' => Quiz::query()
                ->with(['lesson.module.course'])
                ->withCount(['questions', 'quizAttempts'])
                ->orderBy('lesson_id')
                ->get(),
        ]);
    }

    /**
     * Show the quiz creation form.
     */
    public function create(): View
    {
        return view('admin.quizzes.create', [
            'quiz' => new Quiz(),
            'lessons' => $this->availableLessons(),
        ]);
    }

    /**
     * Store a newly created quiz.
     */
    public function store(Request $request): RedirectResponse
    {
        $quiz = Quiz::query()->create($this->validatedData($request));

        return redirect()
            ->route('admin.quizzes.questions.index', $quiz)
            ->with('status', "Nazorat muvaffaqiyatli saqlandi.");
    }

    /**
     * Show the quiz edit form.
     */
    public function edit(Quiz $quiz): View
    {
        $quiz->load('lesson.module.course');

        return view('admin.quizzes.edit', [
            'quiz' => $quiz,
            'lessons' => $this->availableLessons($quiz),
        ]);
    }

    /**
     * Update the selected quiz.
     */
    public function update(Request $request, Quiz $quiz): RedirectResponse
    {
        $quiz->update($this->validatedData($request, $quiz));

        return redirect()
            ->route('admin.quizzes.index')
            ->with('status', "Nazorat yangilandi.");
    }

    /**
     * Delete the selected quiz.
     */
    public function destroy(Quiz $quiz): RedirectResponse
    {
        try {
            $quiz->delete();
        } catch (QueryException) {
            return redirect()
                ->route('admin.quizzes.index')
                ->with('error', "Nazoratni o'chirishda xatolik yuz berdi. Bog'liq ma'lumotlarni tekshirib qayta urinib ko'ring.");
        }

        return redirect()
            ->route('admin.quizzes.index')
            ->with('status', "Nazorat o'chirildi.");
    }

    /**
     * Validate and normalize quiz input.
     *
     * @return array<string, mixed>
     */
    protected function validatedData(Request $request, ?Quiz $quiz = null): array
    {
        return $request->validate([
            'lesson_id' => [
                'required',
                'exists:lessons,id',
                Rule::unique('quizzes', 'lesson_id')->ignore($quiz?->id),
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
        ]) + [
            'is_published' => $request->boolean('is_published'),
        ];
    }

    /**
     * Load lessons that can receive a quiz.
     */
    protected function availableLessons(?Quiz $quiz = null)
    {
        return Lesson::query()
            ->with('module.course')
            ->where(function ($query) use ($quiz): void {
                $query->whereDoesntHave('quiz');

                if ($quiz !== null) {
                    $query->orWhere('id', $quiz->lesson_id);
                }
            })
            ->orderBy('module_id')
            ->orderBy('sort_order')
            ->get();
    }
}
