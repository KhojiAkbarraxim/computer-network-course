<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class DemoCourseSeeder extends Seeder
{
    /**
     * Seed the application's database with demo course data.
     */
    public function run(): void
    {
        $demoCourse = config('demo-course');

        $course = Course::query()->updateOrCreate(
            ['slug' => 'kompyuter-tarmoqlarini-organish'],
            [
                'title' => "Kompyuter tarmoqlarini o'rganish",
                'short_description' => "Kompyuter tarmoqlari asoslarini soddalashtirilgan va ketma-ket o'quv yo'li orqali o'rganishga mo'ljallangan kurs.",
                'description' => Arr::get($demoCourse, 'about.purpose'),
                'level_label' => "Boshlang'ichdan amaliy bosqichgacha",
                'estimated_duration_minutes' => $this->sumModuleDurations(Arr::get($demoCourse, 'modules', [])),
                'is_published' => true,
            ],
        );

        $lessonBlueprints = $this->lessonBlueprints($demoCourse);
        $quizLesson = null;

        foreach (Arr::get($demoCourse, 'modules', []) as $moduleIndex => $moduleData) {
            $moduleSortOrder = (int) $moduleData['sort_order'];

            $module = Module::query()->updateOrCreate(
                [
                    'course_id' => $course->id,
                    'sort_order' => $moduleSortOrder,
                ],
                [
                    'title' => $moduleData['title'],
                    'slug' => Str::slug($moduleData['title']),
                    'short_description' => $moduleData['description'],
                    'difficulty_level' => $moduleData['difficulty'],
                    'estimated_duration_minutes' => $this->parseDurationToMinutes($moduleData['duration']),
                    'is_published' => true,
                ],
            );

            foreach ($lessonBlueprints[$moduleSortOrder] ?? [] as $lessonIndex => $lessonData) {
                $lesson = Lesson::query()->updateOrCreate(
                    [
                        'module_id' => $module->id,
                        'sort_order' => $lessonIndex + 1,
                    ],
                    [
                        'title' => $lessonData['title'],
                        'slug' => Str::slug($lessonData['title']),
                        'short_description' => $lessonData['short_description'],
                        'content' => $lessonData['content'],
                        'important_note_title' => $lessonData['important_note_title'] ?? 'Muhim eslatma',
                        'important_note_text' => $lessonData['important_note_text'] ?? null,
                        'key_terms' => $lessonData['key_terms'] ?? [],
                        'duration_minutes' => $lessonData['duration_minutes'],
                        'is_published' => true,
                    ],
                );

                if ($moduleSortOrder === 1 && ($lessonIndex + 1) === 1) {
                    $quizLesson = $lesson;
                }
            }
        }

        if ($quizLesson !== null) {
            $this->seedQuizForLesson($quizLesson, $this->quizData());
        }
    }

    /**
     * Build lesson blueprints for each module.
     *
     * @param array<string, mixed> $demoCourse
     * @return array<string, array<int, array<string, mixed>>>
     */
    protected function lessonBlueprints(array $demoCourse): array
    {
        $lesson = Arr::get($demoCourse, 'lesson', []);
        $lessonParagraphs = Arr::get($lesson, 'paragraphs', []);

        return [
            1 => $this->buildLessons(
                'Kompyuter tarmoqlariga kirish',
                [
                    "1.1 - Kompyuter tarmog'i nima?",
                    "1.2 - Tarmoq nima uchun kerak?",
                    "1.3 - Mijoz va server tushunchasi",
                    "1.4 - Topologiya va ulanish ko'rinishlari",
                    "1.5 - Kirish moduli nazorati",
                ],
                Arr::get($demoCourse, 'modules.0.description'),
            ),
            2 => $this->buildLessons(
                'Tarmoq turlari',
                [
                    "2.1 - LAN tarmog'i",
                    "2.2 - MAN tarmog'i",
                    "2.3 - WAN tarmog'i",
                    "2.4 - Tarmoq turini tanlash mezonlari",
                ],
                Arr::get($demoCourse, 'modules.1.description'),
            ),
            3 => [
                [
                    'title' => "3.1 - OSI modeli haqida",
                    'short_description' => "OSI modeli nima uchun yaratilgani va tarmoqni tushunishda qanday yordam berishini izohlaydi.",
                    'content' => "OSI modeli tarmoq aloqasini qatlamlar bo'yicha ko'rish imkonini beradi. Bu yondashuv orqali har bir vazifa alohida bo'lib ko'rinadi va muammoni topish osonlashadi.\n\nModel konseptual bo'lsa ham, real tarmoqlarni tahlil qilishda juda qulay fikrlash usuli sifatida ishlatiladi.",
                    'important_note_title' => "Muhim eslatma",
                    'important_note_text' => "OSI modeli amaliy internet protokollarining to'liq nusxasi emas, balki ularni tushunishga yordam beradigan qatlamli yondashuvdir.",
                    'key_terms' => [
                        ['term' => 'OSI modeli', 'definition' => "Tarmoq aloqasini yetti qatlam ko'rinishida tahlil qilish modeli."],
                        ['term' => 'Qatlam', 'definition' => "Ma'lum vazifalarni bajaradigan mantiqiy bosqich."],
                    ],
                    'duration_minutes' => 7,
                ],
                [
                    'title' => "3.2 - 7 qatlam tushunchasi",
                    'short_description' => "OSI modelidagi yetti qatlam nima uchun ajratilganini sodda tilda tushuntiradi.",
                    'content' => implode("\n\n", $lessonParagraphs),
                    'important_note_title' => Arr::get($lesson, 'note.title', 'Muhim eslatma'),
                    'important_note_text' => Arr::get($lesson, 'note.text'),
                    'key_terms' => Arr::get($lesson, 'key_terms', []),
                    'duration_minutes' => 12,
                ],
                [
                    'title' => "3.3 - Har bir qatlam vazifasi",
                    'short_description' => "Har bir qatlam aynan qaysi rolni bajarishini misollar bilan ko'rsatadi.",
                    'content' => "Ilova qatlami foydalanuvchi dasturlariga yaqin ishlaydi, transport qatlami esa ishonchli yetkazishni nazorat qiladi.\n\nTarmoq va kanal qatlamlari manzillash, freym va mahalliy uzatish bilan shug'ullanadi. Jismoniy qatlam signalning real uzatilishini bajaradi.",
                    'important_note_title' => "Muhim eslatma",
                    'important_note_text' => "Qatlamlarni yodlashdan ko'ra, ular qanday muammoni hal qilishini tushunish foydaliroq.",
                    'key_terms' => [
                        ['term' => 'Ilova qatlami', 'definition' => "Foydalanuvchi xizmatlari bilan bog'liq qatlam."],
                        ['term' => 'Transport qatlami', 'definition' => "Ma'lumotning ishonchli yetib borishini boshqaradi."],
                        ['term' => 'Jismoniy qatlam', 'definition' => "Signal va uzatish muhiti bilan ishlaydi."],
                    ],
                    'duration_minutes' => 10,
                ],
                [
                    'title' => "3.4 - Qurilmalar va qatlamlar",
                    'short_description' => "Switch, router va boshqa qurilmalar qaysi qatlamga yaqin ishlashini izohlaydi.",
                    'content' => "Har bir tarmoq qurilmasi ma'lum qatlamdagi vazifaga ko'proq mos keladi. Bu farqni bilish amaliy tahlilda foyda beradi.",
                    'important_note_title' => "Muhim eslatma",
                    'important_note_text' => "Bir qurilma ko'pincha bitta qatlam bilan cheklanmaydi, lekin asosiy vazifasi orqali ko'proq taniladi.",
                    'key_terms' => [
                        ['term' => 'Switch', 'definition' => "Ko'pincha kanal qatlamida ishlaydigan tarmoq qurilmasi."],
                        ['term' => 'Router', 'definition' => "Tarmoq qatlamida marshrutlashni bajaradigan qurilma."],
                    ],
                    'duration_minutes' => 9,
                ],
                [
                    'title' => "3.5 - Protokollar misoli",
                    'short_description' => "OSI qatlamlari bilan bog'liq mashhur protokollarni guruhlab ko'rsatadi.",
                    'content' => "HTTP, TCP, IP va Ethernet kabi protokollarni qatlamlar bo'yicha joylashtirish modelni yodda saqlashni osonlashtiradi.",
                    'important_note_title' => "Muhim eslatma",
                    'important_note_text' => "Bir protokolni to'g'ri qatlamga joylashtirish tarmoq mantiqini ancha tez anglashga yordam beradi.",
                    'key_terms' => [
                        ['term' => 'HTTP', 'definition' => "Ilova qatlamiga yaqin ishlaydigan veb protokoli."],
                        ['term' => 'IP', 'definition' => "Manzillash va marshrutlashga xizmat qiladigan protokol."],
                    ],
                    'duration_minutes' => 8,
                ],
                [
                    'title' => "3.6 - Mini test",
                    'short_description' => "OSI modeli bo'yicha o'zlashtirishni tezkor savollar bilan tekshiradi.",
                    'content' => "Qisqa nazorat savollari orqali qatlamlar, qamrash va qurilmalar o'rtasidagi bog'lanishni mustahkamlaysiz.",
                    'important_note_title' => "Muhim eslatma",
                    'important_note_text' => "Nazorat oldidan qatlamlar ketma-ketligi va ularning roli yana bir bor takrorlab chiqilsa, natija yaxshilanadi.",
                    'key_terms' => [
                        ['term' => 'Nazorat', 'definition' => "O'zlashtirish darajasini tekshiruvchi qisqa savollar to'plami."],
                    ],
                    'duration_minutes' => 6,
                ],
            ],
            4 => $this->buildLessons(
                'TCP/IP modeli',
                [
                    "4.1 - TCP/IP modeliga kirish",
                    "4.2 - Qatlamlar va protokollar",
                    "4.3 - Paketlar oqimi",
                    "4.4 - TCP va IP vazifalari",
                    "4.5 - TCP/IP mini test",
                ],
                Arr::get($demoCourse, 'modules.3.description'),
            ),
            5 => $this->buildLessons(
                'IP manzillar va subnetting',
                [
                    "5.1 - IPv4 manzil tuzilishi",
                    "5.2 - Tarmoq va host qismi",
                    "5.3 - Subnet maska mantig'i",
                    "5.4 - CIDR yozuvi",
                    "5.5 - Oddiy subnet hisoblash",
                    "5.6 - Manzillash xatolarini topish",
                    "5.7 - Amaliy mashqlar",
                ],
                Arr::get($demoCourse, 'modules.4.description'),
            ),
            6 => $this->buildLessons(
                'Router, switch va hub',
                [
                    "6.1 - Hub qanday ishlaydi?",
                    "6.2 - Switchning vazifasi",
                    "6.3 - Router nima qiladi?",
                    "6.4 - Qurilmalarni taqqoslash",
                    "6.5 - Kichik topologiya misoli",
                ],
                Arr::get($demoCourse, 'modules.5.description'),
            ),
            7 => $this->buildLessons(
                'DNS, DHCP va NAT',
                [
                    "7.1 - DNS xizmatining roli",
                    "7.2 - DHCP orqali IP taqsimlash",
                    "7.3 - NAT nima uchun kerak?",
                    "7.4 - Xizmatlar birgalikda qanday ishlaydi?",
                    "7.5 - Oddiy sozlash ssenariysi",
                    "7.6 - Nazorat savollari",
                ],
                Arr::get($demoCourse, 'modules.6.description'),
            ),
            8 => $this->buildLessons(
                'Wi-Fi va simsiz tarmoqlar',
                [
                    "8.1 - Simsiz tarmoq asoslari",
                    "8.2 - Standartlar va chastotalar",
                    "8.3 - Qamrov va signal sifati",
                    "8.4 - Wi-Fi xavfsizligi",
                ],
                Arr::get($demoCourse, 'modules.7.description'),
            ),
            9 => $this->buildLessons(
                'Tarmoq xavfsizligi',
                [
                    "9.1 - Xavfsizlikning asosiy tamoyillari",
                    "9.2 - Kuchli parol siyosati",
                    "9.3 - Segmentatsiya va ajratish",
                    "9.4 - Firewall vazifasi",
                    "9.5 - Oddiy tahdid ssenariylari",
                    "9.6 - Himoya choralarini mustahkamlash",
                ],
                Arr::get($demoCourse, 'modules.8.description'),
            ),
            10 => $this->buildLessons(
                'Amaliy laboratoriya ishlari',
                [
                    "10.1 - Kichik ofis tarmog'i",
                    "10.2 - Kabel va ulanishlarni tekshirish",
                    "10.3 - IP rejalashtirish mashqi",
                    "10.4 - Switch portlarini tahlil qilish",
                    "10.5 - Router yo'naltirish tekshiruvi",
                    "10.6 - DNS muammosini aniqlash",
                    "10.7 - DHCP xizmatini kuzatish",
                    "10.8 - Wi-Fi qamrovini baholash",
                    "10.9 - Xavfsizlik nazorat ro'yxati",
                    "10.10 - Yakuniy laboratoriya mashqi",
                ],
                Arr::get($demoCourse, 'modules.9.description'),
            ),
        ];
    }

    /**
     * Create simple lesson records from titles.
     *
     * @param list<string> $titles
     * @return array<int, array<string, mixed>>
     */
    protected function buildLessons(string $moduleTitle, array $titles, ?string $moduleDescription = null): array
    {
        $lessons = [];

        foreach ($titles as $index => $title) {
            $lessons[] = [
                'title' => $title,
                'short_description' => $moduleDescription ?: "{$moduleTitle} bo'yicha asosiy mavzularni tushuntiradi.",
                'content' => "{$moduleTitle} modulidagi {$title} darsida mavzuning amaliy ahamiyati, asosiy atamalari va real tarmoqdagi qo'llanishi qisqa va tushunarli usulda yoritiladi.\n\nDars davomida asosiy tushuncha, oddiy misol va amaliy qarash birgalikda beriladi, shuning uchun boshlovchi foydalanuvchi mavzuni bosqichma-bosqich o'zlashtira oladi.",
                'important_note_title' => "Muhim eslatma",
                'important_note_text' => "{$title} mavzusini mustahkamlash uchun darsdagi asosiy atamalarni qayta ko'rib chiqish tavsiya etiladi.",
                'key_terms' => $this->defaultKeyTerms($moduleTitle),
                'duration_minutes' => 8 + ($index % 4),
            ];
        }

        return $lessons;
    }

    /**
     * Seed a sample quiz for the provided lesson.
     *
     * @param array<string, mixed> $quizConfig
     */
    protected function seedQuizForLesson(Lesson $lesson, array $quizConfig): void
    {
        $quiz = Quiz::query()->updateOrCreate(
            ['lesson_id' => $lesson->id],
            [
                'title' => $quizConfig['title'],
                'description' => $quizConfig['description'],
                'passing_score' => 70,
                'is_published' => true,
            ],
        );

        foreach (Arr::get($quizConfig, 'questions', []) as $questionIndex => $questionData) {
            $question = QuizQuestion::query()->updateOrCreate(
                [
                    'quiz_id' => $quiz->id,
                    'sort_order' => $questionIndex + 1,
                ],
                [
                    'question_type' => 'single_choice',
                    'question_text' => $questionData['question'],
                    'explanation' => "Ushbu savol {$quiz->title} ichidagi asosiy tushunchani tekshiradi.",
                ],
            );

            foreach ($questionData['options'] as $answerIndex => $option) {
                QuizAnswer::query()->updateOrCreate(
                    [
                        'quiz_question_id' => $question->id,
                        'sort_order' => $answerIndex + 1,
                    ],
                    [
                        'answer_text' => $option,
                        'is_correct' => $answerIndex === (int) $questionData['answer'],
                    ],
                );
            }
        }
    }

    /**
     * Quiz data for the first lesson.
     *
     * @return array<string, mixed>
     */
    protected function quizData(): array
    {
        return [
            'title' => "Kompyuter tarmog'i bo'yicha qisqa nazorat",
            'description' => "Ushbu nazorat birinchi darsdagi asosiy tushunchalarni mustahkamlash uchun tuzilgan.",
            'questions' => [
                [
                    'question' => "Kompyuter tarmog'ining asosiy vazifasi nima?",
                    'options' => [
                        "Faqat bitta qurilmani tezlashtirish",
                        "Qurilmalar o'rtasida ma'lumot almashish",
                        "Faqat internetga chiqish",
                        "Faqat printer ulash",
                    ],
                    'answer' => 1,
                ],
                [
                    'question' => "Tarmoq orqali odatda nimalarni ulash mumkin?",
                    'options' => [
                        "Faqat monitorlarni",
                        "Faqat klaviaturalarni",
                        "Kompyuterlar, serverlar va umumiy resurslarni",
                        "Faqat telefonlarni",
                    ],
                    'answer' => 2,
                ],
                [
                    'question' => "Mijoz-server modelida serverning roli qanday?",
                    'options' => [
                        "Xizmat va resurslarni taqdim etadi",
                        "Faqat foydalanuvchi sifatida ishlaydi",
                        "Tarmoq kabelini almashtiradi",
                        "Faqat faylni o'chiradi",
                    ],
                    'answer' => 0,
                ],
                [
                    'question' => "Tarmoq bo'lmasa umumiy fayl almashish qanday bo'ladi?",
                    'options' => [
                        "Ancha qulay va markazlashgan bo'ladi",
                        "Hech qanday farq bo'lmaydi",
                        "Ko'proq qo'lda va noqulay usullarga bog'lanib qoladi",
                        "Avtomatik ravishda tezlashadi",
                    ],
                    'answer' => 2,
                ],
            ],
        ];
    }

    /**
     * Convert a human readable duration into minutes.
     */
    protected function parseDurationToMinutes(?string $duration): ?int
    {
        if ($duration === null) {
            return null;
        }

        $hours = 0;
        $minutes = 0;

        if (preg_match('/(\d+)\s*soat/u', $duration, $hourMatches) === 1) {
            $hours = (int) $hourMatches[1];
        }

        if (preg_match('/(\d+)\s*daq/i', $duration, $minuteMatches) === 1) {
            $minutes = (int) $minuteMatches[1];
        }

        return ($hours * 60) + $minutes;
    }

    /**
     * Sum all module durations from config.
     *
     * @param array<int, array<string, mixed>> $modules
     */
    protected function sumModuleDurations(array $modules): int
    {
        return (int) collect($modules)
            ->pluck('duration')
            ->map(fn (?string $duration) => $this->parseDurationToMinutes($duration) ?? 0)
            ->sum();
    }

    /**
     * Build a simple default glossary for lessons.
     *
     * @return array<int, array{term: string, definition: string}>
     */
    protected function defaultKeyTerms(string $moduleTitle): array
    {
        return [
            [
                'term' => 'Asosiy tushuncha',
                'definition' => "{$moduleTitle} mavzusini tushunish uchun yodda saqlanadigan eng muhim g'oya.",
            ],
            [
                'term' => 'Amaliy misol',
                'definition' => "Nazariyani real tarmoq holatiga bog'lab tushuntiruvchi oddiy ssenariy.",
            ],
        ];
    }
}
