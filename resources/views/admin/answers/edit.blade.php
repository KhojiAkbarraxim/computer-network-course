@extends('layouts.admin')

@section('title', "Javobni tahrirlash | Admin panel")

@section('content')
    <section class="card-surface p-8">
        <x-section-heading
            kicker="Javoblar"
            title="Javobni tahrirlash"
            description="Tanlangan javob ma'lumotlarini yangilang va saqlang."
        />

        <form method="POST" action="{{ route('admin.answers.update', $answer) }}" class="mt-8">
            @csrf
            @method('PUT')
            @include('admin.answers._form')
        </form>
    </section>
@endsection
