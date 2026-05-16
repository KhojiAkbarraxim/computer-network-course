@extends('layouts.admin')

@section('title', "Yangi nazorat qo'shish | Admin panel")

@section('content')
    <section class="card-surface p-8">
        <x-section-heading
            kicker="Nazoratlar"
            title="Yangi nazorat qo'shish"
            description="Darsga yangi nazorat biriktirish uchun quyidagi maydonlarni to'ldiring."
        />

        <form method="POST" action="{{ route('admin.quizzes.store') }}" class="mt-8">
            @csrf
            @include('admin.quizzes._form')
        </form>
    </section>
@endsection
