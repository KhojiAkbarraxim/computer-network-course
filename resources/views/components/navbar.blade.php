<nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-white/70 bg-white/85 backdrop-blur-xl">
    <div class="container-shell">
        <div class="flex items-center justify-between gap-4 py-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-brand-600 text-lg text-white shadow-lg shadow-brand-600/25">
                    🌐
                </div>
                <div>
                    <p class="font-display text-sm font-semibold uppercase tracking-[0.22em] text-brand-700">O'quv platforma</p>
                    <p class="text-base font-semibold text-slate-900">Kompyuter Tarmoqlarini O'rganish</p>
                </div>
            </a>

            <div class="hidden items-center gap-2 lg:flex">
                @php
                    $links = [
                        ['label' => 'Bosh sahifa', 'route' => 'home'],
                        ['label' => 'Kurs', 'route' => 'course'],
                        ['label' => 'Namuna dars', 'route' => 'lesson.sample'],
                        ['label' => 'Nazorat', 'route' => 'quiz.sample'],
                        ['label' => 'O\'quv paneli', 'route' => 'dashboard'],
                        ['label' => 'Loyiha haqida', 'route' => 'about'],
                    ];
                @endphp

                @foreach ($links as $link)
                    @php($active = request()->routeIs($link['route']))
                    <a
                        href="{{ route($link['route']) }}"
                        class="{{ $active ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/15' : 'text-slate-600 hover:bg-white hover:text-slate-900' }} rounded-full px-4 py-2 text-sm font-semibold transition"
                    >
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>

            <div class="flex items-center gap-3">
                <a
                    href="{{ route('course') }}"
                    class="hidden rounded-full bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700 sm:inline-flex"
                >
                    Kursni boshlash
                </a>

                <button
                    type="button"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700 lg:hidden"
                    @click="open = ! open"
                    :aria-expanded="open.toString()"
                    aria-label="Menyuni ochish"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" />
                    </svg>
                </button>
            </div>
        </div>

        <div x-cloak x-show="open" x-transition class="space-y-2 border-t border-slate-200 pb-4 pt-4 lg:hidden">
            @foreach ($links as $link)
                @php($active = request()->routeIs($link['route']))
                <a
                    href="{{ route($link['route']) }}"
                    class="{{ $active ? 'border-brand-200 bg-brand-50 text-brand-700' : 'border-slate-200 bg-white text-slate-700' }} block rounded-2xl border px-4 py-3 text-sm font-semibold"
                >
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>
    </div>
</nav>
