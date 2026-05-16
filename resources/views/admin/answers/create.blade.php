@extends('layouts.admin')

@section('title', "Yangi javob qo'shish | Admin panel")

@section('content')
    <section class="card-surface p-8">
        <x-section-heading
            kicker="Javoblar"
            title="Yangi javob qo'shish"
            description="Tanlangan savol uchun yangi javob variantini yarating."
        />

        <form method="POST" action="{{ route('admin.questions.answers.store', $question) }}" class="mt-8">
            @csrf
            @include('admin.answers._form')
        </form>
    </section>
@endsection
