<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $pageTitle }} | Kompyuter Tarmoqlarini O'rganish</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|space+grotesk:500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(59,130,246,0.12),_transparent_30%),linear-gradient(180deg,_#f8fbff_0%,_#eef4ff_45%,_#f8fafc_100%)] font-sans text-slate-900 antialiased">
        <div class="min-h-screen">
            @include('partials.navbar')

            <main class="container-shell py-10 sm:py-14">
                <div class="grid gap-8 lg:grid-cols-[0.95fr_1.05fr] lg:items-center">
                    <section class="space-y-6">
                        <span class="section-kicker">Autentifikatsiya</span>
                        <div class="space-y-4">
                            <h1 class="font-display text-4xl font-semibold tracking-tight text-slate-950 sm:text-5xl">
                                O'quv platformasiga xavfsiz kirish
                            </h1>
                            <p class="max-w-2xl text-base leading-8 text-slate-600 sm:text-lg">
                                Hisobingiz orqali darslarni davom ettirish, keyinchalik o'zlashtirish tarixini saqlash va nazorat natijalarini ko'rish imkoniyati yaratiladi.
                            </p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="card-surface p-5">
                                <p class="font-display text-xl font-semibold text-slate-950">Tizimli o'rganish</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Modullar, darslar va nazoratlar bir joyda jamlanadi.</p>
                            </div>
                            <div class="card-surface p-5">
                                <p class="font-display text-xl font-semibold text-slate-950">Keyingi bosqichga tayyor</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Autentifikatsiya hozircha sodda, keyin progress bilan ulanadi.</p>
                            </div>
                        </div>
                    </section>

                    <section class="card-surface p-6 sm:p-8">
                        {{ $slot }}
                    </section>
                </div>
            </main>

            @include('partials.footer')
        </div>
    </body>
</html>
