<footer class="border-t border-slate-200/80 bg-white/90">
    <div class="container-shell py-10">
        <div class="grid gap-8 lg:grid-cols-[1.3fr_0.7fr_0.7fr]">
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-900 text-white shadow-lg shadow-slate-900/20">
                        🧭
                    </div>
                    <div>
                        <p class="font-display text-lg font-semibold text-slate-900">Kompyuter Tarmoqlarini O'rganish</p>
                        <p class="text-sm text-slate-500">Statik demo ko'rinish, Laravel Blade va Tailwind asosida.</p>
                    </div>
                </div>
                <p class="max-w-xl text-sm leading-6 text-slate-600">
                    Ushbu loyiha kompyuter tarmoqlari mavzusini Uzbek tilida sodda, tushunarli va amaliy fikrlashga yaqin usulda o'rgatishga mo'ljallangan.
                </p>
            </div>

            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-500">Sahifalar</p>
                <div class="mt-4 space-y-3 text-sm text-slate-600">
                    <a class="block transition hover:text-brand-700" href="{{ route('home') }}">Bosh sahifa</a>
                    <a class="block transition hover:text-brand-700" href="{{ route('course') }}">Kurs modullari</a>
                    <a class="block transition hover:text-brand-700" href="{{ route('lesson.sample') }}">Namuna dars</a>
                    <a class="block transition hover:text-brand-700" href="{{ route('quiz.sample') }}">Namuna nazorat</a>
                </div>
            </div>

            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-500">Yo'nalish</p>
                <div class="mt-4 space-y-3 text-sm text-slate-600">
                    <p>Nazariya + vizual tushuntirish</p>
                    <p>Modulma-modul progress</p>
                    <p>Nazorat va amaliy laboratoriya mantig'i</p>
                </div>
            </div>
        </div>

        <div class="mt-8 flex flex-col gap-3 border-t border-slate-200 pt-6 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ now()->year }} Kompyuter Tarmoqlarini O'rganish. Demo ko'rinish.</p>
            <p>Keyingi bosqich: backend, foydalanuvchi progressi va real quiz logikasi.</p>
        </div>
    </div>
</footer>
