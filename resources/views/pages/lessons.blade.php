<section class="container-shell">
    <div class="rounded-[2rem] bg-white/90 p-8 shadow-[0_28px_70px_-32px_rgba(15,23,42,0.28)] lg:p-10">
        <span class="section-kicker">Darslar</span>
        <h1 class="mt-5 font-display text-4xl font-semibold tracking-tight text-slate-950 sm:text-5xl">
            Barcha darslar
        </h1>
        <p class="mt-5 max-w-3xl text-lg leading-8 text-slate-600">
            Barcha e'lon qilingan darslar modul bo'yicha tartiblangan holda shu sahifada jamlandi.
        </p>

        <div class="mt-8">
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-500">Modul bo'yicha saralash</p>
            <div class="mt-4 flex flex-wrap gap-3">
                <a
                    href="{{ route('lessons.index') }}"
                    class="{{ $selectedModule ? 'border-slate-200 bg-white text-slate-700 hover:border-brand-200 hover:text-brand-700' : 'border-brand-200 bg-brand-50 text-brand-700' }} inline-flex rounded-full border px-4 py-2.5 text-sm font-semibold transition"
                >
                    Barchasi
                </a>

                @foreach ($modules as $module)
                    <a
                        href="{{ route('lessons.index', ['module' => $module->id]) }}"
                        class="{{ $selectedModule?->id === $module->id ? 'border-brand-200 bg-brand-50 text-brand-700' : 'border-slate-200 bg-white text-slate-700 hover:border-brand-200 hover:text-brand-700' }} inline-flex rounded-full border px-4 py-2.5 text-sm font-semibold transition"
                    >
                        {{ $module->title }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="container-shell mt-12">
    @if ($lessonGroups)
        <div class="space-y-10">
            @foreach ($lessonGroups as $group)
                <div class="space-y-5">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-700">
                                MODUL {{ str_pad((string) $group['module']['sort_order'], 2, '0', STR_PAD_LEFT) }}
                            </p>
                            <h2 class="mt-2 font-display text-3xl font-semibold text-slate-950">
                                {{ $group['module']['title'] }}
                            </h2>
                        </div>
                        <span class="rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700">
                            {{ count($group['lessons']) }} ta dars
                        </span>
                    </div>

                    <div class="grid gap-6 xl:grid-cols-2">
                        @foreach ($group['lessons'] as $lesson)
                            @php
                                $statusClasses = match ($lesson['status_variant']) {
                                    'completed' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                                    'not-started' => 'border-amber-200 bg-amber-50 text-amber-700',
                                    default => 'border-slate-200 bg-slate-50 text-slate-600',
                                };

                                $quizClasses = $lesson['quiz_variant'] === 'available'
                                    ? 'border-brand-200 bg-brand-50 text-brand-700'
                                    : 'border-slate-200 bg-slate-50 text-slate-600';
                            @endphp

                            <article class="card-surface h-full p-6">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold uppercase tracking-[0.16em] text-slate-500">
                                            {{ $lesson['module_title'] }}
                                        </p>
                                        <h3 class="mt-2 font-display text-2xl font-semibold text-slate-950">
                                            {{ $lesson['title'] }}
                                        </h3>
                                    </div>

                                    @if ($lesson['duration'])
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700">
                                            {{ $lesson['duration'] }}
                                        </span>
                                    @endif
                                </div>

                                <p class="mt-4 text-sm leading-6 text-slate-600">
                                    {{ $lesson['preview'] }}
                                </p>

                                <div class="mt-5 flex flex-wrap gap-3">
                                    <span class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold {{ $statusClasses }}">
                                        {{ $lesson['status'] }}
                                    </span>
                                    <span class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold {{ $quizClasses }}">
                                        {{ $lesson['quiz_status'] }}
                                    </span>
                                </div>

                                <div class="mt-6">
                                    <a
                                        href="{{ $lesson['url'] }}"
                                        class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800"
                                    >
                                        Darsni boshlash
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card-surface rounded-[2rem] p-8 text-center">
            <p class="font-display text-2xl font-semibold text-slate-950">Hozircha darslar mavjud emas</p>
            <p class="mt-3 text-sm leading-6 text-slate-600">
                Baza orqali e'lon qilingan darslar paydo bo'lgach, ular shu yerda ko'rinadi.
            </p>
        </div>
    @endif
</section>
