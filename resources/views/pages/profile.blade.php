@extends('layouts.app')

@section('title', "Profil | Kompyuter Tarmoqlarini O'rganish")

@section('content')
    <section class="container-shell">
        <div class="card-surface overflow-hidden p-8">
            <div class="flex flex-wrap items-start justify-between gap-6">
                <div class="max-w-3xl">
                    <span class="section-kicker">Profil</span>
                    <h1 class="mt-5 font-display text-4xl font-semibold tracking-tight text-slate-950">
                        {{ $user->name }}
                    </h1>
                    <p class="mt-4 text-base leading-7 text-slate-600">
                        Shaxsiy ma'lumotlaringiz, o'quv natijalaringiz va hisob sozlamalaringiz shu sahifada jamlangan.
                    </p>
                </div>

                <div class="min-w-[260px] rounded-[28px] border border-brand-100 bg-brand-50/80 p-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-brand-700">Hisob ma'lumotlari</p>
                    <p class="mt-3 text-lg font-semibold text-slate-900">{{ $user->email }}</p>
                    <p class="mt-2 text-sm text-slate-600">Hisob yaratilgan sana: {{ $stats['created_at'] }}</p>
                </div>
            </div>

            <div class="mt-8 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                <x-stat-card :value="$stats['completed_lessons'].' ta'" label="Tugallangan darslar" icon="🎯" />
                <x-stat-card :value="$stats['total_lessons'].' ta'" label="Jami darslar" icon="📚" />
                <x-stat-card :value="$stats['progress_percentage'].'%'" label="Umumiy progress" icon="📈" />
                <x-stat-card :value="$stats['total_quiz_attempts'].' ta'" label="Quiz urinishlari" icon="📝" />
                <x-stat-card :value="$stats['best_quiz_score'].'%'" label="Eng yaxshi quiz natijasi" icon="🏆" />
                <x-stat-card :value="$stats['average_quiz_score'].'%'" label="O'rtacha quiz natijasi" icon="📊" />
                <x-stat-card :value="$stats['created_at']" label="Hisob yaratilgan sana" icon="🗓️" />
            </div>

            @unless ($stats['has_lessons'])
                <div class="mt-6 rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-5 text-sm leading-6 text-slate-600">
                    Hozircha darslar mavjud emas.
                </div>
            @endunless
        </div>
    </section>

    <section class="container-shell mt-12 grid gap-6 xl:grid-cols-2">
        <div class="card-surface p-6">
            <x-section-heading
                kicker="Shaxsiy ma'lumotlar"
                title="Asosiy hisob ma'lumotlari"
                description="Ism va email manzilingizni shu yerda yangilashingiz mumkin."
            />

            <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
                @csrf
                @method('patch')

                <div>
                    <x-input-label for="name" value="Ism" />
                    <x-text-input id="name" name="name" type="text" class="mt-1" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" name="email" type="email" class="mt-1" :value="old('email', $user->email)" required autocomplete="username" />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <x-primary-button>Saqlash</x-primary-button>

                    @if (session('status') === 'profile-updated')
                        <p class="text-sm font-semibold text-emerald-600">Ma'lumotlar saqlandi.</p>
                    @endif
                </div>
            </form>
        </div>

        <div class="card-surface p-6">
            <x-section-heading
                kicker="Parolni yangilash"
                title="Hisob xavfsizligini mustahkamlang"
                description="Parolni yangilash uchun joriy parolingizni kiriting va yangi parolni tasdiqlang."
            />

            <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
                @csrf
                @method('put')

                <div>
                    <x-input-label for="current_password" value="Joriy parol" />
                    <x-text-input id="current_password" name="current_password" type="password" class="mt-1" autocomplete="current-password" />
                    <x-input-error class="mt-2" :messages="$errors->updatePassword->get('current_password')" />
                </div>

                <div>
                    <x-input-label for="password" value="Yangi parol" />
                    <x-text-input id="password" name="password" type="password" class="mt-1" autocomplete="new-password" />
                    <x-input-error class="mt-2" :messages="$errors->updatePassword->get('password')" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" value="Parolni tasdiqlash" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1" autocomplete="new-password" />
                    <x-input-error class="mt-2" :messages="$errors->updatePassword->get('password_confirmation')" />
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <x-primary-button>Saqlash</x-primary-button>

                    @if (session('status') === 'password-updated')
                        <p class="text-sm font-semibold text-emerald-600">Parol yangilandi.</p>
                    @endif
                </div>
            </form>
        </div>
    </section>

    <section class="container-shell mt-6">
        <div class="card-surface border border-rose-100 p-6">
            <details class="group">
                <summary class="flex cursor-pointer list-none items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.16em] text-rose-600">Hisobni o'chirish</p>
                        <h2 class="mt-2 font-display text-2xl font-semibold text-slate-950">Hisobni o'chirish</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Bu amal qaytarilmaydi. Hisob o'chirilsa, tegishli ma'lumotlaringiz ham butunlay olib tashlanadi.
                        </p>
                    </div>

                    <span class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 transition group-open:bg-slate-900 group-open:text-white">
                        Ochish
                    </span>
                </summary>

                <form method="post" action="{{ route('profile.destroy') }}" class="mt-6 space-y-5 border-t border-slate-200 pt-6">
                    @csrf
                    @method('delete')

                    <div>
                        <x-input-label for="delete_password" value="Parolni tasdiqlang" />
                        <x-text-input id="delete_password" name="password" type="password" class="mt-1" autocomplete="current-password" />
                        <x-input-error class="mt-2" :messages="$errors->userDeletion->get('password')" />
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-full bg-rose-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-rose-600/20 transition hover:bg-rose-700"
                        >
                            Hisobni o'chirish
                        </button>
                    </div>
                </form>
            </details>
        </div>
    </section>
@endsection
