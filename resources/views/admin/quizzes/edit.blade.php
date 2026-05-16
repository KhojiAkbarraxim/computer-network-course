@extends('layouts.admin')

@section('title', "Nazoratni tahrirlash | Admin panel")

@section('content')
    <section class="card-surface p-8">
        <x-section-heading
            kicker="Nazoratlar"
            title="Nazoratni tahrirlash"
            description="Tanlangan nazorat ma'lumotlarini yangilang va saqlang."
        />

        <form method="POST" action="{{ route('admin.quizzes.update', $quiz) }}" class="mt-8">
            @csrf
            @method('PUT')
            @include('admin.quizzes._form')
        </form>
    </section>
@endsection
