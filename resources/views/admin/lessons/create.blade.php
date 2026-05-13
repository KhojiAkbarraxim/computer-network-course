@extends('layouts.admin')

@section('title', "Yangi dars qo'shish | Admin panel")

@section('content')
    <section class="card-surface p-8">
        <x-section-heading
            kicker="Darslar"
            title="Yangi dars qo'shish"
            description="Modul tarkibiga yangi dars qo'shish uchun quyidagi maydonlarni to'ldiring."
        />

        <form method="POST" action="{{ route('admin.lessons.store') }}" class="mt-8">
            @csrf
            @include('admin.lessons._form')
        </form>
    </section>
@endsection
