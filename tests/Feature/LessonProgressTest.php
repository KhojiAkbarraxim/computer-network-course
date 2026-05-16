<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use Database\Seeders\DemoCourseSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonProgressTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DemoCourseSeeder::class);
        $this->withoutMiddleware(ValidateCsrfToken::class);
        $this->withoutMiddleware(PreventRequestForgery::class);
    }

    public function test_guests_can_not_mark_a_lesson_as_completed(): void
    {
        $lesson = Lesson::query()->where('is_published', true)->orderBy('id')->firstOrFail();

        $this->from(route('lesson.show', $lesson->id))
            ->post(route('lesson.complete', $lesson->id))
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('lesson_progress', 0);
    }

    public function test_authenticated_users_can_complete_a_lesson_without_duplicates(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::query()->where('is_published', true)->orderBy('id')->firstOrFail();

        $this->actingAs($user)
            ->from(route('lesson.show', $lesson->id))
            ->post(route('lesson.complete', $lesson->id))
            ->assertRedirect(route('lesson.show', $lesson->id));

        $this->actingAs($user)
            ->from(route('lesson.show', $lesson->id))
            ->post(route('lesson.complete', $lesson->id))
            ->assertRedirect(route('lesson.show', $lesson->id));

        $this->assertDatabaseCount('lesson_progress', 1);
        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);
    }

    public function test_lesson_page_shows_completion_state_for_guests_and_users(): void
    {
        $lesson = Lesson::query()->where('is_published', true)->orderBy('id')->firstOrFail();
        $user = User::factory()->create();

        $this->get(route('lesson.show', $lesson->id))
            ->assertOk()
            ->assertSee('Progressni saqlash uchun tizimga kiring');

        $this->actingAs($user)
            ->get(route('lesson.show', $lesson->id))
            ->assertOk()
            ->assertSee('Darsni tugatdim');

        LessonProgress::query()->create([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'completed_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('lesson.show', $lesson->id))
            ->assertOk()
            ->assertSee('Bu dars tugatilgan');
    }

    public function test_seeded_lesson_shows_visual_explanation_content_and_fallback_text_for_missing_visuals(): void
    {
        $lessonWithVisual = Lesson::query()
            ->where('title', 'like', "%Kompyuter tarmog'i nima?%")
            ->firstOrFail();
        $lessonWithoutVisual = Lesson::query()
            ->whereNull('visual_title')
            ->whereNull('visual_description')
            ->whereNull('visual_steps')
            ->whereNull('diagram_type')
            ->firstOrFail();

        $this->get(route('lesson.show', $lessonWithVisual))
            ->assertOk()
            ->assertSeeText('Vizual tushuntirish')
            ->assertSeeText("Bosqichma-bosqich ko'rish")
            ->assertSeeText("Diagramma ko'rinishi")
            ->assertSeeText('Kompyuter')
            ->assertSeeText('Switch')
            ->assertSeeText('Router')
            ->assertSeeText('Internet');

        $this->get(route('lesson.show', $lessonWithoutVisual))
            ->assertOk()
            ->assertSeeText("Bu dars uchun vizual tushuntirish hali qo'shilmagan.");
    }

    public function test_dashboard_uses_real_progress_counts_and_continue_lesson(): void
    {
        $user = User::factory()->create();
        $lessons = Lesson::query()
            ->where('is_published', true)
            ->orderBy('id')
            ->take(3)
            ->get();

        LessonProgress::query()->create([
            'user_id' => $user->id,
            'lesson_id' => $lessons[0]->id,
            'completed_at' => now()->subDay(),
        ]);

        LessonProgress::query()->create([
            'user_id' => $user->id,
            'lesson_id' => $lessons[1]->id,
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk()->assertViewHas('dashboard', function (array $dashboard) use ($lessons): bool {
            return $dashboard['overview'][0]['value'] !== '0 ta'
                && $dashboard['overview'][1]['value'] === '2 ta'
                && $dashboard['continue']['lesson'] === $lessons[2]->title
                && count($dashboard['completed_lessons']) === 2;
        });
    }
}
