<section class="container-shell">
        <div class="grid gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
            <div class="space-y-8">
                <span class="section-kicker">O'zbek tilidagi soddalashtirilgan o'quv yo'li</span>

                <div class="space-y-5">
                    <h1 class="font-display text-4xl font-semibold tracking-tight text-slate-950 sm:text-5xl lg:text-6xl">
                        Kompyuter tarmoqlarini
                        <span class="text-brand-700">tushunarli va zamonaviy</span>
                        usulda o'rganing
                    </h1>
                    <p class="max-w-2xl text-lg leading-8 text-slate-600">
                        Platforma kompyuter tarmoqlari mavzusini bosqichma-bosqich, vizual kartalar, qisqa darslar va qisqa nazoratlar orqali o'rgatish uchun loyihalangan.
                    </p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <a
                        href="{{ route('course') }}"
                        class="inline-flex items-center justify-center rounded-full bg-brand-600 px-6 py-3.5 text-sm font-semibold text-white shadow-xl shadow-brand-600/25 transition hover:bg-brand-700"
                    >
                        Kurs modullarini ko'rish
                    </a>
                    <a
                        href="{{ route('lesson.sample') }}"
                        class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-6 py-3.5 text-sm font-semibold text-slate-700 transition hover:border-brand-200 hover:text-brand-700"
                    >
                        Namuna darsni ochish
                    </a>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($home['hero_metrics'] as $metric)
                        <div class="card-surface p-5">
                            <p class="font-display text-xl font-semibold text-slate-950">{{ $metric['title'] }}</p>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $metric['text'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="relative">
                <div class="gradient-panel card-surface overflow-hidden p-7">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-brand-700">Kurs ko'rinishi</p>
                            <p class="mt-2 font-display text-2xl font-semibold text-slate-950">Boshlovchi uchun to'liq yo'l xaritasi</p>
                        </div>
                        <div class="rounded-3xl bg-white px-4 py-3 text-center shadow-lg shadow-brand-600/10">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">O'zlashtirish</p>
                            <p class="mt-1 font-display text-3xl font-semibold text-brand-700">31%</p>
                        </div>
                    </div>

                    <div class="mt-8 grid gap-4">
                        <div class="rounded-3xl bg-slate-900 p-5 text-white shadow-2xl shadow-slate-900/15">
                            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-300">Davom etayotgan modul</p>
                            <p class="mt-3 font-display text-2xl font-semibold">OSI modeli</p>
                            <p class="mt-2 text-sm leading-6 text-slate-300">Qatlamlar vazifasi va muammoni tahlil qilish uchun foydali mantiq.</p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-3xl bg-white p-5 shadow-lg shadow-brand-600/10">
                                <p class="text-sm font-semibold text-slate-500">Qisqa nazoratlar</p>
                                <p class="mt-3 font-display text-3xl font-semibold text-slate-950">24</p>
                                <p class="mt-2 text-sm text-slate-600">Har modul oxirida bilimni tekshirish uchun.</p>
                            </div>
                            <div class="rounded-3xl bg-white p-5 shadow-lg shadow-brand-600/10">
                                <p class="text-sm font-semibold text-slate-500">Laboratoriya ishlari</p>
                                <p class="mt-3 font-display text-3xl font-semibold text-slate-950">10</p>
                                <p class="mt-2 text-sm text-slate-600">Amaliy fikrlashni yoqish uchun kichik topologiyalar.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="absolute -bottom-6 -left-4 hidden rounded-3xl border border-white/80 bg-white/90 px-5 py-4 shadow-2xl shadow-slate-900/10 md:block">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Yondashuv</p>
                    <p class="mt-2 font-display text-xl font-semibold text-slate-950">Soddalik + vizual izoh + mustahkamlash</p>
                </div>
            </div>
        </div>
    </section>

    <section class="container-shell mt-20">
        <x-section-heading
            kicker="Kurs tarkibi"
            title="Asosiy modullar orqali tarmoq fikrlashini shakllantiring"
            description="Har bir modul qisqa va aniq bo'lib, tushunchalarni ortiqcha murakkablashtirmasdan ketma-ket o'rgatadi."
        />

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            @foreach ($modules as $module)
                <x-module-card :module="$module" compact />
            @endforeach
        </div>
    </section>

    <section class="container-shell mt-20">
        <div class="grid gap-6 lg:grid-cols-3">
            @foreach ($home['benefits'] as $benefit)
                <div class="card-surface p-6">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-3xl">
                        {{ $benefit['icon'] }}
                    </div>
                    <h3 class="mt-5 font-display text-2xl font-semibold text-slate-950">{{ $benefit['title'] }}</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-600">{{ $benefit['text'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="container-shell mt-20">
        <div class="grid gap-8 rounded-[2rem] bg-slate-900 px-6 py-10 text-white shadow-2xl shadow-slate-900/20 lg:grid-cols-[0.9fr_1.1fr] lg:px-10">
            <x-section-heading
                kicker="Nega aynan tarmoqlar?"
                title="Har qanday zamonaviy tizim ortida tarmoq logikasi bor"
                description="Server tomoni, bulut, xavfsizlik yoki texnik yordam bo'lsin, tarmoq asoslarini bilish sizga muammolarni to'g'ri ko'rish imkonini beradi."
                inverse
            />

            <div class="grid gap-4">
                @foreach ($home['reasons'] as $reason)
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-sm">
                        <p class="font-display text-xl font-semibold">{{ $reason['title'] }}</p>
                        <p class="mt-2 text-sm leading-6 text-slate-300">{{ $reason['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="container-shell mt-20">
        <x-section-heading
            kicker="Qisqa statistika"
            title="Namunaviy platforma tuzilmasi"
            description="Interfeys hozircha statik ma'lumotlar bilan ishlaydi, ammo tuzilma keyingi server tomoni bosqichiga tayyorlangan."
            align="center"
        />

        <div class="mt-8 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($statistics as $stat)
                <x-stat-card :value="$stat['value']" :label="$stat['label']" :icon="$stat['icon']" />
            @endforeach
        </div>
</section>
