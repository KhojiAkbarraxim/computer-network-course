<x-guest-layout :page-title="'Kirish'">
    <div class="mb-6">
        <p class="section-kicker">Kirish</p>
        <h2 class="mt-4 font-display text-3xl font-semibold text-slate-950">Hisobingizga kiring</h2>
        <p class="mt-3 text-sm leading-6 text-slate-600">
            Darslarni davom ettirish va keyingi bosqichlarda o'zlashtirish tarixini saqlash uchun tizimga kiring.
        </p>
    </div>

    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="sizning@email.uz" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <div class="flex items-center justify-between gap-3">
                <x-input-label for="password" value="Parol" class="mb-0" />
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm font-semibold text-brand-700 transition hover:text-brand-800">
                        Parolni unutdingizmi?
                    </a>
                @endif
            </div>
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Parolingizni kiriting" class="mt-2" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <label for="remember_me" class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
            <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
            <span class="text-sm font-medium text-slate-700">Meni eslab qolish</span>
        </label>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ route('register') }}" class="text-sm font-semibold text-slate-600 transition hover:text-brand-700">
                Hisobingiz yo'qmi? Ro'yxatdan o'ting
            </a>
            <x-primary-button>
                Kirish
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
