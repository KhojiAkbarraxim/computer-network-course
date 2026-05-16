@extends('layouts.admin')

@section('title', "Yangi savol qo'shish | Admin panel")

@section('content')
    <section class="card-surface p-8">
        <x-section-heading
            kicker="Savollar"
            title="Yangi savol qo'shish"
            description="Tanlangan nazorat uchun yangi savol yarating."
        />

        <form method="POST" action="{{ route('admin.quizzes.questions.store', $quiz) }}" class="mt-8">
            @csrf
            @include('admin.questions._form')
        </form>
    </section>
@endsection
