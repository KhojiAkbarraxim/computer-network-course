@if ($dashboard === null)
    <section class="container-shell">
        <div class="card-surface p-8 text-center">
            <span class="section-kicker">O'quv paneli</span>
            <h1 class="mt-5 font-display text-4xl font-semibold tracking-tight text-slate-950">
                O'quv jarayonining umumiy holati
            </h1>
            <p class="mt-4 text-base leading-7 text-slate-600">
                {{ $emptyMessage ?? "Hozircha darslar mavjud emas." }}
            </p>
            <a
                href="{{ route('course') }}"
                class="mt-6 inline-flex items-center justify-center rounded-full bg-brand-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700"
            >
                Kurs sahifasiga o'tish
            </a>
        </div>
    </section>
@else
    <section class="container-shell">
        <div class="grid gap-6 xl:grid-cols-[1fr_0.95fr]">
            <div class="card-surface overflow-hidden p-8">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <span class="section-kicker">O'quv paneli</span>
                        <h1 class="mt-5 font-display text-4xl font-semibold tracking-tight text-slate-950">
                            O'quv jarayonining umumiy holati
                        </h1>
                        <p class="mt-4 inline-flex rounded-full border border-brand-100 bg-brand-50 px-4 py-2 text-sm font-semibold text-brand-700">
                            {{ $dashboard['course_title'] }}
                        </p>
                        <p class="mt-4 max-w-2xl text-base leading-7 text-slate-600">
                            {{ $dashboard['course_summary'] }}
                        </p>
                    </div>
                    <a
                        href="{{ $dashboard['continue']['url'] }}"
                        class="inline-flex items-center justify-center rounded-full bg-brand-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700"
                    >
                        {{ $dashboard['continue']['button'] }}
                    </a>
                </div>

                <div class="mt-8 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach ($dashboard['overview'] as $item)
                        <x-stat-card :value="$item['value']" :label="$item['label']" :icon="$item['icon']" />
                    @endforeach
                </div>
            </div>

            <div class="card-surface gradient-panel p-8">
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-700">Davom etish kartasi</p>
                <h2 class="mt-4 font-display text-3xl font-semibold text-slate-950">{{ $dashboard['continue']['module'] }}</h2>
                <p class="mt-2 text-lg font-semibold text-slate-700">{{ $dashboard['continue']['lesson'] }}</p>
                <p class="mt-4 text-sm leading-7 text-slate-600">{{ $dashboard['continue']['text'] }}</p>

                <div class="mt-6 h-2 overflow-hidden rounded-full bg-white/80">
                    <div class="h-full rounded-full bg-gradient-to-r from-brand-600 to-emerald-500" style="width: {{ $dashboard['continue']['progress'] }}%"></div>
                </div>
                <p class="mt-3 text-sm font-semibold text-slate-600">Ushbu modul bo'yicha o'zlashtirish: {{ $dashboard['continue']['progress'] }}%</p>

                <div class="mt-6 rounded-3xl bg-slate-900 p-5 text-white shadow-xl shadow-slate-900/15">
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-300">{{ $dashboard['continue']['summary_title'] }}</p>
                    <p class="mt-3 text-lg font-semibold text-white">{{ $dashboard['overview'][1]['value'] }} tugallangan</p>
                    <p class="mt-2 text-sm leading-6 text-slate-300">
                        {{ $dashboard['overview'][0]['value'] }} dars ichidan {{ $dashboard['overview'][2]['value'] }} umumiy progress shakllandi.
                    </p>
                    <p class="mt-2 text-sm leading-6 text-slate-300">
                        {{ $dashboard['continue']['summary_text'] }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="container-shell mt-12 grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
        <div class="space-y-6">
            <div class="card-surface p-6">
                <x-section-heading
                    kicker="Tugallangan darslar"
                    title="Haqiqiy tugallangan darslar"
                    description="Bu ro'yxat aynan siz tugatgan darslar asosida yangilanadi."
                />

                <div class="mt-6 space-y-4">
                    @forelse ($dashboard['completed_lessons'] as $completedLesson)
                        <div class="flex items-center justify-between gap-4 rounded-3xl border border-slate-200 bg-slate-50 p-4">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $completedLesson['title'] }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $completedLesson['module'] }}</p>
                            </div>
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">
                                {{ $completedLesson['time'] }}
                            </span>
                        </div>
                    @empty
                        <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-5 text-sm leading-6 text-slate-600">
                            Hozircha biror dars tugatilmagan.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="card-surface p-6">
                <x-section-heading
                    kicker="Nazorat natijalari"
                    title="So'nggi quiz urinishi"
                    description="Bu yerda oxirgi nazorat natijasi va umumiy quiz ko'rsatkichlari ko'rsatiladi."
                />

                <div class="mt-6">
                    @if ($dashboard['quiz_summary']['latest_title'])
                        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $dashboard['quiz_summary']['latest_title'] }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $dashboard['quiz_summary']['lesson_title'] }}</p>
                                </div>
                                <span class="rounded-full bg-brand-50 px-3 py-1 text-sm font-semibold text-brand-700">
                                    {{ $dashboard['quiz_summary']['latest_score'] }}%
                                </span>
                            </div>

                            <div class="mt-5 grid gap-3 sm:grid-cols-3">
                                <div class="rounded-2xl bg-white p-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Jami urinish</p>
                                    <p class="mt-2 text-lg font-semibold text-slate-900">{{ $dashboard['quiz_summary']['total_attempts'] }} ta</p>
                                </div>
                                <div class="rounded-2xl bg-white p-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">O'rtacha natija</p>
                                    <p class="mt-2 text-lg font-semibold text-slate-900">{{ $dashboard['quiz_summary']['average_score'] }}%</p>
                                </div>
                                <div class="rounded-2xl bg-white p-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Oxirgi urinish</p>
                                    <p class="mt-2 text-lg font-semibold text-slate-900">{{ $dashboard['quiz_summary']['submitted_at'] }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-5 text-sm leading-6 text-slate-600">
                            Hozircha nazorat urinishi mavjud emas.
                        </div>
                    @endif
                </div>
            </div>

            <div class="card-surface p-6">
                <x-section-heading
                    kicker="Progress ko'rsatkichlari"
                    title="O'quv jarayoni bo'yicha umumiy ko'rinish"
                    description="Quyidagi kartalar darslar bo'yicha amaldagi natijalarni ko'rsatadi."
                />

                <div class="mt-6 grid gap-4">
                    @foreach ($dashboard['scores'] as $score)
                        <div class="rounded-3xl border border-slate-200 bg-white p-5">
                            <div class="flex items-center justify-between gap-4">
                                <p class="font-semibold text-slate-900">{{ $score['title'] }}</p>
                                <span class="rounded-full bg-brand-50 px-3 py-1 text-sm font-semibold text-brand-700">{{ $score['score'] }}</span>
                            </div>
                            <p class="mt-2 text-sm text-slate-500">{{ $score['status'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card-surface p-6">
            <x-section-heading
                kicker="Modul o'zlashtirishi"
                title="Qaysi modullar qayergacha yetgan?"
                description="Har bir modul foizi shu modul ichida tugatilgan darslar soni asosida hisoblanadi."
            />

            <div class="mt-8 space-y-5">
                @foreach ($dashboard['module_progress'] as $moduleProgress)
                    <div class="rounded-3xl bg-slate-50 p-5">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $moduleProgress['title'] }}</p>
                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $moduleProgress['completed_count'] }} / {{ $moduleProgress['lesson_count'] }} ta dars tugatilgan
                                </p>
                            </div>
                            <span class="text-sm font-semibold text-slate-500">{{ $moduleProgress['progress'] }}%</span>
                        </div>
                        <div class="mt-4 h-2 overflow-hidden rounded-full bg-slate-200">
                            <div class="h-full rounded-full bg-gradient-to-r from-brand-600 to-emerald-500" style="width: {{ $moduleProgress['progress'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
