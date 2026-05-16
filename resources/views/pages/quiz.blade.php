@php
    $initialSelected = collect($quiz['questions'] ?? [])
        ->mapWithKeys(function (array $question): array {
            $selected = old("answers.{$question['id']}");

            return $selected ? [$question['id'] => (string) $selected] : [];
        })
        ->all();

    $showRetakeForm = old('answers') !== null || empty($quiz['latest_attempt']);
@endphp

<section class="container-shell">
    @if (! $quiz)
        <div class="card-surface rounded-[2rem] p-8 text-center">
            <p class="font-display text-2xl font-semibold text-slate-950">Hozircha nazoratlar mavjud emas.</p>
            <p class="mt-3 text-sm leading-6 text-slate-600">
                Nazorat ma'lumotlari bazaga qo'shilgach, ushbu sahifada savollar va variantlar avtomatik ko'rsatiladi.
            </p>
            <a
                href="{{ route('quizzes.index') }}"
                class="mt-6 inline-flex items-center justify-center rounded-full bg-brand-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700"
            >
                Nazoratlar ro'yxatiga o'tish
            </a>
        </div>
    @else
        <div
            class="grid gap-8 lg:grid-cols-[0.85fr_1.15fr]"
            x-data="{
                totalQuestions: {{ count($quiz['questions']) }},
                selected: @js($initialSelected),
                showRetakeForm: @js($showRetakeForm),
                get answeredCount() {
                    return Object.keys(this.selected).length;
                },
                get progress() {
                    return this.totalQuestions === 0 ? 0 : Math.round((this.answeredCount / this.totalQuestions) * 100);
                },
                resetRetake() {
                    this.selected = {};
                    this.showRetakeForm = true;
                },
            }"
        >
            <aside class="card-surface h-fit p-6 lg:sticky lg:top-28">
                <span class="section-kicker">Nazorat</span>
                <h1 class="mt-5 font-display text-3xl font-semibold tracking-tight text-slate-950">{{ $quiz['title'] }}</h1>
                <p class="mt-4 text-sm leading-7 text-slate-600">{{ $quiz['description'] }}</p>

                @if (! empty($quiz['module_title']) || ! empty($quiz['lesson_title']))
                    <div class="mt-6 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-500">Bog'liq dars</p>
                        @if (! empty($quiz['module_title']))
                            <p class="mt-3 font-display text-xl font-semibold text-slate-950">{{ $quiz['module_title'] }}</p>
                        @endif
                        @if (! empty($quiz['lesson_title']))
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $quiz['lesson_title'] }}</p>
                        @endif
                    </div>
                @endif

                <div class="mt-8 rounded-3xl bg-slate-50 p-5">
                    <div class="flex items-center justify-between text-sm font-semibold text-slate-600">
                        <span>To'ldirish holati</span>
                        <span x-text="`${answeredCount}/${totalQuestions}`"></span>
                    </div>
                    <div class="mt-4 h-2 overflow-hidden rounded-full bg-slate-200">
                        <div class="h-full rounded-full bg-gradient-to-r from-brand-600 to-emerald-500 transition-all duration-300" :style="`width: ${progress}%`"></div>
                    </div>
                    <p class="mt-3 text-sm text-slate-500">
                        Savollarni ko'rib chiqing, so'ng natijani yuboring yoki qayta urinish rejimida yana yechib ko'ring.
                    </p>
                </div>

                <div id="natija" class="mt-6 rounded-[1.75rem] border border-emerald-200 bg-emerald-50/80 p-5">
                    <p class="text-sm font-semibold uppercase tracking-[0.16em] text-emerald-700">Natija kartasi</p>

                    @if ($quiz['latest_attempt'])
                        <p class="mt-3 font-display text-4xl font-semibold text-slate-950">{{ $quiz['latest_attempt']['score'] }}%</p>
                        <div class="mt-4 space-y-3 text-sm leading-6 text-slate-700">
                            <div class="flex items-start justify-between gap-4">
                                <span>Umumiy savollar</span>
                                <span class="font-semibold text-slate-900">{{ $quiz['latest_attempt']['total_questions'] }}</span>
                            </div>
                            <div class="flex items-start justify-between gap-4">
                                <span>To'g'ri javoblar</span>
                                <span class="font-semibold text-slate-900">{{ $quiz['latest_attempt']['correct_answers'] }}</span>
                            </div>
                            <div class="flex items-start justify-between gap-4">
                                <span>Noto'g'ri javoblar</span>
                                <span class="font-semibold text-slate-900">{{ $quiz['latest_attempt']['wrong_answers'] }}</span>
                            </div>
                            <div class="flex items-start justify-between gap-4">
                                <span>Natija foizi</span>
                                <span class="font-semibold text-slate-900">{{ $quiz['latest_attempt']['score'] }}%</span>
                            </div>
                            <div class="flex items-start justify-between gap-4">
                                <span>Oxirgi urinish</span>
                                <span class="font-semibold text-slate-900">{{ $quiz['latest_attempt']['submitted_at'] }}</span>
                            </div>
                        </div>

                        <button
                            type="button"
                            class="mt-5 inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800"
                            @click="resetRetake()"
                        >
                            Qayta urinish
                        </button>
                    @elseif (! $quiz['has_questions'])
                        <p class="mt-3 text-sm leading-6 text-slate-700">Hozircha savollar mavjud emas.</p>
                    @else
                        <p class="mt-3 text-sm leading-6 text-slate-700">
                            Savollarga javob berib, <span class="font-semibold text-slate-900">Natijani yuborish</span> tugmasini bosing. So'nggi urinish natijasi shu yerda ko'rsatiladi.
                        </p>
                    @endif
                </div>
            </aside>

            <div class="space-y-6">
                @if (session('status') === 'quiz-submitted')
                    <div class="card-surface rounded-[2rem] border border-emerald-200 bg-emerald-50/80 p-5 text-sm font-semibold text-emerald-700">
                        Natija saqlandi. Oxirgi urinish kartasi va javoblar tahlili yangilandi.
                    </div>
                @elseif (session('status') === 'quiz-empty')
                    <div class="card-surface rounded-[2rem] border border-amber-200 bg-amber-50/80 p-5 text-sm font-semibold text-amber-700">
                        Hozircha savollar mavjud emas.
                    </div>
                @endif

                @if ($quiz['latest_attempt'])
                    <div class="card-surface p-6">
                        <x-section-heading
                            kicker="Javoblar tahlili"
                            title="Oxirgi urinish bo'yicha tahlil"
                            description="Har bir savol bo'yicha tanlovingiz va to'g'ri javob shu yerda ko'rsatiladi."
                        />

                        <div class="mt-6 space-y-4">
                            @foreach ($quiz['latest_attempt']['reviews'] as $index => $review)
                                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <p class="font-semibold text-slate-900">Savol {{ $index + 1 }}</p>
                                        <span class="{{ $review['is_correct'] ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }} rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em]">
                                            {{ $review['is_correct'] ? "To'g'ri" : "Noto'g'ri" }}
                                        </span>
                                    </div>
                                    <p class="mt-4 text-base font-semibold leading-7 text-slate-900">{{ $review['question'] }}</p>

                                    <div class="mt-5 grid gap-3">
                                        <div class="rounded-2xl bg-white p-4">
                                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Siz tanlagan javob</p>
                                            <p class="mt-2 text-sm leading-6 text-slate-700">{{ $review['selected_answer'] }}</p>
                                        </div>

                                        @if (! $review['is_correct'])
                                            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-emerald-700">To'g'ri javob</p>
                                                <p class="mt-2 text-sm leading-6 text-slate-700">{{ $review['correct_answer'] }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @if ($quiz['has_previous_attempts'])
                        <div class="card-surface p-6">
                            <x-section-heading
                                kicker="Urinishlar tarixi"
                                title="Oldingi natijalar"
                                description="So'nggi urinishlar eng yangisidan boshlab ko'rsatiladi."
                            />

                            <div class="mt-6 space-y-3">
                                @foreach ($quiz['attempt_history'] as $historyIndex => $attempt)
                                    <div class="flex flex-wrap items-center justify-between gap-4 rounded-3xl border border-slate-200 bg-white p-4">
                                        <div>
                                            <p class="font-semibold text-slate-900">
                                                {{ $historyIndex === 0 ? "Oxirgi urinish" : "Urinish ".($historyIndex + 1) }}
                                            </p>
                                            <p class="mt-1 text-sm text-slate-500">{{ $attempt['submitted_at'] }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-semibold text-slate-900">{{ $attempt['score'] }}%</p>
                                            <p class="mt-1 text-sm text-slate-500">{{ $attempt['correct_answers'] }} / {{ $attempt['total_questions'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif

                @if (! $quiz['has_questions'])
                    <div class="card-surface rounded-[2rem] p-8 text-center">
                        <p class="font-display text-2xl font-semibold text-slate-950">Hozircha savollar mavjud emas.</p>
                        <p class="mt-3 text-sm leading-6 text-slate-600">
                            Quiz savollari bazaga qo'shilgach, shu yerda javob variantlari ko'rsatiladi.
                        </p>
                    </div>
                @else
                    <div class="card-surface border border-slate-200 p-6" :class="showRetakeForm ? 'opacity-100' : 'opacity-70'">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="section-kicker">Qayta ishlash bloki</p>
                                <h2 class="mt-4 font-display text-3xl font-semibold text-slate-950">Quizni yana yechib ko'ring</h2>
                                <p class="mt-3 text-sm leading-6 text-slate-600">
                                    Eski natija yuqorida saqlanadi. Yangi urinish shu blokda alohida yuboriladi.
                                </p>
                            </div>

                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-brand-200 hover:text-brand-700"
                                @click="showRetakeForm = !showRetakeForm"
                            >
                                Qayta urinish
                            </button>
                        </div>

                        <form
                            method="POST"
                            action="{{ route('quiz.submit', $quiz['id']) }}"
                            class="mt-6 space-y-6"
                            x-show="showRetakeForm"
                            x-transition
                        >
                            @csrf

                            @foreach ($quiz['questions'] as $index => $question)
                                <article class="rounded-[1.75rem] border border-slate-200 bg-slate-50 p-6 sm:p-8">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <span class="rounded-full bg-brand-50 px-3 py-1 text-sm font-semibold text-brand-700">
                                            Savol {{ $index + 1 }}
                                        </span>
                                        <span class="rounded-full bg-white px-3 py-1 text-sm font-semibold text-slate-600">Bir javobni tanlang</span>
                                    </div>

                                    <h2 class="mt-5 font-display text-2xl font-semibold text-slate-950">{{ $question['question'] }}</h2>
                                    <x-input-error class="mt-4" :messages="$errors->get('answers.'.$question['id'])" />

                                    <div class="mt-6 grid gap-3">
                                        @foreach ($question['options'] as $option)
                                            @php($isSelected = (string) old('answers.'.$question['id']) === (string) $option['id'])
                                            <label
                                                class="flex cursor-pointer items-start gap-4 rounded-3xl border px-4 py-4 transition"
                                                :class="selected['{{ $question['id'] }}'] === '{{ $option['id'] }}' ? 'border-brand-300 bg-brand-50' : 'border-slate-200 bg-white hover:border-slate-300'"
                                            >
                                                <input
                                                    type="radio"
                                                    class="mt-1 h-4 w-4 border-slate-300 text-brand-600 focus:ring-brand-500"
                                                    name="answers[{{ $question['id'] }}]"
                                                    value="{{ $option['id'] }}"
                                                    x-model="selected['{{ $question['id'] }}']"
                                                    @checked($isSelected)
                                                >
                                                <span class="text-sm leading-6 text-slate-700">{{ $option['text'] }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </article>
                            @endforeach

                            <div class="flex flex-col gap-4 rounded-[1.75rem] bg-slate-900 p-6 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="font-display text-2xl font-semibold text-white">Yangi natijani yuborish</p>
                                    <p class="mt-2 text-sm leading-6 text-slate-300">
                                        @auth
                                            Yangi urinish alohida saqlanadi va yuqoridagi natija kartasi yangilanadi.
                                        @else
                                            {{ $quiz['guest_message'] }}
                                        @endauth
                                    </p>
                                </div>

                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-slate-900 transition hover:bg-slate-100"
                                >
                                    Natijani yuborish
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    @endif
</section>
