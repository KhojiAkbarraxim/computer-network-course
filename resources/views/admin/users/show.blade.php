@extends('layouts.admin')

@section('title', "Foydalanuvchi ma'lumotlari | Admin panel")

@section('content')
    <section class="card-surface p-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <x-section-heading
                kicker="Foydalanuvchi ma'lumotlari"
                :title="$user->name"
                description="Foydalanuvchining roli, o'quv faolligi va so'nggi natijalari shu sahifada ko'rsatiladi."
            />

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-brand-200 hover:text-brand-700">
                    Tahrirlash
                </a>
                @if ((int) auth()->id() !== (int) $user->id)
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Foydalanuvchini o\\'chirishni tasdiqlaysizmi?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center rounded-full bg-rose-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-rose-700">
                            O'chirish
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl border border-slate-200 bg-white px-5 py-5">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Email</p>
                <p class="mt-3 text-base font-semibold text-slate-900">{{ $user->email }}</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white px-5 py-5">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Rol</p>
                <p class="mt-3 text-base font-semibold text-slate-900">{{ $user->is_admin ? 'Admin' : 'Oddiy foydalanuvchi' }}</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white px-5 py-5">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Ro'yxatdan o'tgan sana</p>
                <p class="mt-3 text-base font-semibold text-slate-900">{{ $user->created_at?->format('d.m.Y H:i') }}</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white px-5 py-5">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Tugallangan darslar</p>
                <p class="mt-3 text-base font-semibold text-slate-900">{{ $user->completed_lessons_count }}</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white px-5 py-5">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Quiz urinishlari</p>
                <p class="mt-3 text-base font-semibold text-slate-900">{{ $user->quiz_attempts_count }}</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white px-5 py-5">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Eng yaxshi natija</p>
                <p class="mt-3 text-base font-semibold text-slate-900">{{ $bestQuizScore !== null ? $bestQuizScore.'%' : '0%' }}</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white px-5 py-5">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">O'rtacha natija</p>
                <p class="mt-3 text-base font-semibold text-slate-900">{{ $averageQuizScore !== null ? number_format((float) $averageQuizScore, 0).'%' : '0%' }}</p>
            </div>
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-2">
        <div class="card-surface p-8">
            <p class="section-kicker">O'quv faoliyati</p>
            <h2 class="mt-3 font-display text-2xl font-semibold tracking-tight text-slate-950">
                Tugallangan darslar
            </h2>

            @if ($recentCompletedLessons->isEmpty())
                <p class="mt-6 rounded-3xl border border-dashed border-slate-200 bg-slate-50 px-5 py-4 text-sm text-slate-500">
                    Hozircha ma'lumot mavjud emas.
                </p>
            @else
                <div class="mt-6 space-y-3">
                    @foreach ($recentCompletedLessons as $progress)
                        <div class="rounded-3xl border border-slate-200 bg-white px-5 py-4">
                            <p class="text-sm font-semibold text-slate-900">{{ $progress->lesson?->title ?? "Noma'lum dars" }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $progress->completed_at?->format('d.m.Y H:i') ?? "Vaqt ma'lum emas" }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="card-surface p-8">
            <p class="section-kicker">Quiz natijalari</p>
            <h2 class="mt-3 font-display text-2xl font-semibold tracking-tight text-slate-950">
                So'nggi quiz urinishlari
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
                                    <p class="text-sm font-semibold text-slate-900">{{ $attempt->quiz?->title ?? "Noma'lum nazorat" }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $attempt->quiz?->lesson?->title ?? "Noma'lum dars" }}</p>
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
    </section>
@endsection
