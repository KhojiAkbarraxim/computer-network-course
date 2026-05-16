@extends('layouts.admin')

@section('title', "Savolni tahrirlash | Admin panel")

@section('content')
    <section class="card-surface p-8">
        <x-section-heading
            kicker="Savollar"
            title="Savolni tahrirlash"
            description="Tanlangan savol ma'lumotlarini yangilang va saqlang."
        />

        <form method="POST" action="{{ route('admin.questions.update', $question) }}" class="mt-8">
            @csrf
            @method('PUT')
            @include('admin.questions._form')
        </form>
    </section>
@endsection
