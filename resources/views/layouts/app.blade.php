<!DOCTYPE html>
<html lang="uz">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Kompyuter Tarmoqlarini O\'rganish')</title>
        <meta
            name="description"
            content="Kompyuter tarmoqlarini o'rganish uchun soddalashtirilgan, zamonaviy va Uzbek tilidagi o'quv platformasi demo interfeysi."
        >
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
        <div class="pointer-events-none fixed inset-x-0 top-0 -z-10 h-[32rem] bg-[radial-gradient(circle_at_top,_rgba(37,99,235,0.18),_transparent_58%)]"></div>
        <div class="pointer-events-none fixed inset-0 -z-10 bg-[linear-gradient(to_right,rgba(148,163,184,0.06)_1px,transparent_1px),linear-gradient(to_bottom,rgba(148,163,184,0.06)_1px,transparent_1px)] bg-[size:72px_72px] [mask-image:radial-gradient(circle_at_center,black_30%,transparent_92%)]"></div>

        <x-navbar />

        <main class="pb-16 pt-6 md:pt-10">
            @yield('content')
        </main>

        <x-footer />
    </body>
</html>
