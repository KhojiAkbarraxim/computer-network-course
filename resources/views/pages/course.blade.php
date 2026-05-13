<section class="container-shell">
        <div class="grid gap-8 rounded-[2rem] bg-white/90 p-8 shadow-[0_28px_70px_-32px_rgba(15,23,42,0.28)] lg:grid-cols-[1fr_0.55fr] lg:p-10">
            <div>
                <span class="section-kicker">To'liq kurs xaritasi</span>
                <h1 class="mt-5 font-display text-4xl font-semibold tracking-tight text-slate-950 sm:text-5xl">
                    Modulma-modul tuzilgan o'quv yo'li
                </h1>
                <p class="mt-5 max-w-3xl text-lg leading-8 text-slate-600">
                    {{ $course?->short_description ?: "Bu sahifa foydalanuvchiga butun kurs tuzilmasini birdan ko'rsatadi: modul raqami, qisqa mazmuni, darslar soni, murakkablik darajasi va namunaviy o'zlashtirish foizi." }}
                </p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                <div class="rounded-3xl bg-brand-50 p-5">
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-700">Umumiy modul</p>
                    <p class="mt-3 font-display text-4xl font-semibold text-slate-950">{{ $modules->count() }}</p>
                </div>
                <div class="rounded-3xl bg-emerald-50 p-5">
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-emerald-700">Yo'nalish</p>
                    <p class="mt-3 font-display text-2xl font-semibold text-slate-950">
                        {{ $course?->level_label ?: "Nazariya + amaliy laboratoriya" }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="container-shell mt-12">
        @if ($modules->isNotEmpty())
            <div class="grid gap-6 xl:grid-cols-2">
                @foreach ($modules as $module)
                    <x-module-card :module="$module" />
                @endforeach
            </div>
        @else
            <div class="card-surface rounded-[2rem] p-8 text-center">
                <p class="font-display text-2xl font-semibold text-slate-950">Hozircha kurs modullari mavjud emas.</p>
                <p class="mt-3 text-sm leading-6 text-slate-600">
                    Kurs ma'lumotlari bazaga qo'shilgach, ushbu sahifada barcha modullar avtomatik ko'rsatiladi.
                </p>
            </div>
        @endif
</section>
