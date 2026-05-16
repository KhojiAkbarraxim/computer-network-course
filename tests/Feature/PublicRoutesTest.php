<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\User;
use Database\Seeders\DemoCourseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DemoCourseSeeder::class);
    }

    public function test_public_pages_render_with_existing_project_views(): void
    {
        $lesson = Lesson::query()
            ->where('is_published', true)
            ->orderBy('id')
            ->firstOrFail();

        $quiz = Quiz::query()
            ->where('is_published', true)
            ->orderBy('id')
            ->firstOrFail();

        $this->get('/')
            ->assertOk()
            ->assertViewIs('home');

        $this->get('/course')->assertOk();
        $this->get('/lesson/sample')->assertOk();
        $this->get("/lesson/{$lesson->id}")->assertOk();
        $this->get('/quizzes')
            ->assertOk()
            ->assertSeeText('Mavjud nazoratlar ro\'yxati')
            ->assertSeeText($quiz->title)
            ->assertSeeText('Nazoratni boshlash');
        $this->get('/quiz/sample')
            ->assertRedirect(route('quiz.show', $quiz));
        $this->get("/quiz/{$quiz->id}")->assertOk();
        $this->get('/about')->assertOk();
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
    }

    public function test_quiz_listing_and_sample_route_show_empty_state_when_no_quiz_exists(): void
    {
        Quiz::query()->delete();

        $this->get('/quizzes')
            ->assertOk()
            ->assertSeeText('Hozircha nazoratlar mavjud emas.');

        $this->get('/quiz/sample')
            ->assertOk()
            ->assertSeeText('Hozircha nazoratlar mavjud emas.');
    }

    public function test_dashboard_redirects_guests_to_login(): void
    {
        $this->get('/dashboard')
            ->assertRedirect(route('login', absolute: false));
    }

    public function test_dashboard_renders_for_authenticated_users(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertViewIs('dashboard');
    }
}
