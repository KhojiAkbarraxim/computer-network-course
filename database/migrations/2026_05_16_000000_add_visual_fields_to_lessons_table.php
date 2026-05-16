<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table): void {
            $table->string('visual_title')->nullable()->after('key_terms');
            $table->text('visual_description')->nullable()->after('visual_title');
            $table->json('visual_steps')->nullable()->after('visual_description');
            $table->string('diagram_type')->nullable()->after('visual_steps');
        });

        DB::table('lessons')
            ->select(['id', 'title'])
            ->orderBy('id')
            ->get()
            ->each(function (object $lesson): void {
                $visual = $this->visualDataForTitle((string) $lesson->title);

                if ($visual === null) {
                    return;
                }

                DB::table('lessons')
                    ->where('id', $lesson->id)
                    ->update([
                        'visual_title' => $visual['visual_title'],
                        'visual_description' => $visual['visual_description'],
                        'visual_steps' => json_encode($visual['visual_steps'], JSON_UNESCAPED_UNICODE),
                        'diagram_type' => $visual['diagram_type'],
                    ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table): void {
            $table->dropColumn([
                'visual_title',
                'visual_description',
                'visual_steps',
                'diagram_type',
            ]);
        });
    }

    /**
     * Provide default visual data for existing demo lessons.
     *
     * @return array<string, mixed>|null
     */
    protected function visualDataForTitle(string $title): ?array
    {
        $normalized = mb_strtolower($title);

        if (str_contains($normalized, "kompyuter tarmog'i nima")) {
            return [
                'visual_title' => "Kompyuter tarmog'i qanday ishlaydi?",
                'visual_description' => "Asosiy qurilmalar o'rtasidagi ma'lumot oqimini soddalashtirilgan ko'rinishda kuzating.",
                'visual_steps' => [
                    'Kompyuter tarmoq so\'rovini yuboradi',
                    'Switch ichki aloqani yo\'naltiradi',
                    'Router tashqi tarmoqqa chiqishni boshqaradi',
                    'Internet orqali xizmatga ulaniladi',
                ],
                'diagram_type' => 'basic-network',
            ];
        }

        if (str_contains($normalized, 'osi modeli')) {
            return [
                'visual_title' => 'OSI modeli qatlamlari',
                'visual_description' => "Har bir qatlamning vazifasi bir-biriga ulanib, to'liq tarmoq mantiqini shakllantiradi.",
                'visual_steps' => [
                    'Muammoni kerakli qatlamda aniqlang',
                    'Har bir qatlamning vazifasini ajratib ko\'ring',
                    'Qaysi qurilma qaysi qatlamga yaqin ishlashini solishtiring',
                ],
                'diagram_type' => 'osi',
            ];
        }

        if (str_contains($normalized, 'tcp/ip model')) {
            return [
                'visual_title' => 'TCP/IP modeli oqimi',
                'visual_description' => "Ma'lumot ilovadan boshlanib tarmoqqa kirish qatlamigacha bosqichma-bosqich harakatlanadi.",
                'visual_steps' => [
                    'Ilova ma\'lumotni tayyorlaydi',
                    'Transport qatlamida uzatish boshqariladi',
                    'Internet qatlami manzillashni bajaradi',
                    'Tarmoqqa kirish qatlami signalni uzatadi',
                ],
                'diagram_type' => 'tcp-ip',
            ];
        }

        if (str_contains($normalized, "xavfsizlikning asosiy tamoyillari")) {
            return [
                'visual_title' => 'Tarmoq xavfsizligi zanjiri',
                'visual_description' => "Foydalanuvchi so'rovi himoya qatlamidan o'tib ichki tarmoqqa kiradi.",
                'visual_steps' => [
                    'Foydalanuvchi so\'rovi qabul qilinadi',
                    'Firewall kiruvchi trafikni tekshiradi',
                    'Faqat xavfsiz trafik himoyalangan tarmoqqa uzatiladi',
                ],
                'diagram_type' => 'security',
            ];
        }

        if (str_contains($normalized, 'yakuniy laboratoriya mashqi')) {
            return [
                'visual_title' => 'Laboratoriya ishining bajarilish ketma-ketligi',
                'visual_description' => "Topshiriqni bajarish, tekshirish va yakuniy natijani baholash jarayoni shu blokda ko'rsatiladi.",
                'visual_steps' => [
                    'Topshiriq shartlarini diqqat bilan o\'qing',
                    'Qadamlarni ketma-ket tekshirib bajaring',
                    'Natijani tahlil qilib xulosaga keling',
                ],
                'diagram_type' => 'lab',
            ];
        }

        return null;
    }
};
