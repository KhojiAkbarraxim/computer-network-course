<section class="container-shell" x-data="{ sidebarOpen: window.innerWidth >= 1024 }" @resize.window="if (window.innerWidth >= 1024) sidebarOpen = true">
    @if (! $lesson)
        <div class="card-surface rounded-[2rem] p-8 text-center">
            <p class="font-display text-2xl font-semibold text-slate-950">Hozircha dars ma’lumotlari mavjud emas.</p>
            <p class="mt-3 text-sm leading-6 text-slate-600">
                Darslar bazaga qo'shilgach, ushbu sahifada to'liq dars matni va navigatsiya ko'rinadi.
            </p>
        </div>
    @else
        <div class="mb-6 flex items-center justify-between gap-4 lg:hidden">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-brand-700">{{ $lesson['module_label'] }}</p>
                <h1 class="font-display text-2xl font-semibold text-slate-950">{{ $lesson['module_title'] }}</h1>
            </div>
            <button
                type="button"
                class="inline-flex rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700"
                @click="sidebarOpen = ! sidebarOpen"
            >
                Darslar ro'yxati
            </button>
        </div>

        <div class="grid gap-8 lg:grid-cols-[320px_minmax(0,1fr)]">
            <aside
                class="card-surface h-fit overflow-hidden lg:sticky lg:top-28"
                x-cloak
                x-show="sidebarOpen"
                x-transition
            >
                <div class="border-b border-slate-200 p-6">
                    <p class="text-sm font-semibold uppercase tracking-[0.16em] text-brand-700">{{ $lesson['module_label'] }}</p>
                    <h2 class="mt-2 font-display text-2xl font-semibold text-slate-950">{{ $lesson['module_title'] }}</h2>
                    <div class="mt-4 h-2 overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-gradient-to-r from-brand-600 to-emerald-500" style="width: {{ $lesson['progress'] }}%"></div>
                    </div>
                    <p class="mt-2 text-sm text-slate-500">Modul o'zlashtirishi: {{ $lesson['progress'] }}%</p>
                </div>

                <div class="space-y-3 p-4">
                    @foreach ($lesson['sidebar_lessons'] as $sidebarLesson)
                        <a
                            href="{{ route('lesson.show', $sidebarLesson['id']) }}"
                            class="{{ $sidebarLesson['active'] ? 'border-brand-200 bg-brand-50 text-brand-700' : 'border-transparent bg-slate-50 text-slate-700 hover:border-slate-200 hover:bg-white' }} block rounded-2xl border px-4 py-3 transition"
                        >
                            <p class="text-sm font-semibold">{{ $sidebarLesson['title'] }}</p>
                            <p class="mt-1 text-xs uppercase tracking-[0.16em] text-slate-500">{{ $sidebarLesson['duration'] }}</p>
                        </a>
                    @endforeach
                </div>
            </aside>

            <div class="space-y-8">
                <article class="card-surface p-6 sm:p-8">
                    @if (session('status'))
                        <x-auth-session-status class="mb-5" :status="session('status')" />
                    @endif

                    <div class="flex flex-wrap items-center gap-3">
                        <span class="rounded-full bg-brand-50 px-3 py-1 text-sm font-semibold text-brand-700">{{ $lesson['module_label'] }}</span>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700">{{ $lesson['duration'] }}</span>
                    </div>

                    <h1 class="mt-5 font-display text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl">
                        {{ $lesson['current_lesson'] }}
                    </h1>

                    <div class="mt-5 flex flex-wrap items-center gap-3">
                        @auth
                            @if ($lesson['is_completed'])
                                <span class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700">
                                    Bu dars tugatilgan
                                </span>
                            @else
                                <form method="POST" action="{{ route('lesson.complete', $lesson['id']) }}">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="inline-flex rounded-full bg-brand-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700"
                                    >
                                        Darsni tugatdim
                                    </button>
                                </form>
                            @endif
                        @else
                            <p class="text-sm font-medium text-slate-500">
                                Progressni saqlash uchun tizimga kiring
                            </p>
                        @endauth
                    </div>

                    <div class="mt-6 space-y-5 text-base leading-8 text-slate-600">
                        @foreach ($lesson['paragraphs'] as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </div>

                    <div class="mt-8 rounded-[1.75rem] border border-amber-200 bg-amber-50/80 p-5">
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-2xl">
                                ⚠️
                            </div>
                            <div>
                                <p class="font-display text-xl font-semibold text-slate-950">{{ $lesson['note']['title'] }}</p>
                                <p class="mt-2 text-sm leading-6 text-slate-700">{{ $lesson['note']['text'] }}</p>
                            </div>
                        </div>
                    </div>
                </article>

                <section class="card-surface overflow-hidden p-6 sm:p-8">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="section-kicker">Tarmoq diagramma ko'rinishi</p>
                            <h2 class="mt-4 font-display text-2xl font-semibold text-slate-950">Tarmoq tushunchalarini bosqichma-bosqich ko'rish</h2>
                        </div>
                        <span class="hidden rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white sm:inline-flex">Vizual tushuntirish</span>
                    </div>

                    <div class="mt-8 rounded-[2rem] border border-dashed border-slate-300 bg-slate-50 p-6">
                        <div class="grid gap-4 md:grid-cols-3">
                            <div class="rounded-3xl bg-white p-5 shadow-sm">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-brand-700">Qadam 1</p>
                                <p class="mt-3 font-display text-xl font-semibold text-slate-950">Manbani aniqlash</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Darsdagi tarmoq elementi, qurilma yoki xizmat qayerda ishlashini tushunib oling.</p>
                            </div>
                            <div class="rounded-3xl bg-white p-5 shadow-sm">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">Qadam 2</p>
                                <p class="mt-3 font-display text-xl font-semibold text-slate-950">Aloqa oqimini ko'rish</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Ma'lumot qanday uzatilishini, qaysi bosqichlarda qayta ishlanishini ko'z oldingizga keltiring.</p>
                            </div>
                            <div class="rounded-3xl bg-white p-5 shadow-sm">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-700">Qadam 3</p>
                                <p class="mt-3 font-display text-xl font-semibold text-slate-950">Muammoni tahlil qilish</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Agar uzilish bo'lsa, qaysi qismda sabab bo'lishi mumkinligini shu blok orqali baholang.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="card-surface p-6 sm:p-8">
                    <x-section-heading
                        kicker="Asosiy terminlar"
                        title="Dars davomida yodda tutish kerak bo'lgan tushunchalar"
                        description="Bu bo'lim keyinchalik glossary yoki tezkor takrorlash bloki sifatida kengaytirilishi mumkin."
                    />

                    <div class="mt-8 grid gap-5 lg:grid-cols-2">
                        @foreach ($lesson['key_terms'] as $term)
                            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                                <p class="font-display text-xl font-semibold text-slate-950">{{ $term['term'] }}</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">{{ $term['definition'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>

                <div class="flex flex-col gap-3 sm:flex-row sm:justify-between">
                    @if ($lesson['previous'])
                        <a
                            href="{{ $lesson['previous']['url'] }}"
                            class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-brand-200 hover:text-brand-700"
                        >
                            Oldingi: {{ $lesson['previous']['title'] }}
                        </a>
                    @else
                        <a
                            href="{{ route('course') }}"
                            class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-brand-200 hover:text-brand-700"
                        >
                            Kurs modullariga qaytish
                        </a>
                    @endif

                    @if ($lesson['next'])
                        <a
                            href="{{ $lesson['next']['url'] }}"
                            class="inline-flex items-center justify-center rounded-full bg-brand-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700"
                        >
                            Keyingi: {{ $lesson['next']['title'] }}
                        </a>
                    @else
                        <a
                            href="{{ route('quiz.sample') }}"
                            class="inline-flex items-center justify-center rounded-full bg-brand-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700"
                        >
                            Keyingi: qisqa nazorat
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif
</section>
