<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\User;
use Database\Seeders\DemoCourseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminQuizCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DemoCourseSeeder::class);
    }

    public function test_guests_and_normal_users_cannot_access_admin_quiz_management_pages(): void
    {
        $quiz = Quiz::query()->firstOrFail();
        $question = $quiz->questions()->firstOrFail();

        $this->get(route('admin.quizzes.index'))
            ->assertRedirect(route('login'));

        $this->get(route('admin.quizzes.questions.index', $quiz))
            ->assertRedirect(route('login'));

        $this->get(route('admin.questions.answers.index', $question))
            ->assertRedirect(route('login'));

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.quizzes.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('admin.quizzes.questions.index', $quiz))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('admin.questions.answers.index', $question))
            ->assertForbidden();
    }

    public function test_admin_can_create_update_and_delete_quiz_question_and_answer(): void
    {
        $admin = User::factory()->admin()->create();
        $module = Module::query()->orderBy('id')->firstOrFail();
        $lesson = Lesson::query()->create([
            'module_id' => $module->id,
            'sort_order' => 999,
            'title' => 'Admin uchun yangi dars',
            'slug' => 'admin-uchun-yangi-dars',
            'short_description' => 'Quiz yaratish testi uchun dars.',
            'content' => 'Quiz yaratish testi uchun dars matni.',
            'duration_minutes' => 10,
            'is_published' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.quizzes.index'))
            ->assertOk()
            ->assertSeeText('Nazoratlar');

        $this->actingAs($admin)
            ->get(route('admin.quizzes.create'))
            ->assertOk()
            ->assertSeeText("Yangi nazorat qo'shish");

        $this->actingAs($admin)
            ->post(route('admin.quizzes.store'), [
                'lesson_id' => $lesson->id,
                'title' => 'Yangi nazorat',
                'description' => 'Admin tomonidan yaratilgan nazorat.',
                'is_published' => '1',
            ])
            ->assertRedirect();

        $quiz = Quiz::query()->where('lesson_id', $lesson->id)->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.quizzes.questions.index', $quiz))
            ->assertOk()
            ->assertSeeText('Savollar');

        $this->actingAs($admin)
            ->get(route('admin.quizzes.edit', $quiz))
            ->assertOk()
            ->assertSeeText("Nazoratni tahrirlash");

        $this->actingAs($admin)
            ->put(route('admin.quizzes.update', $quiz), [
                'lesson_id' => $lesson->id,
                'title' => 'Yangilangan nazorat',
                'description' => 'Yangilangan nazorat tavsifi.',
                'is_published' => '0',
            ])
            ->assertRedirect(route('admin.quizzes.index'));

        $this->assertDatabaseHas('quizzes', [
            'id' => $quiz->id,
            'title' => 'Yangilangan nazorat',
            'is_published' => false,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.quizzes.questions.store', $quiz), [
                'sort_order' => 1,
                'question_text' => 'Tarmoq qurilmasining asosiy vazifasi nima?',
            ])
            ->assertRedirect(route('admin.quizzes.questions.index', $quiz));

        $question = QuizQuestion::query()->where('quiz_id', $quiz->id)->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.questions.update', $question), [
                'sort_order' => 2,
                'question_text' => 'Yangilangan savol matni',
            ])
            ->assertRedirect(route('admin.quizzes.questions.index', $quiz));

        $this->assertDatabaseHas('quiz_questions', [
            'id' => $question->id,
            'quiz_id' => $quiz->id,
            'sort_order' => 2,
            'question_text' => 'Yangilangan savol matni',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.questions.answers.store', $question), [
                'sort_order' => 1,
                'answer_text' => "Birinchi to'g'ri javob",
                'is_correct' => '1',
            ])
            ->assertRedirect(route('admin.questions.answers.index', $question));

        $this->actingAs($admin)
            ->post(route('admin.questions.answers.store', $question), [
                'sort_order' => 2,
                'answer_text' => "Ikkinchi noto'g'ri javob",
            ])
            ->assertRedirect(route('admin.questions.answers.index', $question));

        $question->refresh();
        $firstAnswer = $question->answers()->where('sort_order', 1)->firstOrFail();
        $secondAnswer = $question->answers()->where('sort_order', 2)->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.questions.answers.index', $question))
            ->assertOk()
            ->assertSeeText('Javoblar')
            ->assertSeeText("To'g'ri javob");

        $this->actingAs($admin)
            ->put(route('admin.answers.update', $secondAnswer), [
                'sort_order' => 2,
                'answer_text' => "Yangilangan to'g'ri javob",
                'is_correct' => '1',
            ])
            ->assertRedirect(route('admin.questions.answers.index', $question));

        $this->assertDatabaseHas('quiz_answers', [
            'id' => $secondAnswer->id,
            'answer_text' => "Yangilangan to'g'ri javob",
            'is_correct' => true,
        ]);

        $this->assertDatabaseHas('quiz_answers', [
            'id' => $firstAnswer->id,
            'is_correct' => false,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.answers.destroy', $firstAnswer))
            ->assertRedirect(route('admin.questions.answers.index', $question));

        $this->assertDatabaseMissing('quiz_answers', [
            'id' => $firstAnswer->id,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.questions.destroy', $question))
            ->assertRedirect(route('admin.quizzes.questions.index', $quiz));

        $this->assertDatabaseMissing('quiz_questions', [
            'id' => $question->id,
        ]);

        $this->assertDatabaseMissing('quiz_answers', [
            'id' => $secondAnswer->id,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.quizzes.destroy', $quiz))
            ->assertRedirect(route('admin.quizzes.index'));

        $this->assertDatabaseMissing('quizzes', [
            'id' => $quiz->id,
        ]);
    }

    public function test_admin_must_keep_at_least_one_correct_answer_for_each_question(): void
    {
        $admin = User::factory()->admin()->create();
        $question = QuizQuestion::query()->with('answers')->orderBy('id')->firstOrFail();
        $correctAnswer = $question->answers->firstWhere('is_correct', true);

        $this->assertNotNull($correctAnswer);

        $this->actingAs($admin)
            ->put(route('admin.answers.update', $correctAnswer), [
                'sort_order' => $correctAnswer->sort_order,
                'answer_text' => $correctAnswer->answer_text,
            ])
            ->assertSessionHasErrors('is_correct');

        $this->actingAs($admin)
            ->delete(route('admin.answers.destroy', $correctAnswer))
            ->assertRedirect(route('admin.questions.answers.index', $question))
            ->assertSessionHas('error', "Savolda kamida bitta to'g'ri javob qolishi kerak.");

        $this->assertDatabaseHas('quiz_answers', [
            'id' => $correctAnswer->id,
        ]);
    }
}
