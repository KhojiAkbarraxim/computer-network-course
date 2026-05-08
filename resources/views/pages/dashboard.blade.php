@extends('layouts.app')

@section('title', 'O\'quv paneli | Kompyuter Tarmoqlarini O\'rganish')

@section('content')
    <section class="container-shell">
        <div class="grid gap-6 xl:grid-cols-[1fr_0.95fr]">
            <div class="card-surface overflow-hidden p-8">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <span class="section-kicker">O'quv paneli</span>
                        <h1 class="mt-5 font-display text-4xl font-semibold tracking-tight text-slate-950">
                            O'quv jarayonining umumiy holati
                        </h1>
                        <p class="mt-4 max-w-2xl text-base leading-7 text-slate-600">
                            Bu sahifa foydalanuvchi progressini, keyingi dars tavsiyasini, quiz natijalarini va modul kesimidagi o'sishni bitta ko'rinishda ko'rsatadi.
                        </p>
                    </div>
                    <a
                        href="{{ route('lesson.sample') }}"
                        class="inline-flex items-center justify-center rounded-full bg-brand-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700"
                    >
                        O'qishni davom ettirish
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
                <p class="mt-3 text-sm font-semibold text-slate-600">Ushbu modul bo'yicha progress: {{ $dashboard['continue']['progress'] }}%</p>

                <div class="mt-6 rounded-3xl bg-slate-900 p-5 text-white shadow-xl shadow-slate-900/15">
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-300">Tavsiya</p>
                    <p class="mt-3 text-sm leading-6 text-slate-300">Darsdan so'ng mini-quizni ishlang va keyin TCP/IP moduliga o'ting.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="container-shell mt-12 grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
        <div class="space-y-6">
            <div class="card-surface p-6">
                <x-section-heading
                    kicker="Tugallangan darslar"
                    title="Yaqinda yakunlangan bo'limlar"
                    description="Keyinchalik bu yer real vaqt tamg'asi, davomiylik va faoliyat tarixiga ulanadi."
                />

                <div class="mt-6 space-y-4">
                    @foreach ($dashboard['completed_lessons'] as $completedLesson)
                        <div class="flex items-center justify-between gap-4 rounded-3xl border border-slate-200 bg-slate-50 p-4">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $completedLesson['title'] }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $completedLesson['module'] }}</p>
                            </div>
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">
                                {{ $completedLesson['time'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card-surface p-6">
                <x-section-heading
                    kicker="Nazorat natijalari"
                    title="So'nggi tekshiruv kartalari"
                    description="Statik holatda baho, holat va keyingi mustahkamlash yo'nalishi ko'rsatilgan."
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
                kicker="Modul progressi"
                title="Qaysi modullar qayergacha yetgan?"
                description="Keyinchalik bu blok foydalanuvchining real progressi va unlock qilingan darslar bilan bog'lanadi."
            />

            <div class="mt-8 space-y-5">
                @foreach ($dashboard['module_progress'] as $moduleProgress)
                    <div class="rounded-3xl bg-slate-50 p-5">
                        <div class="flex items-center justify-between gap-4">
                            <p class="font-semibold text-slate-900">{{ $moduleProgress['title'] }}</p>
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
@endsection
