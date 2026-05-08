@extends('layouts.app')

@section('title', 'Kurs modullari | Kompyuter Tarmoqlarini O\'rganish')

@section('content')
    <section class="container-shell">
        <div class="grid gap-8 rounded-[2rem] bg-white/90 p-8 shadow-[0_28px_70px_-32px_rgba(15,23,42,0.28)] lg:grid-cols-[1fr_0.55fr] lg:p-10">
            <div>
                <span class="section-kicker">To'liq kurs xaritasi</span>
                <h1 class="mt-5 font-display text-4xl font-semibold tracking-tight text-slate-950 sm:text-5xl">
                    Modulma-modul tuzilgan o'quv yo'li
                </h1>
                <p class="mt-5 max-w-3xl text-lg leading-8 text-slate-600">
                    Bu sahifa foydalanuvchiga butun kurs tuzilmasini birdan ko'rsatadi: modul raqami, qisqa mazmuni, darslar soni, murakkablik darajasi va demo progress.
                </p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                <div class="rounded-3xl bg-brand-50 p-5">
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-700">Umumiy modul</p>
                    <p class="mt-3 font-display text-4xl font-semibold text-slate-950">{{ count($modules) }}</p>
                </div>
                <div class="rounded-3xl bg-emerald-50 p-5">
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-emerald-700">Yo'nalish</p>
                    <p class="mt-3 font-display text-2xl font-semibold text-slate-950">Nazariya + amaliy laboratoriya</p>
                </div>
            </div>
        </div>
    </section>

    <section class="container-shell mt-12">
        <div class="grid gap-6 xl:grid-cols-2">
            @foreach ($modules as $module)
                <x-module-card :module="$module" />
            @endforeach
        </div>
    </section>
@endsection
