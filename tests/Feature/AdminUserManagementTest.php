<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Database\Seeders\DemoCourseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DemoCourseSeeder::class);
    }

    public function test_admin_routes_are_protected_from_guests_and_normal_users(): void
    {
        $managedUser = User::factory()->create();

        $this->get(route('admin.users.index'))
            ->assertRedirect(route('login'));

        $this->actingAs($managedUser)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_users_index_and_detail_pages(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Boshqaruvchi admin',
            'email' => 'admin@example.com',
        ]);
        $managedUser = User::factory()->create([
            'name' => 'Test foydalanuvchi',
            'email' => 'user@example.com',
        ]);
        $lesson = Lesson::query()->firstOrFail();
        $quiz = Quiz::query()->firstOrFail();

        LessonProgress::query()->create([
            'user_id' => $managedUser->id,
            'lesson_id' => $lesson->id,
            'completed_at' => now(),
        ]);
        QuizAttempt::query()->create([
            'user_id' => $managedUser->id,
            'quiz_id' => $quiz->id,
            'score' => 80,
            'total_questions' => 5,
            'correct_answers' => 4,
            'submitted_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSeeText('Foydalanuvchilar')
            ->assertSeeText($managedUser->name)
            ->assertSeeText($managedUser->email)
            ->assertSeeText("O'rtacha quiz natijasi");

        $this->actingAs($admin)
            ->get(route('admin.users.show', $managedUser))
            ->assertOk()
            ->assertSeeText("Foydalanuvchi ma'lumotlari")
            ->assertSeeText($managedUser->name)
            ->assertSeeText($lesson->title)
            ->assertSeeText($quiz->title)
            ->assertSeeText('80%');
    }

    public function test_admin_can_update_normal_user_role_and_profile_fields(): void
    {
        $admin = User::factory()->admin()->create();
        $managedUser = User::factory()->create([
            'name' => 'Eski ism',
            'email' => 'old@example.com',
            'is_admin' => false,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.users.update', $managedUser), [
                'name' => 'Yangi ism',
                'email' => 'new@example.com',
                'is_admin' => '1',
            ])
            ->assertRedirect(route('admin.users.show', $managedUser))
            ->assertSessionHas('status', "Foydalanuvchi ma'lumotlari yangilandi.");

        $this->assertDatabaseHas('users', [
            'id' => $managedUser->id,
            'name' => 'Yangi ism',
            'email' => 'new@example.com',
            'is_admin' => true,
        ]);
    }

    public function test_admin_cannot_remove_their_own_admin_role_or_delete_themselves(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@example.com',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.users.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
            ])
            ->assertRedirect()
            ->assertSessionHas('error', "O'zingizning admin huquqingizni olib tashlay olmaysiz.");

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('error', "O'zingizni o'chira olmaysiz");

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'is_admin' => true,
        ]);
    }

    public function test_admin_cannot_delete_user_with_related_learning_results_but_can_delete_clean_user(): void
    {
        $admin = User::factory()->admin()->create();
        $busyUser = User::factory()->create();
        $cleanUser = User::factory()->create();
        $lesson = Lesson::query()->firstOrFail();
        $quiz = Quiz::query()->firstOrFail();

        LessonProgress::query()->create([
            'user_id' => $busyUser->id,
            'lesson_id' => $lesson->id,
            'completed_at' => now(),
        ]);
        QuizAttempt::query()->create([
            'user_id' => $busyUser->id,
            'quiz_id' => $quiz->id,
            'score' => 60,
            'total_questions' => 5,
            'correct_answers' => 3,
            'submitted_at' => now(),
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $busyUser))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('error', "Bu foydalanuvchini o'chirish mumkin emas, chunki unga bog'langan natijalar mavjud");

        $this->assertDatabaseHas('users', [
            'id' => $busyUser->id,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $cleanUser))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('status', "Foydalanuvchi o'chirildi");

        $this->assertDatabaseMissing('users', [
            'id' => $cleanUser->id,
        ]);
    }
}
