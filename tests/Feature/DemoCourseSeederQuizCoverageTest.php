<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use Database\Seeders\DemoCourseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoCourseSeederQuizCoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_each_lesson_receives_one_quiz_with_three_questions_and_four_answers(): void
    {
        $this->seed(DemoCourseSeeder::class);

        $lessonCount = Lesson::query()->count();

        $this->assertGreaterThan(0, $lessonCount);
        $this->assertSame($lessonCount, Quiz::query()->count());
        $this->assertSame($lessonCount * 3, QuizQuestion::query()->count());
        $this->assertSame($lessonCount * 12, QuizAnswer::query()->count());

        Quiz::query()
            ->with('questions.answers')
            ->orderBy('id')
            ->get()
            ->each(function (Quiz $quiz): void {
                $this->assertCount(3, $quiz->questions);

                $quiz->questions->each(function (QuizQuestion $question): void {
                    $this->assertCount(4, $question->answers);
                    $this->assertSame(1, $question->answers->where('is_correct', true)->count());
                });
            });
    }

    public function test_each_lesson_receives_visual_explanation_data_and_expected_diagram_types(): void
    {
        $this->seed(DemoCourseSeeder::class);

        Lesson::query()
            ->orderBy('id')
            ->get()
            ->each(function (Lesson $lesson): void {
                $this->assertNotNull($lesson->visual_title);
                $this->assertNotNull($lesson->visual_description);
                $this->assertNotNull($lesson->visual_steps);
                $this->assertNotNull($lesson->diagram_type);
                $this->assertIsArray($lesson->visual_steps);
                $this->assertGreaterThanOrEqual(3, count($lesson->visual_steps));
            });

        $this->assertSame(
            'osi',
            Lesson::query()->where('title', '3.1 - OSI modeli haqida')->value('diagram_type')
        );
        $this->assertSame(
            'wifi',
            Lesson::query()->where('title', '8.1 - Simsiz tarmoq asoslari')->value('diagram_type')
        );
        $this->assertSame(
            'security',
            Lesson::query()->where('title', '9.1 - Xavfsizlikning asosiy tamoyillari')->value('diagram_type')
        );

        $osiLesson = Lesson::query()->where('title', '3.1 - OSI modeli haqida')->firstOrFail();
        $wifiLesson = Lesson::query()->where('title', '8.1 - Simsiz tarmoq asoslari')->firstOrFail();
        $securityLesson = Lesson::query()->where('title', '9.1 - Xavfsizlikning asosiy tamoyillari')->firstOrFail();

        $this->get(route('lesson.show', $osiLesson))
            ->assertOk()
            ->assertSeeText('7. Ilova qatlami')
            ->assertSeeText("3.1 - OSI modeli haqida uchun vizual tushuntirish");

        $this->get(route('lesson.show', $wifiLesson))
            ->assertOk()
            ->assertSeeText('Wi-Fi router')
            ->assertSeeText("8.1 - Simsiz tarmoq asoslari uchun vizual tushuntirish");

        $this->get(route('lesson.show', $securityLesson))
            ->assertOk()
            ->assertSeeText('Firewall')
            ->assertSeeText("9.1 - Xavfsizlikning asosiy tamoyillari uchun vizual tushuntirish");
    }
}
