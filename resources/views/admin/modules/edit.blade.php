@extends('layouts.admin')

@section('title', "Modulni tahrirlash | Admin panel")

@section('content')
    <section class="card-surface p-8">
        <x-section-heading
            kicker="Modullar"
            title="Modulni tahrirlash"
            description="Tanlangan modul ma'lumotlarini yangilang va saqlang."
        />

        <form method="POST" action="{{ route('admin.modules.update', $module) }}" class="mt-8">
            @csrf
            @method('PUT')
            @include('admin.modules._form')
        </form>
    </section>
@endsection
