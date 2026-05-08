@props([
    'value',
    'label',
    'icon' => '📊',
])

<div class="card-surface flex items-start gap-4 p-5">
    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-2xl">
        {{ $icon }}
    </div>
    <div>
        <p class="font-display text-3xl font-semibold text-slate-950">{{ $value }}</p>
        <p class="mt-1 text-sm leading-6 text-slate-600">{{ $label }}</p>
    </div>
</div>
