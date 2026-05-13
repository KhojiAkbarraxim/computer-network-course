@extends('layouts.admin')

@section('title', "Darsni tahrirlash | Admin panel")

@section('content')
    <section class="card-surface p-8">
        <x-section-heading
            kicker="Darslar"
            title="Darsni tahrirlash"
            description="Tanlangan dars ma'lumotlarini yangilang va saqlang."
        />

        <form method="POST" action="{{ route('admin.lessons.update', $lesson) }}" class="mt-8">
            @csrf
            @method('PUT')
            @include('admin.lessons._form')
        </form>
    </section>
@endsection
