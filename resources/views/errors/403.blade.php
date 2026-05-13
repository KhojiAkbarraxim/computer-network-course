@extends('layouts.app')

@section('title', "Ruxsat yo'q | Kompyuter Tarmoqlarini O'rganish")

@section('content')
    <section class="container-shell">
        <div class="card-surface max-w-3xl p-8 text-center">
            <span class="section-kicker">Ruxsat cheklangan</span>
            <h1 class="mt-5 font-display text-4xl font-semibold tracking-tight text-slate-950">
                Ushbu bo'limga kirish uchun admin huquqi kerak
            </h1>
            <p class="mt-4 text-base leading-7 text-slate-600">
                Sizda admin panelni ochish uchun yetarli ruxsat mavjud emas. Kerak bo'lsa bosh sahifa yoki o'quv paneliga qayting.
            </p>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-brand-200 hover:text-brand-700">
                    Bosh sahifa
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-full bg-brand-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700">
                        O'quv paneli
                    </a>
                @endif
            </div>
        </div>
    </section>
@endsection
