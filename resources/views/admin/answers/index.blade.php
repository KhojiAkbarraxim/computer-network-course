@extends('layouts.admin')

@section('title', "Javoblar | Admin panel")

@section('content')
    <section class="card-surface p-8">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
            <div>
                <x-section-heading
                    kicker="Javoblar"
                    title="Savol javoblari"
                    description="Tanlangan savol uchun javob variantlarini shu sahifada boshqaring."
                />

                <div class="mt-6 rounded-3xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm text-slate-600">
                    <p class="font-semibold text-slate-900">{{ $question->question_text }}</p>
                    <p class="mt-1">{{ $quiz->title }}</p>
                    <p class="mt-1 text-xs">{{ $quiz->lesson?->title ?? "Noma'lum dars" }}</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.quizzes.questions.index', $quiz) }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-brand-200 hover:text-brand-700">
                    Savollar
                </a>
                <a href="{{ route('admin.questions.answers.create', $question) }}" class="inline-flex items-center justify-center rounded-full bg-brand-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700">
                    Yangi javob qo'shish
                </a>
            </div>
        </div>

        <div class="mt-8 overflow-x-auto">
            <table class="min-w-full text-left text-sm text-slate-700">
                <thead>
                    <tr class="border-b border-slate-200 text-xs uppercase tracking-[0.16em] text-slate-500">
                        <th class="px-4 py-3">Tartib raqami</th>
                        <th class="px-4 py-3">Javob matni</th>
                        <th class="px-4 py-3">Holat</th>
                        <th class="px-4 py-3">Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($question->answers as $answer)
                        <tr class="border-b border-slate-100 align-top">
                            <td class="px-4 py-4">{{ $answer->sort_order }}</td>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-slate-900">{{ $answer->answer_text }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <span class="{{ $answer->is_correct ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }} rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em]">
                                    {{ $answer->is_correct ? "To'g'ri javob" : "Noto'g'ri javob" }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('admin.answers.edit', $answer) }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:border-brand-200 hover:text-brand-700">
                                        Tahrirlash
                                    </a>
                                    <form method="POST" action="{{ route('admin.answers.destroy', $answer) }}" onsubmit="return confirm('Javobni o\\'chirishni tasdiqlaysizmi?')">
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
                                Hozircha javoblar mavjud emas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
