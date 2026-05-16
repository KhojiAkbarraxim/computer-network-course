<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display the user management table.
     */
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::query()
                ->withCount([
                    'lessonProgress as completed_lessons_count',
                    'quizAttempts as quiz_attempts_count',
                ])
                ->withAvg('quizAttempts as average_quiz_score', 'score')
                ->latest()
                ->get(),
        ]);
    }

    /**
     * Display the selected user details.
     */
    public function show(User $user): View
    {
        $user->loadCount([
            'lessonProgress as completed_lessons_count',
            'quizAttempts as quiz_attempts_count',
        ]);

        return view('admin.users.show', [
            'user' => $user,
            'bestQuizScore' => $user->quizAttempts()->max('score'),
            'averageQuizScore' => $user->quizAttempts()->avg('score'),
            'recentCompletedLessons' => $user->lessonProgress()
                ->with('lesson')
                ->orderByDesc('completed_at')
                ->take(5)
                ->get(),
            'recentAttempts' => $user->quizAttempts()
                ->with('quiz.lesson')
                ->orderByDesc('submitted_at')
                ->take(5)
                ->get(),
        ]);
    }

    /**
     * Show the edit form for the selected user.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'isCurrentAdmin' => (int) request()->user()?->id === (int) $user->id,
        ]);
    }

    /**
     * Update the selected user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $this->validatedData($request, $user);
        $isCurrentAdmin = (int) $request->user()?->id === (int) $user->id;
        $shouldBeAdmin = $request->boolean('is_admin');

        if ($isCurrentAdmin && ! $shouldBeAdmin) {
            return back()
                ->withInput()
                ->with('error', "O'zingizning admin huquqingizni olib tashlay olmaysiz.");
        }

        $user->update($validated + [
            'is_admin' => $isCurrentAdmin ? true : $shouldBeAdmin,
        ]);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('status', "Foydalanuvchi ma'lumotlari yangilandi.");
    }

    /**
     * Delete the selected user when it is safe to do so.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ((int) $request->user()?->id === (int) $user->id) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', "O'zingizni o'chira olmaysiz");
        }

        if ($user->lessonProgress()->exists() || $user->quizAttempts()->exists()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', "Bu foydalanuvchini o'chirish mumkin emas, chunki unga bog'langan natijalar mavjud");
        }

        try {
            $user->delete();
        } catch (QueryException) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', "Bu foydalanuvchini o'chirish mumkin emas, chunki unga bog'langan natijalar mavjud");
        }

        return redirect()
            ->route('admin.users.index')
            ->with('status', "Foydalanuvchi o'chirildi");
    }

    /**
     * Validate user update input.
     *
     * @return array<string, mixed>
     */
    protected function validatedData(Request $request, User $user): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'is_admin' => ['nullable', 'boolean'],
        ]);
    }
}
