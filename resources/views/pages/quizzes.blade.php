<section class="container-shell">
    <x-section-heading
        kicker="Nazoratlar"
        title="Mavjud nazoratlar ro'yxati"
        description="Bazadagi e'lon qilingan nazoratlarni tanlang va natijangizni saqlagan holda ishlang."
    />

    @if ($quizzes->isEmpty())
        <div class="card-surface mt-8 rounded-[2rem] p-8 text-center">
            <p class="font-display text-2xl font-semibold text-slate-950">Hozircha nazoratlar mavjud emas.</p>
            <p class="mt-3 text-sm leading-6 text-slate-600">
                Nazorat ma'lumotlari bazaga qo'shilgach, ushbu sahifada barcha mavjud nazoratlar ko'rsatiladi.
            </p>
        </div>
    @else
        <div class="mt-8 grid gap-6 lg:grid-cols-2 xl:grid-cols-3">
            @foreach ($quizzes as $quiz)
                <article class="card-surface h-full p-6">
                    <div class="flex items-start justify-between gap-4">
                        <span class="section-kicker">Nazorat</span>
                        <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-brand-700">
                            {{ $quiz->questions_count }} ta savol
                        </span>
                    </div>

                    <h2 class="mt-5 font-display text-2xl font-semibold text-slate-950">{{ $quiz->title }}</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600">
                        {{ $quiz->description ?: "Ushbu nazorat darsdagi asosiy tushunchalarni takrorlash uchun mo'ljallangan." }}
                    </p>

                    <div class="mt-6 grid gap-3 text-sm text-slate-600">
                        <div class="rounded-2xl bg-slate-50 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Bog'liq modul</p>
                            <p class="mt-1 font-semibold text-slate-900">{{ $quiz->lesson?->module?->title ?? "Noma'lum modul" }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Bog'liq dars</p>
                            <p class="mt-1 font-semibold text-slate-900">{{ $quiz->lesson?->title ?? "Noma'lum dars" }}</p>
                        </div>
                    </div>

                    <a
                        href="{{ route('quiz.show', $quiz) }}"
                        class="mt-6 inline-flex items-center justify-center rounded-full bg-brand-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700"
                    >
                        Nazoratni boshlash
                    </a>
                </article>
            @endforeach
        </div>
    @endif
</section>
