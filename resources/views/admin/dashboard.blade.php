@extends('layouts.admin')

@section('title', "Admin panel | Kompyuter Tarmoqlarini O'rganish")

@section('content')
    <section class="card-surface p-8">
        <x-section-heading
            kicker="Admin panel"
            title="Kontent boshqaruvi bo'yicha umumiy ko'rinish"
            description="Asosiy statistikalar, tezkor amallar va oxirgi faoliyat shu sahifada jamlangan."
        />

        <div class="mt-8 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($stats as $item)
                <x-stat-card :value="$item['value']" :label="$item['label']" :icon="$item['icon']" />
            @endforeach
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        <div class="card-surface p-8">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="section-kicker">Tezkor amallar</p>
                    <h2 class="mt-3 font-display text-2xl font-semibold tracking-tight text-slate-950">
                        Eng ko'p ishlatiladigan bo'limlar
                    </h2>
                </div>
            </div>

            <div class="mt-8 grid gap-4 md:grid-cols-2">
                <a href="{{ route('admin.modules.create') }}" class="rounded-3xl border border-brand-200 bg-brand-50 px-5 py-5 transition hover:border-brand-300 hover:bg-brand-100">
                    <p class="text-sm font-semibold text-brand-700">Modul qo'shish</p>
                    <p class="mt-2 text-sm text-slate-600">Kurs tarkibiga yangi modul yarating.</p>
                </a>
                <a href="{{ route('admin.lessons.create') }}" class="rounded-3xl border border-brand-200 bg-brand-50 px-5 py-5 transition hover:border-brand-300 hover:bg-brand-100">
                    <p class="text-sm font-semibold text-brand-700">Dars qo'shish</p>
                    <p class="mt-2 text-sm text-slate-600">Mavjud modulga yangi dars biriktiring.</p>
                </a>
                <a href="{{ route('admin.quizzes.create') }}" class="rounded-3xl border border-brand-200 bg-brand-50 px-5 py-5 transition hover:border-brand-300 hover:bg-brand-100">
                    <p class="text-sm font-semibold text-brand-700">Nazorat qo'shish</p>
                    <p class="mt-2 text-sm text-slate-600">Darsga yangi nazorat biriktiring.</p>
                </a>
                <a href="{{ route('admin.modules.index') }}" class="rounded-3xl border border-slate-200 bg-white px-5 py-5 transition hover:border-brand-200 hover:text-brand-700">
                    <p class="text-sm font-semibold text-slate-900">Modullarni boshqarish</p>
                    <p class="mt-2 text-sm text-slate-600">Barcha modullarni ko'rish va tahrirlash.</p>
                </a>
                <a href="{{ route('admin.lessons.index') }}" class="rounded-3xl border border-slate-200 bg-white px-5 py-5 transition hover:border-brand-200 hover:text-brand-700">
                    <p class="text-sm font-semibold text-slate-900">Darslarni boshqarish</p>
                    <p class="mt-2 text-sm text-slate-600">Darslarni tahrirlash yoki o'chirish.</p>
                </a>
                <a href="{{ route('admin.quizzes.index') }}" class="rounded-3xl border border-slate-200 bg-white px-5 py-5 transition hover:border-brand-200 hover:text-brand-700">
                    <p class="text-sm font-semibold text-slate-900">Nazoratlarni boshqarish</p>
                    <p class="mt-2 text-sm text-slate-600">Nazoratlar, savollar va javoblarni boshqaring.</p>
                </a>
                <a href="{{ route('admin.users.index') }}" class="rounded-3xl border border-slate-200 bg-white px-5 py-5 transition hover:border-brand-200 hover:text-brand-700">
                    <p class="text-sm font-semibold text-slate-900">Foydalanuvchilarni boshqarish</p>
                    <p class="mt-2 text-sm text-slate-600">Ro'yxatdan o'tgan foydalanuvchilarni ko'ring va tahrirlang.</p>
                </a>
            </div>

            <a href="{{ route('home') }}" class="mt-6 inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-brand-200 hover:text-brand-700">
                Saytga qaytish
            </a>
        </div>

        <div class="card-surface p-8">
            <p class="section-kicker">Faollik</p>
            <h2 class="mt-3 font-display text-2xl font-semibold tracking-tight text-slate-950">
                Oxirgi tugatilgan darslar
            </h2>

            @if ($recentCompletedLessons->isEmpty())
                <p class="mt-6 rounded-3xl border border-dashed border-slate-200 bg-slate-50 px-5 py-4 text-sm text-slate-500">
                    Hozircha ma'lumot mavjud emas.
                </p>
            @else
                <div class="mt-6 space-y-3">
                    @foreach ($recentCompletedLessons as $progress)
                        <div class="rounded-3xl border border-slate-200 bg-white px-5 py-4">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $progress->user?->name ?? "Noma'lum foydalanuvchi" }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $progress->lesson?->title ?? "Noma'lum dars" }}</p>
                                </div>
                                <p class="text-xs font-semibold text-slate-500">{{ $progress->completed_at?->format('d.m.Y H:i') ?? "Vaqt ma'lum emas" }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-3">
        <div class="card-surface p-8">
            <p class="section-kicker">Quiz tahlili</p>
            <h2 class="mt-3 font-display text-2xl font-semibold tracking-tight text-slate-950">
                Oxirgi quiz urinishlari
            </h2>

            @if ($recentAttempts->isEmpty())
                <p class="mt-6 rounded-3xl border border-dashed border-slate-200 bg-slate-50 px-5 py-4 text-sm text-slate-500">
                    Hozircha ma'lumot mavjud emas.
                </p>
            @else
                <div class="mt-6 space-y-3">
                    @foreach ($recentAttempts as $attempt)
                        <div class="rounded-3xl border border-slate-200 bg-white px-5 py-4">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $attempt->user?->name ?? "Noma'lum foydalanuvchi" }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $attempt->quiz?->title ?? "Noma'lum nazorat" }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-brand-700">{{ $attempt->score }}%</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $attempt->submitted_at?->format('d.m.Y H:i') ?? "Vaqt ma'lum emas" }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="card-surface p-8">
            <p class="section-kicker">Foydalanuvchilar</p>
            <h2 class="mt-3 font-display text-2xl font-semibold tracking-tight text-slate-950">
                Oxirgi ro'yxatdan o'tgan foydalanuvchilar
            </h2>

            @if ($recentUsers->isEmpty())
                <p class="mt-6 rounded-3xl border border-dashed border-slate-200 bg-slate-50 px-5 py-4 text-sm text-slate-500">
                    Hozircha ma'lumot mavjud emas.
                </p>
            @else
                <div class="mt-6 space-y-3">
                    @foreach ($recentUsers as $user)
                        <div class="rounded-3xl border border-slate-200 bg-white px-5 py-4">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $user->name }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $user->email }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="rounded-full {{ $user->is_admin ? 'bg-brand-100 text-brand-700' : 'bg-slate-100 text-slate-600' }} px-3 py-1 text-xs font-semibold">
                                        {{ $user->is_admin ? 'Admin' : 'Foydalanuvchi' }}
                                    </span>
                                    <p class="mt-2 text-xs text-slate-500">{{ $user->created_at?->format('d.m.Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="card-surface p-8">
            <p class="section-kicker">Faol foydalanuvchilar</p>
            <h2 class="mt-3 font-display text-2xl font-semibold tracking-tight text-slate-950">
                Eng faol foydalanuvchilar
            </h2>

            @if ($activeUsers->isEmpty())
                <p class="mt-6 rounded-3xl border border-dashed border-slate-200 bg-slate-50 px-5 py-4 text-sm text-slate-500">
                    Hozircha ma'lumot mavjud emas.
                </p>
            @else
                <div class="mt-6 space-y-3">
                    @foreach ($activeUsers as $user)
                        <div class="rounded-3xl border border-slate-200 bg-white px-5 py-4">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $user->name }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $user->completed_lessons_count }} ta tugatilgan dars</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-brand-700">{{ $user->quiz_attempts_count }} ta quiz urinishi</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $user->email }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
