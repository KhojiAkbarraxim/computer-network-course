@extends('layouts.app')

@section('title', 'Loyiha haqida | Kompyuter Tarmoqlarini O\'rganish')

@section('content')
    <section class="container-shell">
        <div class="rounded-[2rem] bg-white/90 p-8 shadow-[0_28px_70px_-32px_rgba(15,23,42,0.28)] sm:p-10">
            <x-section-heading
                kicker="Loyiha haqida"
                title="Nima uchun bu platforma yaratilmoqda?"
                description="{{ $about['purpose'] }}"
            />

            <div class="mt-10 grid gap-6 lg:grid-cols-3">
                @foreach ($about['steps'] as $step)
                    <div class="card-surface p-6">
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-700">Bosqich</p>
                        <h2 class="mt-4 font-display text-2xl font-semibold text-slate-950">{{ $step['title'] }}</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-600">{{ $step['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="container-shell mt-12 grid gap-6 xl:grid-cols-3">
        <div class="card-surface p-6 xl:col-span-1">
            <x-section-heading
                kicker="Metodologiya"
                title="O'qitish yondashuvi"
                description="Platforma murakkab mavzuni soddalashtirishga qaratilgan, lekin kasbiy mantiqni saqlab qoladi."
            />

            <div class="mt-6 space-y-4">
                @foreach ($about['methodology'] as $item)
                    <div class="flex items-start gap-3 rounded-2xl bg-slate-50 px-4 py-4">
                        <span class="mt-1 text-brand-600">-</span>
                        <p class="text-sm leading-6 text-slate-700">{{ $item }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card-surface p-6 xl:col-span-1">
            <x-section-heading
                kicker="Kimlar uchun?"
                title="Maqsadli foydalanuvchilar"
                description="Mazmun va interfeys yangi boshlovchi foydalanuvchi uchun qo'rqinchli bo'lmasligi uchun soddalashtirilgan."
            />

            <div class="mt-6 space-y-4">
                @foreach ($about['audiences'] as $audience)
                    <div class="rounded-2xl border border-slate-200 bg-white px-4 py-4 text-sm leading-6 text-slate-700">
                        {{ $audience }}
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card-surface p-6 xl:col-span-1">
            <x-section-heading
                kicker="Kutilayotgan natija"
                title="O'quvchi nimani qo'lga kiritadi?"
                description="Kurs yakunida foydalanuvchi bazaviy tarmoq bilimlarini amaliy mantiq bilan bog'lay oladi."
            />

            <div class="mt-6 space-y-4">
                @foreach ($about['outcomes'] as $outcome)
                    <div class="rounded-2xl bg-emerald-50 px-4 py-4 text-sm leading-6 text-slate-700">
                        {{ $outcome }}
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
