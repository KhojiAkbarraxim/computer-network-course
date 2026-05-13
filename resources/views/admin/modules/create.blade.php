@extends('layouts.admin')

@section('title', "Yangi modul qo'shish | Admin panel")

@section('content')
    <section class="card-surface p-8">
        <x-section-heading
            kicker="Modullar"
            title="Yangi modul qo'shish"
            description="Kurs tarkibiga yangi modul qo'shish uchun quyidagi maydonlarni to'ldiring."
        />

        <form method="POST" action="{{ route('admin.modules.store') }}" class="mt-8">
            @csrf
            @include('admin.modules._form')
        </form>
    </section>
@endsection
