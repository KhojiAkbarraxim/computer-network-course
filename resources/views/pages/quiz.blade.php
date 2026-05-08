@extends('layouts.app')

@section('title', 'Namuna nazorat | Kompyuter Tarmoqlarini O\'rganish')

@section('content')
    <section class="container-shell">
        <div
            class="grid gap-8 lg:grid-cols-[0.85fr_1.15fr]"
            x-data="{
                questions: @js($quiz['questions']),
                selected: {},
                submitted: false,
                get answeredCount() {
                    return Object.keys(this.selected).length;
                },
                get progress() {
                    return Math.round((this.answeredCount / this.questions.length) * 100);
                },
                get correctCount() {
                    return this.questions.filter((question) => Number(this.selected[question.id]) === question.answer).length;
                },
                get score() {
                    return Math.round((this.correctCount / this.questions.length) * 100);
                },
                submit() {
                    this.submitted = true;
                },
            }"
        >
            <aside class="card-surface h-fit p-6 lg:sticky lg:top-28">
                <span class="section-kicker">Namuna quiz</span>
                <h1 class="mt-5 font-display text-3xl font-semibold tracking-tight text-slate-950">{{ $quiz['title'] }}</h1>
                <p class="mt-4 text-sm leading-7 text-slate-600">{{ $quiz['description'] }}</p>

                <div class="mt-8 rounded-3xl bg-slate-50 p-5">
                    <div class="flex items-center justify-between text-sm font-semibold text-slate-600">
                        <span>To'ldirish progressi</span>
                        <span x-text="`${answeredCount}/${questions.length}`"></span>
                    </div>
                    <div class="mt-4 h-2 overflow-hidden rounded-full bg-slate-200">
                        <div class="h-full rounded-full bg-gradient-to-r from-brand-600 to-emerald-500 transition-all duration-300" :style="`width: ${progress}%`"></div>
                    </div>
                    <p class="mt-3 text-sm text-slate-500">Barcha savollar frontend darajasida ishlaydigan demo ko'rinishida berilgan.</p>
                </div>

                <div
                    class="mt-6 rounded-[1.75rem] border border-emerald-200 bg-emerald-50/80 p-5"
                    :class="submitted ? 'opacity-100' : 'opacity-70'"
                >
                    <p class="text-sm font-semibold uppercase tracking-[0.16em] text-emerald-700">{{ $quiz['result']['title'] }}</p>
                    <p class="mt-3 font-display text-4xl font-semibold text-slate-950" x-text="submitted ? `${score}%` : '--%'"></p>
                    <p class="mt-3 text-sm leading-6 text-slate-700">{{ $quiz['result']['message'] }}</p>

                    <div class="mt-4 space-y-3 text-sm leading-6 text-slate-700">
                        @foreach ($quiz['result']['highlights'] as $highlight)
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 text-emerald-600">✔</span>
                                <p>{{ $highlight }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </aside>

            <div class="space-y-6">
                <template x-for="(question, index) in questions" :key="question.id">
                    <article class="card-surface p-6 sm:p-8">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <span class="rounded-full bg-brand-50 px-3 py-1 text-sm font-semibold text-brand-700" x-text="`Savol ${index + 1}`"></span>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-600">Bir javobni tanlang</span>
                        </div>

                        <h2 class="mt-5 font-display text-2xl font-semibold text-slate-950" x-text="question.question"></h2>

                        <div class="mt-6 grid gap-3">
                            <template x-for="(option, optionIndex) in question.options" :key="optionIndex">
                                <label
                                    class="flex cursor-pointer items-start gap-4 rounded-3xl border px-4 py-4 transition"
                                    :class="selected[question.id] === optionIndex ? 'border-brand-300 bg-brand-50' : 'border-slate-200 bg-white hover:border-slate-300'"
                                >
                                    <input type="radio" class="mt-1 h-4 w-4 border-slate-300 text-brand-600 focus:ring-brand-500" :name="question.id" :value="optionIndex" x-model="selected[question.id]">
                                    <span class="text-sm leading-6 text-slate-700" x-text="option"></span>
                                </label>
                            </template>
                        </div>
                    </article>
                </template>

                <div class="card-surface flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="font-display text-2xl font-semibold text-slate-950">Natijani ko'rishga tayyormisiz?</p>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Bu bosqich faqat interfeysni ko'rsatadi. Keyinchalik backend bilan saqlash va tarix qo'shiladi.</p>
                    </div>
                    <button
                        type="button"
                        class="inline-flex items-center justify-center rounded-full bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800"
                        @click="submit()"
                    >
                        Javoblarni yakunlash
                    </button>
                </div>
            </div>
        </div>
    </section>
@endsection
