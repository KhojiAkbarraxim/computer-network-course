@extends('layouts.admin')

@section('title', "Savollar | Admin panel")

@section('content')
    <section class="card-surface p-8">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
            <div>
                <x-section-heading
                    kicker="Savollar"
                    title="Nazorat savollari"
                    description="Tanlangan nazorat ichidagi savollarni shu sahifadan boshqaring."
                />

                <div class="mt-6 rounded-3xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm text-slate-600">
                    <p class="font-semibold text-slate-900">{{ $quiz->title }}</p>
                    <p class="mt-1">{{ $quiz->lesson?->title ?? "Noma'lum dars" }}</p>
                    <p class="mt-1 text-xs">{{ $quiz->lesson?->module?->title }} @if ($quiz->lesson?->module?->course) · {{ $quiz->lesson->module->course->title }} @endif</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.quizzes.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-brand-200 hover:text-brand-700">
                    Nazoratlar
                </a>
                <a href="{{ route('admin.quizzes.questions.create', $quiz) }}" class="inline-flex items-center justify-center rounded-full bg-brand-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700">
                    Yangi savol qo'shish
                </a>
            </div>
        </div>

        <div class="mt-8 overflow-x-auto">
            <table class="min-w-full text-left text-sm text-slate-700">
                <thead>
                    <tr class="border-b border-slate-200 text-xs uppercase tracking-[0.16em] text-slate-500">
                        <th class="px-4 py-3">Tartib raqami</th>
                        <th class="px-4 py-3">Savol matni</th>
                        <th class="px-4 py-3">Javoblar</th>
                        <th class="px-4 py-3">Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($quiz->questions as $question)
                        <tr class="border-b border-slate-100 align-top">
                            <td class="px-4 py-4">{{ $question->sort_order }}</td>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-slate-900">{{ $question->question_text }}</p>
                            </td>
                            <td class="px-4 py-4">{{ $question->answers_count }}</td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('admin.questions.answers.index', $question) }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:border-brand-200 hover:text-brand-700">
                                        Javoblar
                                    </a>
                                    <a href="{{ route('admin.questions.edit', $question) }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:border-brand-200 hover:text-brand-700">
                                        Tahrirlash
                                    </a>
                                    <form method="POST" action="{{ route('admin.questions.destroy', $question) }}" onsubmit="return confirm('Savolni o\\'chirishni tasdiqlaysizmi?')">
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
                            <td colspan="4" class="px-4 py-10 text-center text-sm text-slate-500">
                                Hozircha savollar mavjud emas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
