<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizAttempt;
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

    public function test_admin_user_sees_admin_panel_link_in_main_navbar(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Admin panel');
    }

    public function test_normal_user_and_guest_do_not_see_admin_panel_link_in_main_navbar(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertDontSee('Admin panel');

        auth()->logout();

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('Admin panel');
    }

    public function test_admin_can_open_dashboard_and_see_summary_cards(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create([
            'name' => 'Faol foydalanuvchi',
        ]);
        $quiz = Quiz::query()->firstOrFail();
        $lesson = Lesson::query()->firstOrFail();
        QuizAttempt::query()->create([
            'user_id' => $admin->id,
            'quiz_id' => $quiz->id,
            'score' => 80,
            'total_questions' => 5,
            'correct_answers' => 4,
            'submitted_at' => now(),
        ]);
        QuizAttempt::query()->create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'score' => 100,
            'total_questions' => 5,
            'correct_answers' => 5,
            'submitted_at' => now()->subMinute(),
        ]);
        LessonProgress::query()->create([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'completed_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSeeText('Admin panel')
            ->assertSeeText('Jami foydalanuvchilar')
            ->assertSeeText('Admin foydalanuvchilar')
            ->assertSeeText('Oddiy foydalanuvchilar')
            ->assertSeeText('Jami kurslar')
            ->assertSeeText('Jami modullar')
            ->assertSeeText('Jami darslar')
            ->assertSeeText('Tugatilgan darslar soni')
            ->assertSeeText('Jami nazoratlar')
            ->assertSeeText('Jami savollar')
            ->assertSeeText('Jami javoblar')
            ->assertSeeText('Jami foydalanuvchilar')
            ->assertSeeText('Jami quiz urinishlari')
            ->assertSeeText("O'rtacha quiz natijasi")
            ->assertSeeText('Eng yuqori quiz natijasi')
            ->assertSeeText("Modul qo'shish")
            ->assertSeeText("Dars qo'shish")
            ->assertSeeText("Nazorat qo'shish")
            ->assertSeeText("Foydalanuvchilarni boshqarish")
            ->assertSeeText('Oxirgi quiz urinishlari')
            ->assertSeeText("Oxirgi ro'yxatdan o'tgan foydalanuvchilar")
            ->assertSeeText("Oxirgi tugatilgan darslar")
            ->assertSeeText("Eng faol foydalanuvchilar")
            ->assertSeeText($quiz->title)
            ->assertSeeText($admin->name)
            ->assertSeeText($user->name)
            ->assertSeeText($lesson->title)
            ->assertSeeText('90%')
            ->assertSeeText('100%');
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
                'visual_title' => 'Vizual blok',
                'visual_description' => 'Vizual tavsif matni.',
                'diagram_type' => 'basic-network',
                'visual_steps_text' => "Birinchi qadam\nIkkinchi qadam",
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
                'visual_title' => 'Yangilangan vizual blok',
                'visual_description' => 'Yangilangan vizual tavsif.',
                'diagram_type' => 'osi',
                'visual_steps_text' => "Birinchi bosqich\nIkkinchi bosqich\nUchinchi bosqich",
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
        $this->assertSame('Yangilangan vizual blok', $lesson->visual_title);
        $this->assertSame('Yangilangan vizual tavsif.', $lesson->visual_description);
        $this->assertSame('osi', $lesson->diagram_type);
        $this->assertSame(['Birinchi bosqich', 'Ikkinchi bosqich', 'Uchinchi bosqich'], $lesson->visual_steps);

        $this->actingAs($admin)
            ->delete(route('admin.lessons.destroy', $lesson))
            ->assertRedirect(route('admin.lessons.index'));

        $this->assertDatabaseMissing('lessons', [
            'id' => $lesson->id,
        ]);
    }
}
