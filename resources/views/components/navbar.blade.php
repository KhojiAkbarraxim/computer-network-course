<nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-white/70 bg-white/85 backdrop-blur-xl">
    <div class="container-shell">
        @php
            $user = Auth::user();
            $isAdmin = (bool) ($user?->is_admin);
            $links = $isAdmin
                ? [
                    ['label' => 'Admin panel', 'route' => 'admin.dashboard', 'active' => ['admin.*']],
                ]
                : [
                    ['label' => 'Bosh sahifa', 'route' => 'home', 'active' => ['home']],
                    ['label' => 'Kurs', 'route' => 'course', 'active' => ['course']],
                    ['label' => 'Darslar', 'route' => 'lessons.index', 'active' => ['lessons.index', 'lesson.sample', 'lesson.show']],
                    ['label' => 'Nazorat', 'route' => 'quizzes.index', 'active' => ['quizzes.index', 'quiz.sample', 'quiz.show']],
                    ...($user ? [['label' => 'O\'quv panel', 'route' => 'dashboard', 'active' => ['dashboard']]] : []),
                    ['label' => 'Loyiha haqida', 'route' => 'about', 'active' => ['about']],
                ];
        @endphp

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
                @foreach ($links as $link)
                    @php($active = request()->routeIs(...$link['active']))
                    <a
                        href="{{ route($link['route']) }}"
                        class="{{ $active ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/15' : 'text-slate-600 hover:bg-white hover:text-slate-900' }} rounded-full px-4 py-2 text-sm font-semibold transition"
                    >
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>

            <div class="flex items-center gap-3">
                @guest
                    <a
                        href="{{ route('login') }}"
                        class="hidden rounded-full border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-brand-200 hover:text-brand-700 sm:inline-flex"
                    >
                        Kirish
                    </a>
                    <a
                        href="{{ route('register') }}"
                        class="hidden rounded-full bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700 sm:inline-flex"
                    >
                        Ro'yxatdan o'tish
                    </a>
                @else
                    <div class="hidden items-center gap-3 sm:flex">
                        <a
                            href="{{ route('profile.edit') }}"
                            class="inline-flex rounded-full border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-brand-200 hover:text-brand-700"
                        >
                            Profil
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                type="submit"
                                class="inline-flex rounded-full bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700"
                            >
                                Chiqish
                            </button>
                        </form>
                    </div>
                @endguest

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
                @php($active = request()->routeIs(...$link['active']))
                <a
                    href="{{ route($link['route']) }}"
                    class="{{ $active ? 'border-brand-200 bg-brand-50 text-brand-700' : 'border-slate-200 bg-white text-slate-700' }} block rounded-2xl border px-4 py-3 text-sm font-semibold"
                >
                    {{ $link['label'] }}
                </a>
            @endforeach

            @guest
                <div class="grid gap-2 pt-2">
                    <a
                        href="{{ route('login') }}"
                        class="block rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700"
                    >
                        Kirish
                    </a>
                    <a
                        href="{{ route('register') }}"
                        class="block rounded-2xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white"
                    >
                        Ro'yxatdan o'tish
                    </a>
                </div>
            @else
                <a
                    href="{{ route('profile.edit') }}"
                    class="block rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-brand-200 hover:text-brand-700"
                >
                    Profil
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="block w-full rounded-2xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white"
                    >
                        Chiqish
                    </button>
                </form>
            @endguest
        </div>
    </div>
</nav>
