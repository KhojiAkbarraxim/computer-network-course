@props([
    'kicker' => null,
    'title',
    'description' => null,
    'align' => 'left',
    'inverse' => false,
])

@php
    $alignment = $align === 'center' ? 'mx-auto max-w-3xl text-center items-center' : 'max-w-3xl';
    $titleClass = $inverse ? 'text-white' : 'text-slate-950';
    $descriptionClass = $inverse ? 'text-slate-300' : 'text-slate-600';
@endphp

<div {{ $attributes->class(['flex flex-col gap-4', $alignment]) }}>
    @if ($kicker)
        <span class="section-kicker">{{ $kicker }}</span>
    @endif

    <div class="space-y-3">
        <h2 class="font-display text-3xl font-semibold tracking-tight sm:text-4xl {{ $titleClass }}">
            {{ $title }}
        </h2>

        @if ($description)
            <p class="text-base leading-7 sm:text-lg {{ $descriptionClass }}">
                {{ $description }}
            </p>
        @endif
    </div>
</div>
