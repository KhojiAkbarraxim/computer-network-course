<?php

namespace Tests\Feature;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use App\Models\User;
use Database\Seeders\DemoCourseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizAttemptTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DemoCourseSeeder::class);
    }

    public function test_guests_see_login_message_and_can_not_submit_quiz(): void
    {
        $quiz = $this->publishedQuiz();

        $this->get(route('quiz.sample'))
            ->assertRedirect(route('quiz.show', $quiz));

        $this->get(route('quiz.show', $quiz))
            ->assertOk()
            ->assertSee("Natijani saqlash uchun tizimga kiring.");

        $this->post(route('quiz.submit', $quiz->id), [
            'answers' => $this->correctAnswersPayload($quiz),
        ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('status', "Natijani saqlash uchun tizimga kiring.");

        $this->assertDatabaseCount('quiz_attempts', 0);
        $this->assertDatabaseCount('quiz_attempt_answers', 0);
    }

    public function test_authenticated_user_can_submit_quiz_and_save_attempt_rows(): void
    {
        $user = User::factory()->create();
        $quiz = $this->publishedQuiz();

        $this->actingAs($user)
            ->post(route('quiz.submit', $quiz->id), [
                'answers' => $this->correctAnswersPayload($quiz),
            ])
            ->assertRedirect(route('quiz.show', $quiz->id).'#natija');

        $this->assertDatabaseHas('quiz_attempts', [
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'score' => 100,
            'total_questions' => $quiz->questions->count(),
            'correct_answers' => $quiz->questions->count(),
        ]);

        $attempt = QuizAttempt::query()
            ->where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->firstOrFail();

        $this->assertSame($quiz->questions->count(), QuizAttemptAnswer::query()->where('quiz_attempt_id', $attempt->id)->count());

        $this->actingAs($user)
            ->get(route('quiz.show', $quiz->id))
            ->assertOk()
            ->assertSeeText("To'g'ri javoblar")
            ->assertSeeText("Noto'g'ri javoblar")
            ->assertSeeText('Natija foizi')
            ->assertSeeText('100%')
            ->assertSeeText('Siz tanlagan javob');
    }

    public function test_dashboard_and_profile_show_quiz_statistics(): void
    {
        $user = User::factory()->create();
        $quiz = $this->publishedQuiz();

        $this->actingAs($user)->post(route('quiz.submit', $quiz->id), [
            'answers' => $this->correctAnswersPayload($quiz),
        ]);

        $this->actingAs($user)->post(route('quiz.submit', $quiz->id), [
            'answers' => $this->wrongAnswersPayload($quiz),
        ]);

        $this->actingAs($user)
            ->get(route('quiz.show', $quiz->id))
            ->assertOk()
            ->assertSeeText("Qayta urinish")
            ->assertSeeText("Oldingi natijalar")
            ->assertSeeText("Oxirgi urinish")
            ->assertSeeText("To'g'ri javob")
            ->assertSeeText("Noto'g'ri")
            ->assertViewHas('quiz', function (array $quizView): bool {
                return ($quizView['latest_attempt']['wrong_answers'] ?? null) !== null
                    && count($quizView['attempt_history'] ?? []) === 2
                    && ($quizView['has_previous_attempts'] ?? false) === true;
            });

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('dashboard', function (array $dashboard) use ($quiz): bool {
                return $dashboard['quiz_summary']['latest_title'] === $quiz->title
                    && $dashboard['quiz_summary']['total_attempts'] === 2
                    && $dashboard['quiz_summary']['average_score'] === 50;
            });

        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertViewHas('stats', function (array $stats): bool {
                return $stats['total_quiz_attempts'] === 2
                    && $stats['best_quiz_score'] === 100
                    && $stats['average_quiz_score'] === 50;
            });
    }

    public function test_quiz_page_shows_empty_state_when_no_questions_exist(): void
    {
        $quiz = $this->publishedQuiz();

        $quiz->questions()->delete();

        $this->get(route('quiz.show', $quiz->id))
            ->assertOk()
            ->assertSee('Hozircha savollar mavjud emas.');
    }

    /**
     * Get the first published quiz with loaded answers.
     */
    protected function publishedQuiz(): Quiz
    {
        return Quiz::query()
            ->where('is_published', true)
            ->with('questions.answers')
            ->orderBy('id')
            ->firstOrFail();
    }

    /**
     * Build a payload where every selected answer is correct.
     *
     * @return array<int, int>
     */
    protected function correctAnswersPayload(Quiz $quiz): array
    {
        return $quiz->questions->mapWithKeys(fn ($question) => [
            $question->id => $question->answers->firstWhere('is_correct', true)?->id,
        ])->all();
    }

    /**
     * Build a payload where every selected answer is incorrect when possible.
     *
     * @return array<int, int>
     */
    protected function wrongAnswersPayload(Quiz $quiz): array
    {
        return $quiz->questions->mapWithKeys(function ($question): array {
            $wrongAnswer = $question->answers->firstWhere('is_correct', false);
            $fallbackAnswer = $question->answers->first();

            return [
                $question->id => ($wrongAnswer ?? $fallbackAnswer)?->id,
            ];
        })->all();
    }
}
