@extends('layouts.admin')

@section('title', "Nazoratlar | Admin panel")

@section('content')
    <section class="card-surface p-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <x-section-heading
                kicker="Nazoratlar"
                title="Nazoratlar ro'yxati"
                description="Darslarga biriktirilgan nazoratlarni shu yerda yaratish, tahrirlash va o'chirish mumkin."
            />

            <a href="{{ route('admin.quizzes.create') }}" class="inline-flex items-center justify-center rounded-full bg-brand-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700">
                Yangi nazorat qo'shish
            </a>
        </div>

        <div class="mt-8 overflow-x-auto">
            <table class="min-w-full text-left text-sm text-slate-700">
                <thead>
                    <tr class="border-b border-slate-200 text-xs uppercase tracking-[0.16em] text-slate-500">
                        <th class="px-4 py-3">Bog'liq dars</th>
                        <th class="px-4 py-3">Sarlavha</th>
                        <th class="px-4 py-3">Savollar</th>
                        <th class="px-4 py-3">Urinishlar</th>
                        <th class="px-4 py-3">Holat</th>
                        <th class="px-4 py-3">Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($quizzes as $quiz)
                        <tr class="border-b border-slate-100 align-top">
                            <td class="px-4 py-4">
                                <p class="font-semibold text-slate-900">{{ $quiz->lesson?->title ?? "Noma'lum dars" }}</p>
                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $quiz->lesson?->module?->title }}
                                    @if ($quiz->lesson?->module?->course)
                                        · {{ $quiz->lesson->module->course->title }}
                                    @endif
                                </p>
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-slate-900">{{ $quiz->title }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($quiz->description, 90) ?: "Tavsif kiritilmagan." }}</p>
                            </td>
                            <td class="px-4 py-4">{{ $quiz->questions_count }}</td>
                            <td class="px-4 py-4">{{ $quiz->quiz_attempts_count }}</td>
                            <td class="px-4 py-4">
                                <span class="{{ $quiz->is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }} rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em]">
                                    {{ $quiz->is_published ? 'Faol' : 'Nofaol' }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('admin.quizzes.questions.index', $quiz) }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:border-brand-200 hover:text-brand-700">
                                        Savollar
                                    </a>
                                    <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:border-brand-200 hover:text-brand-700">
                                        Tahrirlash
                                    </a>
                                    <form method="POST" action="{{ route('admin.quizzes.destroy', $quiz) }}" onsubmit="return confirm('Nazoratni o\'chirishni tasdiqlaysizmi?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center rounded-full bg-rose-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-rose-700">
                                            O'chirish
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">
                                Hozircha nazorat mavjud emas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
