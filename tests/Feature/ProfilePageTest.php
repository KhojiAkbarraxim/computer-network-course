<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use Database\Seeders\DemoCourseSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfilePageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DemoCourseSeeder::class);
        $this->withoutMiddleware(ValidateCsrfToken::class);
        $this->withoutMiddleware(PreventRequestForgery::class);
    }

    public function test_guests_are_redirected_from_profile_page(): void
    {
        $this->get(route('profile.edit'))
            ->assertRedirect(route('login'));
    }

    public function test_profile_page_shows_real_progress_stats_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $lessons = Lesson::query()
            ->where('is_published', true)
            ->orderBy('id')
            ->take(2)
            ->get();

        foreach ($lessons as $lesson) {
            LessonProgress::query()->create([
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
                'completed_at' => now(),
            ]);
        }

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertOk()
            ->assertSee('Profil')
            ->assertSee($user->name)
            ->assertSee('Tugallangan darslar')
            ->assertSee('Umumiy progress')
            ->assertViewHas('stats', function (array $stats): bool {
                return $stats['completed_lessons'] === 2
                    && $stats['total_lessons'] >= 2
                    && $stats['progress_percentage'] === (int) round((2 / $stats['total_lessons']) * 100);
            });
    }

    public function test_authenticated_user_can_update_profile_information(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => 'Yangi Ism',
                'email' => 'yangi@example.com',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('profile.edit'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Yangi Ism',
            'email' => 'yangi@example.com',
        ]);
    }

    public function test_authenticated_user_can_update_password_from_profile_flow(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put(route('password.update'), [
                'current_password' => 'password',
                'password' => 'YangiParol123!',
                'password_confirmation' => 'YangiParol123!',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertTrue(Hash::check('YangiParol123!', $user->fresh()->password));
    }
}
