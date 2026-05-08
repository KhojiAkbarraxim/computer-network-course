@props([
    'module',
    'compact' => false,
])

@php
    $difficultyStyles = [
        'Boshlang\'ich' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'O\'rta' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'Yuqori' => 'bg-rose-50 text-rose-700 ring-rose-200',
    ];

    $difficultyClass = $difficultyStyles[$module['difficulty']] ?? 'bg-slate-100 text-slate-700 ring-slate-200';
@endphp

<article class="card-surface h-full overflow-hidden p-6">
    <div class="flex items-start justify-between gap-4">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <span class="inline-flex rounded-full bg-slate-900 px-3 py-1 text-xs font-semibold tracking-[0.22em] text-white">
                    MODUL {{ $module['number'] }}
                </span>
                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset {{ $difficultyClass }}">
                    {{ $module['difficulty'] }}
                </span>
            </div>
            <h3 class="font-display text-2xl font-semibold text-slate-950">{{ $module['title'] }}</h3>
        </div>
        <span class="rounded-2xl bg-brand-50 px-3 py-2 text-sm font-semibold text-brand-700">
            {{ $module['progress'] }}%
        </span>
    </div>

    <p class="mt-4 text-sm leading-6 text-slate-600">
        {{ $module['description'] }}
    </p>

    <div class="mt-5 h-2 overflow-hidden rounded-full bg-slate-100">
        <div class="h-full rounded-full bg-gradient-to-r from-brand-600 to-emerald-500" style="width: {{ $module['progress'] }}%"></div>
    </div>

    <div class="mt-5 grid gap-3 text-sm text-slate-600 {{ $compact ? 'sm:grid-cols-1' : 'sm:grid-cols-2' }}">
        <div class="rounded-2xl bg-slate-50 px-4 py-3">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Darslar</p>
            <p class="mt-1 font-semibold text-slate-900">{{ $module['lesson_count'] }} ta</p>
        </div>
        <div class="rounded-2xl bg-slate-50 px-4 py-3">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Taxminiy vaqt</p>
            <p class="mt-1 font-semibold text-slate-900">{{ $module['duration'] }}</p>
        </div>
    </div>

    @if (! $compact)
        <div class="mt-6 flex flex-wrap items-center gap-3">
            <a
                href="{{ route('lesson.sample') }}"
                class="inline-flex items-center justify-center rounded-full bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800"
            >
                Darsni ko'rish
            </a>
            <a
                href="{{ route('quiz.sample') }}"
                class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-brand-200 hover:text-brand-700"
            >
                Nazorat namunasi
            </a>
        </div>
    @endif
</article>
