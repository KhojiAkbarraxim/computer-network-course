<x-guest-layout page-title="Ro'yxatdan o'tish">
    <div class="mb-6">
        <p class="section-kicker">Ro'yxatdan o'tish</p>
        <h2 class="mt-4 font-display text-3xl font-semibold text-slate-950">Yangi hisob yarating</h2>
        <p class="mt-3 text-sm leading-6 text-slate-600">
            Hisob yaratgach, keyingi bosqichlarda dars jarayoni va nazoratlar natijasini shaxsiy ko'rinishda boshqarish imkoniyati paydo bo'ladi.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="name" value="Ism" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Ismingizni kiriting" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="sizning@email.uz" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Parol" />
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Kamida 8 belgidan iborat parol" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Parolni tasdiqlash" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Parolni qayta kiriting" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 transition hover:text-brand-700">
                Hisobingiz bormi? Kirish
            </a>
            <x-primary-button>
                Ro'yxatdan o'tish
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
