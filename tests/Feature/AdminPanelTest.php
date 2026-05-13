<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;
use Database\Seeders\DemoCourseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DemoCourseSeeder::class);
    }

    public function test_guests_are_redirected_from_admin_area(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));

        $this->get(route('admin.modules.index'))
            ->assertRedirect(route('login'));
    }

    public function test_normal_users_receive_forbidden_response_for_admin_area(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden()
            ->assertSee("admin huquqi kerak");
    }

    public function test_admin_can_open_dashboard_and_see_summary_cards(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Admin panel')
            ->assertSee('Kurslar')
            ->assertSee('Modullar')
            ->assertSee('Darslar');
    }

    public function test_admin_can_create_update_and_delete_module(): void
    {
        $admin = User::factory()->admin()->create();
        $course = Course::query()->orderBy('id')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.modules.store'), [
                'course_id' => $course->id,
                'sort_order' => 99,
                'title' => 'Yangi modul',
                'slug' => 'yangi-modul',
                'short_description' => 'Admin orqali yaratilgan modul.',
                'difficulty_level' => "O'rta",
                'estimated_duration_minutes' => 45,
                'is_published' => '1',
            ])
            ->assertRedirect(route('admin.modules.index'));

        $module = Module::query()->where('slug', 'yangi-modul')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.modules.update', $module), [
                'course_id' => $course->id,
                'sort_order' => 100,
                'title' => 'Yangilangan modul',
                'slug' => 'yangilangan-modul',
                'short_description' => 'Yangilangan modul tavsifi.',
                'difficulty_level' => 'Murakkab',
                'estimated_duration_minutes' => 50,
                'is_published' => '0',
            ])
            ->assertRedirect(route('admin.modules.index'));

        $this->assertDatabaseHas('modules', [
            'id' => $module->id,
            'title' => 'Yangilangan modul',
            'slug' => 'yangilangan-modul',
            'sort_order' => 100,
            'is_published' => false,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.modules.destroy', $module))
            ->assertRedirect(route('admin.modules.index'));

        $this->assertDatabaseMissing('modules', [
            'id' => $module->id,
        ]);
    }

    public function test_admin_can_create_update_and_delete_lesson(): void
    {
        $admin = User::factory()->admin()->create();
        $module = Module::query()->orderBy('id')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.lessons.store'), [
                'module_id' => $module->id,
                'sort_order' => 99,
                'title' => 'Yangi dars',
                'slug' => 'yangi-dars',
                'short_description' => 'Admin orqali yaratilgan dars.',
                'content' => 'Yangi dars matni.',
                'important_note' => 'Muhim admin eslatmasi.',
                'key_terms_text' => "IP manzil | Qurilma manzili\nRouter | Yo'naltiruvchi qurilma",
                'duration_minutes' => 35,
                'is_published' => '1',
            ])
            ->assertRedirect(route('admin.lessons.index'));

        $lesson = Lesson::query()->where('slug', 'yangi-dars')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.lessons.update', $lesson), [
                'module_id' => $module->id,
                'sort_order' => 100,
                'title' => 'Yangilangan dars',
                'slug' => 'yangilangan-dars',
                'short_description' => 'Yangilangan dars tavsifi.',
                'content' => 'Yangilangan dars matni.',
                'important_note' => 'Yangilangan muhim eslatma.',
                'key_terms_text' => "Switch | Trafikni taqsimlaydi",
                'duration_minutes' => 40,
                'is_published' => '0',
            ])
            ->assertRedirect(route('admin.lessons.index'));

        $this->assertDatabaseHas('lessons', [
            'id' => $lesson->id,
            'title' => 'Yangilangan dars',
            'slug' => 'yangilangan-dars',
            'sort_order' => 100,
            'is_published' => false,
        ]);

        $lesson->refresh();
        $this->assertSame('Muhim eslatma', $lesson->important_note_title);
        $this->assertSame('Yangilangan muhim eslatma.', $lesson->important_note_text);
        $this->assertIsArray($lesson->key_terms);

        $this->actingAs($admin)
            ->delete(route('admin.lessons.destroy', $lesson))
            ->assertRedirect(route('admin.lessons.index'));

        $this->assertDatabaseMissing('lessons', [
            'id' => $lesson->id,
        ]);
    }
}
