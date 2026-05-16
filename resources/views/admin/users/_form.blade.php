<div class="grid gap-6 md:grid-cols-2">
    <div>
        <x-input-label for="name" value="Ism" />
        <x-text-input id="name" name="name" type="text" class="mt-1" :value="old('name', $user->name)" required />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="email" value="Email" />
        <x-text-input id="email" name="email" type="email" class="mt-1" :value="old('email', $user->email)" required />
        <x-input-error class="mt-2" :messages="$errors->get('email')" />
    </div>
</div>

@if ($isCurrentAdmin)
    <div class="mt-6 rounded-3xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-800">
        O'zingizning admin huquqingizni olib tashlay olmaysiz.
    </div>
    <input type="hidden" name="is_admin" value="1">
@endif

<label class="mt-6 flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
    <input
        type="checkbox"
        name="is_admin"
        value="1"
        class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500"
        @checked(old('is_admin', $user->is_admin))
        @disabled($isCurrentAdmin)
    >
    <span class="text-sm font-semibold text-slate-700">Admin huquqi</span>
</label>
<x-input-error class="mt-2" :messages="$errors->get('is_admin')" />

<div class="mt-8 flex flex-wrap items-center gap-3">
    <x-primary-button>Saqlash</x-primary-button>
    <a href="{{ route('admin.users.show', $user) }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-brand-200 hover:text-brand-700">
        Bekor qilish
    </a>
</div>
