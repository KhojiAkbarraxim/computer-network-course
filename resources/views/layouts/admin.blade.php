<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', "Admin panel | Kompyuter Tarmoqlarini O'rganish")</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|space+grotesk:500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(59,130,246,0.12),_transparent_30%),linear-gradient(180deg,_#f8fbff_0%,_#eef4ff_45%,_#f8fafc_100%)] font-sans text-slate-900 antialiased">
        <div class="min-h-screen">
            <div class="container-shell py-6">
                <div class="grid gap-6 lg:grid-cols-[280px_1fr]">
                    <aside class="card-surface h-fit p-6 lg:sticky lg:top-6">
                        <p class="section-kicker">Admin panel</p>
                        <h1 class="mt-5 font-display text-3xl font-semibold tracking-tight text-slate-950">
                            Kontent boshqaruvi
                        </h1>
                        <p class="mt-3 text-sm leading-6 text-slate-600">
                            Kurs tarkibini boshqarish uchun kerakli bo'limlarni shu yerdan oching.
                        </p>

                        <div class="mt-8 space-y-2">
                            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'border-brand-200 bg-brand-50 text-brand-700' : 'border-slate-200 bg-white text-slate-700' }} block rounded-2xl border px-4 py-3 text-sm font-semibold transition">
                                Admin panel
                            </a>
                            <a href="{{ route('admin.modules.index') }}" class="{{ request()->routeIs('admin.modules.*') ? 'border-brand-200 bg-brand-50 text-brand-700' : 'border-slate-200 bg-white text-slate-700' }} block rounded-2xl border px-4 py-3 text-sm font-semibold transition">
                                Modullar
                            </a>
                            <a href="{{ route('admin.lessons.index') }}" class="{{ request()->routeIs('admin.lessons.*') ? 'border-brand-200 bg-brand-50 text-brand-700' : 'border-slate-200 bg-white text-slate-700' }} block rounded-2xl border px-4 py-3 text-sm font-semibold transition">
                                Darslar
                            </a>
                        </div>

                        <div class="mt-8 rounded-3xl bg-slate-50 p-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Foydalanuvchi</p>
                            <p class="mt-2 text-sm font-semibold text-slate-900">{{ auth()->user()?->name }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ auth()->user()?->email }}</p>
                        </div>

                        <div class="mt-6 flex flex-col gap-3">
                            <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-brand-200 hover:text-brand-700">
                                Saytga qaytish
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-brand-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700">
                                    Chiqish
                                </button>
                            </form>
                        </div>
                    </aside>

                    <main class="space-y-6">
                        @if (session('status'))
                            <div class="rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
                                {{ session('status') }}
                            </div>
                        @endif

                        @yield('content')
                    </main>
                </div>
            </div>
        </div>
    </body>
</html>
