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
                    array_merge([
                        'title' => $lessonData['title'],
                        'slug' => Str::slug($lessonData['title']),
                        'short_description' => $lessonData['short_description'],
                        'content' => $lessonData['content'],
                        'important_note_title' => $lessonData['important_note_title'] ?? 'Muhim eslatma',
                        'important_note_text' => $lessonData['important_note_text'] ?? null,
                        'key_terms' => $lessonData['key_terms'] ?? [],
                        'visual_title' => $lessonData['visual_title'] ?? null,
                        'visual_description' => $lessonData['visual_description'] ?? null,
                        'visual_steps' => $lessonData['visual_steps'] ?? null,
                        'diagram_type' => $lessonData['diagram_type'] ?? null,
                        'duration_minutes' => $lessonData['duration_minutes'],
                        'is_published' => true,
                    ], $this->visualDataForLesson($lessonData['title'], $moduleData['title'])),
                );
            }
        }

        Lesson::query()
            ->select('lessons.*')
            ->join('modules', 'modules.id', '=', 'lessons.module_id')
            ->with([
                'module' => fn ($query) => $query->with([
                    'lessons' => fn ($lessonQuery) => $lessonQuery
                        ->orderBy('sort_order')
                        ->orderBy('id'),
                ]),
            ])
            ->orderBy('modules.sort_order')
            ->orderBy('lessons.sort_order')
            ->orderBy('lessons.id')
            ->get()
            ->each(fn (Lesson $lesson) => $this->seedQuizForLesson($lesson));
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
            $lessons[] = array_merge([
                'title' => $title,
                'short_description' => $moduleDescription ?: "{$moduleTitle} bo'yicha asosiy mavzularni tushuntiradi.",
                'content' => "{$moduleTitle} modulidagi {$title} darsida mavzuning amaliy ahamiyati, asosiy atamalari va real tarmoqdagi qo'llanishi qisqa va tushunarli usulda yoritiladi.\n\nDars davomida asosiy tushuncha, oddiy misol va amaliy qarash birgalikda beriladi, shuning uchun boshlovchi foydalanuvchi mavzuni bosqichma-bosqich o'zlashtira oladi.",
                'important_note_title' => "Muhim eslatma",
                'important_note_text' => "{$title} mavzusini mustahkamlash uchun darsdagi asosiy atamalarni qayta ko'rib chiqish tavsiya etiladi.",
                'key_terms' => $this->defaultKeyTerms($moduleTitle),
                'duration_minutes' => 8 + ($index % 4),
            ], $this->visualDataForLesson($title, $moduleTitle));
        }

        return $lessons;
    }

    /**
     * Seed a quiz for the provided lesson.
     */
    protected function seedQuizForLesson(Lesson $lesson): void
    {
        $quizConfig = $this->quizDataForLesson($lesson);
        $quiz = Quiz::query()->updateOrCreate(
            ['lesson_id' => $lesson->id],
            [
                'title' => $quizConfig['title'],
                'description' => $quizConfig['description'],
                'passing_score' => 70,
                'is_published' => true,
            ],
        );

        $questionIds = [];

        foreach (Arr::get($quizConfig, 'questions', []) as $questionIndex => $questionData) {
            $question = QuizQuestion::query()->updateOrCreate(
                [
                    'quiz_id' => $quiz->id,
                    'sort_order' => $questionIndex + 1,
                ],
                [
                    'question_type' => 'single_choice',
                    'question_text' => $questionData['question'],
                    'explanation' => $questionData['explanation'],
                ],
            );

            $questionIds[] = $question->id;
            $answerIds = [];

            foreach ($questionData['options'] as $answerIndex => $option) {
                $answer = QuizAnswer::query()->updateOrCreate(
                    [
                        'quiz_question_id' => $question->id,
                        'sort_order' => $answerIndex + 1,
                    ],
                    [
                        'answer_text' => $option,
                        'is_correct' => $answerIndex === (int) $questionData['answer'],
                    ],
                );

                $answerIds[] = $answer->id;
            }

            QuizAnswer::query()
                ->where('quiz_question_id', $question->id)
                ->whereNotIn('id', $answerIds)
                ->delete();
        }

        QuizQuestion::query()
            ->where('quiz_id', $quiz->id)
            ->whereNotIn('id', $questionIds)
            ->delete();
    }

    /**
     * Build lesson specific quiz data.
     *
     * @return array<string, mixed>
     */
    protected function quizDataForLesson(Lesson $lesson): array
    {
        $topic = $this->lessonTopic($lesson->title);
        $profile = $this->quizProfileForLesson($lesson->title, $lesson->module?->title ?? 'Kompyuter tarmoqlari');

        return [
            'title' => "{$lesson->title} bo'yicha nazorat",
            'description' => "\"{$topic}\" darsidagi asosiy tushunchalarni mustahkamlash uchun qisqa nazorat.",
            'questions' => [
                $this->buildQuizQuestion(
                    "\"{$topic}\" mavzusida asosiy tushuncha qaysi javobda to'g'ri berilgan?",
                    $profile['concept'],
                    $this->contextualDistractorsForLesson($lesson, 'concept', $profile['concept']),
                    1,
                    "{$topic} mavzusida eslab qolish kerak bo'lgan asosiy fikr shu javobda berilgan.",
                ),
                $this->buildQuizQuestion(
                    "\"{$topic}\" bo'yicha amaliy holatda qaysi misol mos keladi?",
                    $profile['example'],
                    $this->contextualDistractorsForLesson($lesson, 'example', $profile['example']),
                    2,
                    "{$topic} mavzusini amaliyotda ko'rish uchun shu misol eng mos keladi.",
                ),
                $this->buildQuizQuestion(
                    "\"{$topic}\" bilan ishlaganda nimaga e'tibor berish kerak?",
                    $profile['attention'],
                    $this->contextualDistractorsForLesson($lesson, 'attention', $profile['attention']),
                    0,
                    "{$topic} bo'yicha to'g'ri yondashuv shu javobda ko'rsatilgan.",
                ),
            ],
        ];
    }

    /**
     * Build a quiz question with one correct answer.
     *
     * @param list<string> $distractors
     * @return array<string, mixed>
     */
    protected function buildQuizQuestion(string $question, string $correctAnswer, array $distractors, int $correctIndex, string $explanation): array
    {
        $options = array_values(array_slice($distractors, 0, 3));
        array_splice($options, $correctIndex, 0, [$correctAnswer]);

        return [
            'question' => $question,
            'options' => $options,
            'answer' => $correctIndex,
            'explanation' => $explanation,
        ];
    }

    /**
     * Normalize lesson title for quiz text.
     */
    protected function lessonTopic(string $lessonTitle): string
    {
        return trim((string) preg_replace('/^\d+\.\d+\s*-\s*/u', '', $lessonTitle));
    }

    /**
     * Build a topic profile for each lesson.
     *
     * @return array{concept: string, example: string, attention: string}
     */
    protected function quizProfileForLesson(string $lessonTitle, string $moduleTitle): array
    {
        $normalizedLesson = mb_strtolower($lessonTitle);
        $normalizedModule = mb_strtolower($moduleTitle);
        $topic = $this->lessonTopic($lessonTitle);

        return match (true) {
            str_contains($normalizedLesson, "kompyuter tarmog'i nima") => [
                'concept' => "Kompyuter tarmog'i qurilmalar o'rtasida ma'lumot almashish va resurslardan birga foydalanish uchun xizmat qiladi.",
                'example' => "Bir ofisdagi kompyuterlar va printerlarni bitta tarmoq orqali ulash.",
                'attention' => "Qaysi qurilmalar tarmoqqa ulanganini va ma'lumot oqimi qayerdan qayerga borishini tushunish kerak.",
            ],
            str_contains($normalizedLesson, 'tarmoq nima uchun kerak') => [
                'concept' => "Tarmoq fayl, internet va umumiy qurilmalardan birgalikda foydalanishni qulaylashtiradi.",
                'example' => "Sinfdagi bir nechta kompyuterning bitta printerdan foydalanishi.",
                'attention' => "Tarmoqning foydasi tezkor almashish va markazlashgan boshqaruvda ekanini eslab qolish kerak.",
            ],
            str_contains($normalizedLesson, 'mijoz va server') => [
                'concept' => "Mijoz so'rov yuboradi, server esa xizmat yoki resursni taqdim etadi.",
                'example' => "Brauzer veb-serverdan sahifa so'rashi.",
                'attention' => "Xizmatni so'rayotgan qurilma va xizmatni berayotgan qurilmani adashtirmaslik kerak.",
            ],
            str_contains($normalizedLesson, 'topologiya') || str_contains($normalizedLesson, 'ulanish ko\'rinishlari') => [
                'concept' => "Topologiya tarmoq qurilmalarining qanday tartibda ulanganini ko'rsatadi.",
                'example' => "Yulduzsimon topologiyada barcha kompyuterlarni switch markaziga ulash.",
                'attention' => "Topologiya tanlashda kabel yo'li va keyin kengaytirish qulayligini hisobga olish kerak.",
            ],
            str_contains($normalizedLesson, 'lan tarmog') => [
                'concept' => "LAN kichik hududdagi qurilmalarni tezkor va mahalliy tarzda ulaydi.",
                'example' => "Bitta ofis yoki kompyuter sinfidagi qurilmalarni bir tarmoqqa ulash.",
                'attention' => "LAN odatda kichik joy va yaqin joylashgan qurilmalar uchun ishlatilishini esda tutish kerak.",
            ],
            str_contains($normalizedLesson, 'man tarmog') => [
                'concept' => "MAN bir shahar yoki yirik kampus bo'ylab joylashgan tarmoqlarni bog'laydi.",
                'example' => "Shaharning turli nuqtalaridagi universitet binolarini ulash.",
                'attention' => "MAN LANdan kattaroq, lekin WANdan torroq qamrovga ega ekanini ajratish kerak.",
            ],
            str_contains($normalizedLesson, 'wan tarmog') => [
                'concept' => "WAN uzoq hududlardagi tarmoqlarni bir-biriga ulash uchun xizmat qiladi.",
                'example' => "Bir necha shahardagi filiallarni bitta kompaniya tarmog'iga bog'lash.",
                'attention' => "WAN katta masofada ishlashi va internet kabi keng tarmoqlarga yaqin ekanini bilish kerak.",
            ],
            str_contains($normalizedLesson, 'tanlash mezonlari') => [
                'concept' => "Tarmoq turini tanlashda qamrov hududi, foydalanuvchilar soni va xarajat hisobga olinadi.",
                'example' => "Kichik ofis uchun LAN, bir nechta filial uchun esa WAN tanlash.",
                'attention' => "Tarmoq turini faqat nomiga emas, vazifasi va qamroviga qarab tanlash kerak.",
            ],
            str_contains($normalizedLesson, 'osi modeli haqida') => [
                'concept' => "OSI modeli tarmoq aloqasini yetti qatlamga bo'lib tushunishga yordam beradi.",
                'example' => "Muammoni qaysi qatlamda ekanini bosqichma-bosqich tekshirish.",
                'attention' => "OSI modeli amaliy protokollarning nusxasi emas, tushunish uchun model ekanini unutmaslik kerak.",
            ],
            str_contains($normalizedLesson, '7 qatlam') => [
                'concept' => "OSI modelidagi yetti qatlam vazifalarni alohida bosqichlarga ajratadi.",
                'example' => "Ilova qatlami dasturga, fizik qatlam esa kabel va signalga yaqin ishlashi.",
                'attention' => "Qatlamlar ketma-ketligi va har birining o'z roli borligini yodda tutish kerak.",
            ],
            str_contains($normalizedLesson, 'har bir qatlam vazifasi') => [
                'concept' => "Har bir qatlam tarmoqda alohida vazifani bajaradi va bir-birini to'ldiradi.",
                'example' => "Transport qatlami uzatishni nazorat qilsa, tarmoq qatlami manzillashni boshqaradi.",
                'attention' => "Bitta vazifani noto'g'ri qatlamga bog'lab yubormaslik kerak.",
            ],
            str_contains($normalizedLesson, 'qurilmalar va qatlamlar') => [
                'concept' => "Tarmoq qurilmalari ma'lum qatlam vazifalariga yaqin ishlaydi.",
                'example' => "Switch kanal qatlamiga, router esa tarmoq qatlamiga yaqin ishlashi.",
                'attention' => "Qurilmaning asosiy vazifasini tegishli qatlam bilan bog'lash kerak.",
            ],
            str_contains($normalizedLesson, 'protokollar misoli') => [
                'concept' => "HTTP, TCP, IP va Ethernet kabi protokollar turli qatlamlarda ishlaydi.",
                'example' => "HTTP ilova qatlamida, IP esa tarmoq qatlamida ishlashini ko'rsatish.",
                'attention' => "Protokol nomini eshitganda uni qaysi qatlamga yaqin ekanini ham eslash kerak.",
            ],
            str_contains($normalizedLesson, 'tcp/ip modeliga kirish') => [
                'concept' => "TCP/IP modeli internetda ko'p ishlatiladigan qatlamli tarmoq modelidir.",
                'example' => "Ilovadan chiqqan ma'lumot TCP/IP qatlamlari orqali internetga uzatilishi.",
                'attention' => "TCP/IP modelida qatlamlar soni OSI modelidan kamroq ekanini bilish kerak.",
            ],
            str_contains($normalizedLesson, 'qatlamlar va protokollar') && str_contains($normalizedModule, 'tcp/ip') => [
                'concept' => "TCP/IP modelida protokollar qatlamlar bo'yicha guruhlanadi va birga ishlaydi.",
                'example' => "TCP transport qatlamida, IP esa internet qatlamida ishlashi.",
                'attention' => "Protokol va qatlam o'rtasidagi bog'lanishni tushunish kerak.",
            ],
            str_contains($normalizedLesson, 'paketlar oqimi') => [
                'concept' => "Ma'lumot tarmoq bo'ylab qatlamlar orqali paket ko'rinishida harakatlanadi.",
                'example' => "Foydalanuvchi xabari kapsulalanib tarmoqqa uzatilishi va qarshi tomonda ochilishi.",
                'attention' => "Paket oqimi bosqichma-bosqich sodir bo'lishini tasavvur qila olish kerak.",
            ],
            str_contains($normalizedLesson, 'tcp va ip vazifalari') => [
                'concept' => "TCP ishonchli uzatishni, IP esa manzillash va yo'naltirishni bajaradi.",
                'example' => "TCP paketlar tartibini nazorat qilsa, IP ularni kerakli manzilga olib boradi.",
                'attention' => "TCP va IP vazifalarini bir-biri bilan almashtirib yubormaslik kerak.",
            ],
            str_contains($normalizedLesson, 'ipv4 manzil tuzilishi') => [
                'concept' => "IPv4 manzili to'rtta oktetdan tashkil topgan 32 bitli manzildir.",
                'example' => "192.168.1.10 ko'rinishidagi manzilni o'qish.",
                'attention' => "IPv4 manzilida nuqta bilan ajratilgan qismlar borligini esda tutish kerak.",
            ],
            str_contains($normalizedLesson, 'tarmoq va host qismi') => [
                'concept' => "IP manzilning bir qismi tarmoqni, boshqa qismi esa qurilmani bildiradi.",
                'example' => "Bir subnet ichida bir xil tarmoq qismi, turli host qismlaridan foydalanish.",
                'attention' => "Tarmoq qismi va host qismini subnet maska orqali ajratish kerak.",
            ],
            str_contains($normalizedLesson, 'subnet maska') => [
                'concept' => "Subnet maska IP manzilda qaysi bitlar tarmoqqa tegishli ekanini ko'rsatadi.",
                'example' => "255.255.255.0 maskasi bilan /24 tarmoqni aniqlash.",
                'attention' => "Maska noto'g'ri bo'lsa qurilmalar bir-birini topolmasligini bilish kerak.",
            ],
            str_contains($normalizedLesson, 'cidr yozuvi') => [
                'concept' => "CIDR yozuvi subnetni qisqa ko'rinishda, masalan /24 shaklida ifodalaydi.",
                'example' => "192.168.10.0/24 tarmog'ini yozish.",
                'attention' => "Slashdan keyingi son tarmoq bitlari sonini bildiradi.",
            ],
            str_contains($normalizedLesson, 'subnet hisoblash') => [
                'concept' => "Subnet hisoblash orqali nechta host va nechta tarmoq bo'lishini topish mumkin.",
                'example' => "Kichik ofis uchun nechta qurilma sig'ishini oldindan hisoblash.",
                'attention' => "Hisoblashda foydalaniladigan host bitlari sonini to'g'ri aniqlash kerak.",
            ],
            str_contains($normalizedLesson, 'manzillash xatolarini topish') => [
                'concept' => "Manzillash xatolari noto'g'ri IP, maska yoki shlyuz sabab yuz beradi.",
                'example' => "Bir subnetdagi ikki qurilmaga turli maska berib qo'yilganini topish.",
                'attention' => "IP, subnet maska va default gateway ni birga tekshirish kerak.",
            ],
            str_contains($normalizedLesson, 'hub qanday ishlaydi') => [
                'concept' => "Hub kelgan signalni barcha portlarga bir xil uzatadi.",
                'example' => "Bitta portga kelgan trafikning barcha ulangan qurilmalarga tarqalishi.",
                'attention' => "Hub aqlli tanlamaydi, trafikni hammaga uzatishini bilish kerak.",
            ],
            str_contains($normalizedLesson, 'switchning vazifasi') => [
                'concept' => "Switch MAC manzilga qarab trafikni kerakli portga yo'naltiradi.",
                'example' => "Bitta kompyuterdan chiqqan kadrni faqat kerakli printer portiga yuborish.",
                'attention' => "Switch hubdan farqli ravishda barcha portlarga emas, kerakli portga ishlashini tushunish kerak.",
            ],
            str_contains($normalizedLesson, 'router nima qiladi') => [
                'concept' => "Router turli tarmoqlar orasida paketlarni yo'naltiradi.",
                'example' => "Mahalliy ofis tarmog'idan internetga chiqishni boshqarish.",
                'attention' => "Router bir tarmoq ichidagi emas, turli tarmoqlar orasidagi harakatni boshqaradi.",
            ],
            str_contains($normalizedLesson, 'qurilmalarni taqqoslash') => [
                'concept' => "Hub, switch va router turli vazifani bajaradi va bir-birini to'liq almashtirmaydi.",
                'example' => "Kichik ofisda switch ichki ulanishni, router esa internetga chiqishni boshqarishi.",
                'attention' => "Qurilma tanlashda uning vazifasini aniq bilish kerak.",
            ],
            str_contains($normalizedLesson, 'kichik topologiya misoli') => [
                'concept' => "Kichik topologiyada qurilmalar bir-biri bilan mantiqiy va qulay tartibda ulanadi.",
                'example' => "Kompyuterlar switchga, switch esa routerga ulanishi.",
                'attention' => "Ulanish zanjirini chizib ko'rish amaliy tushunishni kuchaytiradi.",
            ],
            str_contains($normalizedLesson, 'dns xizmatining roli') => [
                'concept' => "DNS domen nomlarini IP manzillarga o'giradi.",
                'example' => "Brauzerga sayt nomi yozilganda unga mos IP topilishi.",
                'attention' => "DNS nomni eslab qolishni osonlashtiradi, lekin real aloqa IP bilan bo'lishini bilish kerak.",
            ],
            str_contains($normalizedLesson, 'dhcp orqali ip taqsimlash') => [
                'concept' => "DHCP qurilmalarga IP va boshqa tarmoq sozlamalarini avtomatik beradi.",
                'example' => "Noutbuk Wi-Fi ga ulanganda avtomatik IP manzil olishi.",
                'attention' => "DHCP ishlamasa qurilma kerakli tarmoq sozlamalarini olmasligini tekshirish kerak.",
            ],
            str_contains($normalizedLesson, 'nat nima uchun kerak') => [
                'concept' => "NAT ichki xususiy manzillarni tashqi tarmoqda ishlashga moslashtiradi.",
                'example' => "Bir nechta ofis kompyuterining bitta tashqi IP orqali internetga chiqishi.",
                'attention' => "NAT ichki va tashqi tarmoq o'rtasidagi manzil almashtirishini bajarishini tushunish kerak.",
            ],
            str_contains($normalizedLesson, 'xizmatlar birgalikda qanday ishlaydi') => [
                'concept' => "DNS, DHCP va NAT birgalikda ishlaganda foydalanuvchi tarmoqqa qulay ulanadi.",
                'example' => "Qurilma IP ni DHCP dan olib, sayt nomini DNS orqali yechib, NAT orqali internetga chiqishi.",
                'attention' => "Bu xizmatlar bir-birini to'ldirishini bosqichma-bosqich ko'rish kerak.",
            ],
            str_contains($normalizedLesson, 'oddiy sozlash ssenariysi') => [
                'concept' => "Oddiy sozlashda xizmatlar kerakli ketma-ketlikda yoqilib va tekshirib chiqiladi.",
                'example' => "Avval IP taqsimlash, keyin DNS tekshiruvi, so'ng internet chiqishini sinash.",
                'attention' => "Sozlash jarayonida har bir xizmatni alohida tekshirish foydali.",
            ],
            str_contains($normalizedLesson, 'simsiz tarmoq asoslari') => [
                'concept' => "Simsiz tarmoq kabelsiz, radio to'lqinlar orqali aloqa o'rnatadi.",
                'example' => "Noutbukning Wi-Fi orqali access point ga ulanishi.",
                'attention' => "Simsiz tarmoqda qamrov va signal sifati kabelga qaraganda muhimroq ekanini bilish kerak.",
            ],
            str_contains($normalizedLesson, 'standartlar va chastotalar') => [
                'concept' => "Wi-Fi standartlari va chastotalar tezlik hamda qamrovga ta'sir qiladi.",
                'example' => "2.4 GHz kengroq qamrov, 5 GHz esa ko'proq tezlik berishi.",
                'attention' => "Chastota tanlashda faqat tezlikka emas, to'siq va masofaga ham qarash kerak.",
            ],
            str_contains($normalizedLesson, 'qamrov va signal sifati') => [
                'concept' => "Signal sifati masofa, devor va shovqin sabab o'zgaradi.",
                'example' => "Routerdan uzoq xonada signalning pasayib ketishi.",
                'attention' => "Qamrovni baholaganda access point joylashuvi va to'siqlarni ko'rish kerak.",
            ],
            str_contains($normalizedLesson, 'wi-fi xavfsizligi') => [
                'concept' => "Wi-Fi xavfsizligi kuchli parol va zamonaviy himoya rejimlariga tayanadi.",
                'example' => "Uy tarmog'ida WPA2 yoki WPA3 va kuchli paroldan foydalanish.",
                'attention' => "Oddiy va qisqa parollar simsiz tarmoqni oson zaiflashtirishi mumkin.",
            ],
            str_contains($normalizedLesson, 'xavfsizlikning asosiy tamoyillari') => [
                'concept' => "Tarmoq xavfsizligi maxfiylik, yaxlitlik va mavjudlik tamoyillariga tayanadi.",
                'example' => "Muhim faylni faqat ruxsatli foydalanuvchi ko'ra olishi va o'zgartira olishi.",
                'attention' => "Xavfsizlik faqat parol emas, bir nechta tamoyil va choralarni o'z ichiga oladi.",
            ],
            str_contains($normalizedLesson, 'kuchli parol siyosati') => [
                'concept' => "Kuchli parol uzun, murakkab va boshqa tizimlarda takrorlanmaydi.",
                'example' => "Har bir xizmat uchun alohida va uzun parol ishlatish.",
                'attention' => "Faqat bitta kuchli parolni hamma joyda qayta ishlatish xavfli ekanini bilish kerak.",
            ],
            str_contains($normalizedLesson, 'segmentatsiya va ajratish') => [
                'concept' => "Segmentatsiya tarmoqni kichik bo'limlarga ajratib xavfsizlik va boshqaruvni yaxshilaydi.",
                'example' => "Mehmonlar Wi-Fi sini ichki ofis tarmog'idan alohida VLAN ga ajratish.",
                'attention' => "Hamma qurilmalarni bitta tekis tarmoqqa qo'yish har doim ham xavfsiz emas.",
            ],
            str_contains($normalizedLesson, 'firewall vazifasi') => [
                'concept' => "Firewall kiruvchi va chiquvchi trafikni qoidalar asosida nazorat qiladi.",
                'example' => "Keraksiz portlarni yopib, faqat kerakli xizmatlarga ruxsat berish.",
                'attention' => "Firewall hamma narsani avtomatik hal qilmaydi, qoidalari to'g'ri sozlanishi kerak.",
            ],
            str_contains($normalizedLesson, 'oddiy tahdid ssenariylari') => [
                'concept' => "Oddiy tahdidlar noto'g'ri havola, zaif parol yoki shubhali trafik orqali kelishi mumkin.",
                'example' => "Foydalanuvchining fishing xatdagi havolani bosib yuborishi.",
                'attention' => "Tahdidlarni erta ko'rish uchun foydalanuvchi xatti-harakati va tizim belgilariga e'tibor berish kerak.",
            ],
            str_contains($normalizedLesson, 'himoya choralarini mustahkamlash') => [
                'concept' => "Himoyani kuchaytirish uchun yangilash, zaxira nusxa va qo'shimcha tekshiruvlar kerak bo'ladi.",
                'example' => "Tizimni yangilab, zaxira nusxa olib va ikki bosqichli tasdiqlashni yoqish.",
                'attention' => "Bitta himoya chorasi yetarli emas, bir nechta qatlamli himoya ishlatish kerak.",
            ],
            str_contains($normalizedLesson, 'kichik ofis tarmog') => [
                'concept' => "Kichik ofis tarmog'ida qurilmalar va xizmatlar sodda, lekin tartibli reja asosida ulanadi.",
                'example' => "Kompyuterlar, printer va routerdan iborat ofis tarmog'ini chizish.",
                'attention' => "Avval qurilmalar ro'yxati va ulanish maqsadini aniqlab olish kerak.",
            ],
            str_contains($normalizedLesson, 'kabel va ulanishlarni tekshirish') => [
                'concept' => "Kabel va portlarni tekshirish tarmoq muammosini topishdagi birinchi amaliy qadamdir.",
                'example' => "Ulanmagan kabel yoki o'chgan port indikatori sabab aloqa yo'qligini aniqlash.",
                'attention' => "Murakkab sozlamalardan oldin fizik ulanishni tekshirish kerak.",
            ],
            str_contains($normalizedLesson, 'ip rejalashtirish mashqi') => [
                'concept' => "IP rejalashtirish qurilmalarga mantiqiy va takrorlanmaydigan manzil berishni ta'minlaydi.",
                'example' => "Ofis bo'limlari uchun alohida subnet ajratish.",
                'attention' => "Qurilmalar soni va kelajakdagi kengayishni hisobga olib reja qilish kerak.",
            ],
            str_contains($normalizedLesson, 'switch portlarini tahlil qilish') => [
                'concept' => "Switch portlarini tahlil qilish orqali qaysi qurilma qayerga ulanganini aniqlash mumkin.",
                'example' => "Port holati va faol MAC manzillarni tekshirish.",
                'attention' => "Port ishlamayapti degan xulosadan oldin uning holati va ulanishini ko'rish kerak.",
            ],
            str_contains($normalizedLesson, 'router yo\'naltirish tekshiruvi') => [
                'concept' => "Router yo'naltirishini tekshirish paketning to'g'ri tarmoqqa ketayotganini ko'rsatadi.",
                'example' => "Default route yoki mahalliy marshrutlar to'g'ri yozilganini ko'rish.",
                'attention' => "Gateway va marshrut yozuvlarini birgalikda tekshirish muhim.",
            ],
            str_contains($normalizedLesson, 'dns muammosini aniqlash') => [
                'concept' => "DNS muammosida nom yechilmaydi, lekin IP bilan aloqa ba'zan ishlashi mumkin.",
                'example' => "Sayt nomi ochilmayapti, lekin uning IP manziliga ping javob berishi.",
                'attention' => "DNS muammosini internet umuman yo'q degan holatdan ajratish kerak.",
            ],
            str_contains($normalizedLesson, 'dhcp xizmatini kuzatish') => [
                'concept' => "DHCP jarayonini kuzatish qurilma qachon va qanday sozlama olayotganini tushunishga yordam beradi.",
                'example' => "Yangi noutbuk tarmoqqa ulanganda avtomatik IP olish jarayonini ko'rish.",
                'attention' => "IP berilmasa DHCP server va ulanish holatini birga tekshirish kerak.",
            ],
            str_contains($normalizedLesson, 'wi-fi qamrovini baholash') => [
                'concept' => "Wi-Fi qamrovini baholash access point joylashuvi va signal kuchini tekshirishdir.",
                'example' => "Ofisning turli nuqtalarida signalni solishtirish.",
                'attention' => "Qamrovni faqat router yonida emas, foydalanuvchi ishlaydigan joylarda ham tekshirish kerak.",
            ],
            str_contains($normalizedLesson, 'xavfsizlik nazorat ro\'yxati') => [
                'concept' => "Xavfsizlik nazorat ro'yxati asosiy himoya choralarini muntazam tekshirib turishga yordam beradi.",
                'example' => "Parol, yangilanish va zaxira nusxani bitta ro'yxat bo'yicha tekshirish.",
                'attention' => "Nazorat ro'yxatidan foydalanish unutib yuboriladigan mayda xatolarni kamaytiradi.",
            ],
            str_contains($normalizedLesson, 'yakuniy laboratoriya mashqi') => [
                'concept' => "Yakuniy laboratoriya mashqi avvalgi darslarda o'rganilgan tushunchalarni bir joyda qo'llashni talab qiladi.",
                'example' => "Topologiya, IP reja, DNS va xavfsizlikni bir ssenariy ichida tekshirish.",
                'attention' => "Yakuniy topshiriqda muammoni bosqichma-bosqich tekshirish eng to'g'ri yondashuv bo'ladi.",
            ],
            str_contains($normalizedLesson, 'mini test') || str_contains($normalizedLesson, 'nazorat') => [
                'concept' => "{$moduleTitle} bo'yicha asosiy tushunchalarni qisqa savollar orqali mustahkamlash.",
                'example' => "Moduldagi bir nechta tarmoq atamasini amaliy vaziyat bilan bog'lash.",
                'attention' => "Nazoratdan oldin asosiy atamalar va misollarni qayta ko'rib chiqish kerak.",
            ],
            str_contains($normalizedLesson, 'amaliy mashq') || str_contains($normalizedLesson, 'mashqlar') => [
                'concept' => "{$moduleTitle} mavzusini amaliy topshiriq orqali mustahkamlash.",
                'example' => "Nazariy tushunchani oddiy tarmoq ssenariysida qo'llab ko'rish.",
                'attention' => "Amaliy mashqda natijaga emas, bajarilgan bosqichlarning mantiqiga ham e'tibor berish kerak.",
            ],
            default => [
                'concept' => "{$topic} mavzusi {$moduleTitle} bo'yicha asosiy tarmoq tushunchasini sodda tarzda tushuntiradi.",
                'example' => "{$topic} bilan bog'liq oddiy tarmoq holatini amaliy ko'rib chiqish.",
                'attention' => "{$topic} mavzusida asosiy atamalar va jarayon ketma-ketligini eslab qolish kerak.",
            ],
        };
    }

    /**
     * Use sibling lesson profiles to build topic-relevant distractors.
     *
     * @return list<string>
     */
    protected function contextualDistractorsForLesson(Lesson $lesson, string $field, string $correctAnswer): array
    {
        $moduleTitle = $lesson->module?->title ?? 'Kompyuter tarmoqlari';

        $distractors = collect($lesson->module?->lessons ?? [])
            ->filter(fn (Lesson $candidate) => $candidate->id !== $lesson->id)
            ->map(function (Lesson $candidate) use ($moduleTitle, $field): string {
                $profile = $this->quizProfileForLesson($candidate->title, $moduleTitle);

                return trim((string) ($profile[$field] ?? ''));
            })
            ->filter(fn (string $text) => $text !== '' && $text !== $correctAnswer)
            ->unique()
            ->take(3)
            ->values()
            ->all();

        if (count($distractors) === 3) {
            return $distractors;
        }

        $fallback = match ($field) {
            'concept' => [
                "Bu tushuncha tarmoqdagi boshqa xizmatning vazifasi bilan adashib ketadi.",
                "Bu tushuncha ma'lumot uzatish jarayonini emas, tasodifiy tashqi belgini tasvirlaydi.",
                "Bu tushuncha mavzuning asosiy vazifasini to'liq ifodalamaydi.",
            ],
            'example' => [
                "Faqat bitta qurilmaning nomini o'zgartirish bilan cheklanish.",
                "Tarmoqsiz holatda ish stoli ko'rinishini almashtirish.",
                "Jarayonni tekshirmasdan faqat qurilmani o'chirib-yoqish.",
            ],
            default => [
                "Muammoni bosqichma-bosqich tekshirmasdan taxmin qilish.",
                "Asosiy atamalarni bir-biriga aralashtirib yuborish.",
                "Jarayon natijasini ko'rmasdan oldin xulosa qilish.",
            ],
        };

        return array_values(array_slice(array_unique(array_merge($distractors, $fallback)), 0, 3));
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

    /**
     * Build visual explanation data for every lesson.
     *
     * @return array<string, mixed>
     */
    protected function visualDataForLesson(string $lessonTitle, string $moduleTitle): array
    {
        $topic = $this->lessonTopic($lessonTitle);
        $diagramType = $this->diagramTypeForLesson($lessonTitle, $moduleTitle);

        return [
            'visual_title' => "{$lessonTitle} uchun vizual tushuntirish",
            'visual_description' => $this->visualDescriptionForLesson($topic, $diagramType),
            'visual_steps' => $this->visualStepsForLesson($topic, $diagramType),
            'diagram_type' => $diagramType,
        ];
    }

    /**
     * Resolve diagram type from module and lesson context.
     */
    protected function diagramTypeForLesson(string $lessonTitle, string $moduleTitle): string
    {
        $normalizedLesson = mb_strtolower($lessonTitle);
        $normalizedModule = mb_strtolower($moduleTitle);

        return match (true) {
            str_contains($normalizedModule, 'kompyuter tarmoqlariga kirish') => 'basic-network',
            str_contains($normalizedModule, 'tarmoq turlari') => 'network-types',
            str_contains($normalizedModule, 'osi') => 'osi',
            str_contains($normalizedModule, 'tcp/ip') => 'tcp-ip',
            str_contains($normalizedModule, 'subnet') || str_contains($normalizedModule, 'ip manzil') => 'ip-subnet',
            str_contains($normalizedModule, 'router, switch va hub') || str_contains($normalizedLesson, 'switch') || str_contains($normalizedLesson, 'router') || str_contains($normalizedLesson, 'hub') => 'devices',
            str_contains($normalizedModule, 'dns, dhcp va nat') => 'dns-dhcp-nat',
            str_contains($normalizedModule, 'wi-fi') || str_contains($normalizedModule, 'simsiz') => 'wifi',
            str_contains($normalizedModule, 'xavfsizlik') => 'security',
            str_contains($normalizedModule, 'laboratoriya') => 'lab',
            default => 'default',
        };
    }

    /**
     * Build a short visual description for the lesson page.
     */
    protected function visualDescriptionForLesson(string $topic, string $diagramType): string
    {
        return match ($diagramType) {
            'basic-network' => "\"{$topic}\" mavzusida qurilmalar qanday ulanib, ma'lumot qaysi yo'l bilan harakatlanishini sodda ko'rinishda kuzatasiz.",
            'network-types' => "\"{$topic}\" mavzusida tarmoq qamrovi kichik hududdan yirik hududgacha qanday kengayishini ko'rasiz.",
            'osi' => "\"{$topic}\" mavzusida qatlamlar tartibi va har bir qatlam nimaga xizmat qilishini vizual ravishda ko'rasiz.",
            'tcp-ip' => "\"{$topic}\" mavzusida ma'lumot TCP/IP bosqichlari orqali qanday o'tishini oddiy oqim bilan tushunasiz.",
            'ip-subnet' => "\"{$topic}\" mavzusida IP manzil, subnet maska va tarmoq qismlari qanday ajralishini ko'rasiz.",
            'devices' => "\"{$topic}\" mavzusida qurilmalar o'rtasidagi vazifa farqini ketma-ket sxema orqali ko'rasiz.",
            'dns-dhcp-nat' => "\"{$topic}\" mavzusida DNS, DHCP va NAT xizmatlari bir-birini qanday to'ldirishini ko'rasiz.",
            'wifi' => "\"{$topic}\" mavzusida qurilmaning Wi-Fi router orqali internetga chiqish jarayonini ko'rasiz.",
            'security' => "\"{$topic}\" mavzusida foydalanuvchi so'rovi himoya qatlamlaridan qanday o'tishini ko'rasiz.",
            'lab' => "\"{$topic}\" mavzusida amaliy topshiriqni bajarish bosqichlari va tekshirish tartibini ko'rasiz.",
            default => "\"{$topic}\" mavzusida asosiy tushuncha, jarayon va natija o'rtasidagi bog'lanishni ko'rasiz.",
        };
    }

    /**
     * Build visual steps for the lesson page.
     *
     * @return list<string>
     */
    protected function visualStepsForLesson(string $topic, string $diagramType): array
    {
        return match ($diagramType) {
            'basic-network' => [
                "{$topic} dagi asosiy qurilmalarni aniqlash",
                "Kompyuterdan tarmoqqa chiqish yo'lini ko'rish",
                "Switch va router vazifasini ajratib olish",
                "Natijada internet yoki xizmatga ulanishni mustahkamlash",
            ],
            'network-types' => [
                "{$topic} uchun qamrov hududini aniqlash",
                'LAN, MAN va WAN o\'rtasidagi farqni ko\'rish',
                'Qaysi holatda qaysi tarmoq turi tanlanishini mustahkamlash',
            ],
            'osi' => [
                "{$topic} bo'yicha qatlam nomlarini tartiblash",
                'Har bir qatlamning vazifasini alohida ko\'rish',
                'Protokol yoki qurilmani mos qatlamga bog\'lash',
                'Muammoni qaysi qatlamda izlashni eslab qolish',
            ],
            'tcp-ip' => [
                "{$topic} bo'yicha ilovadan boshlangan ma'lumotni kuzatish",
                'Transport va internet qatlamlarining vazifasini ko\'rish',
                'Tarmoqqa kirish bosqichida uzatish qanday tugashini mustahkamlash',
            ],
            'ip-subnet' => [
                "{$topic} dagi IP manzil yozuvini ko'rish",
                'Subnet maska nimani ajratishini aniqlash',
                'Tarmoq qismi va host qismini alohida ko\'rish',
                'Natijani oddiy misolda tekshirish',
            ],
            'devices' => [
                "{$topic} bilan bog'liq qurilma nomlarini aniqlash",
                'Hub, switch va router vazifalarini solishtirish',
                'Qaysi qurilma qaysi vazifada foydali ekanini mustahkamlash',
            ],
            'dns-dhcp-nat' => [
                "{$topic} bo'yicha xizmatlar ketma-ketligini ko'rish",
                'DNS, DHCP va NAT roli qayerda boshlanishini aniqlash',
                'Xizmatlar birgalikda foydalanuvchiga qanday qulaylik yaratishini mustahkamlash',
            ],
            'wifi' => [
                "{$topic} uchun qurilmaning ulanish nuqtasini ko'rish",
                'Wi-Fi router orqali signal qanday uzatilishini kuzatish',
                'Qamrov yoki xavfsizlikka ta\'sir qiluvchi omillarni mustahkamlash',
            ],
            'security' => [
                "{$topic} bo'yicha foydalanuvchi so'rovini kuzatish",
                'Firewall yoki himoya qatlami qayerda ishlashini aniqlash',
                "Xavfsiz trafik ichki tarmoqqa qanday o'tishini mustahkamlash",
            ],
            'lab' => [
                "{$topic} uchun topshiriq shartini aniqlash",
                'Kerakli buyruq yoki tekshiruv bosqichini tanlash',
                'Natijani tekshirish va xulosa chiqarishni mustahkamlash',
            ],
            default => [
                'Asosiy tushunchani aniqlash',
                'Jarayon qanday ishlashini ko\'rish',
                'Amaliy misol bilan mustahkamlash',
            ],
        };
    }
}
