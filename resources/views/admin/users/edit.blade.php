@extends('layouts.admin')

@section('title', "Foydalanuvchini tahrirlash | Admin panel")

@section('content')
    <section class="card-surface p-8">
        <x-section-heading
            kicker="Foydalanuvchilar"
            title="Foydalanuvchini tahrirlash"
            description="Ism, email va admin huquqini shu sahifada yangilashingiz mumkin."
        />

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="mt-8">
            @csrf
            @method('PATCH')

            @include('admin.users._form', ['user' => $user, 'isCurrentAdmin' => $isCurrentAdmin])
        </form>
    </section>
@endsection
