@extends('layouts.admin')

@section('title', "Admin panel | Kompyuter Tarmoqlarini O'rganish")

@section('content')
    <section class="card-surface p-8">
        <x-section-heading
            kicker="Admin panel"
            title="Kontent boshqaruvi bo'yicha umumiy ko'rinish"
            description="Kurs, modul, dars va foydalanuvchilar bo'yicha asosiy sonlar shu sahifada ko'rsatiladi."
        />

        <div class="mt-8 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($stats as $item)
                <x-stat-card :value="$item['value']" :label="$item['label']" :icon="$item['icon']" />
            @endforeach
        </div>
    </section>
@endsection
