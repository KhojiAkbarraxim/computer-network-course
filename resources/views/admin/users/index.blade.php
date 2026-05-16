@extends('layouts.admin')

@section('title', "Foydalanuvchilar | Admin panel")

@section('content')
    <section class="card-surface p-8">
        <x-section-heading
            kicker="Foydalanuvchilar"
            title="Foydalanuvchilar ro'yxati"
            description="Ro'yxatdan o'tgan foydalanuvchilarni, ularning faolligini va rolini shu sahifada boshqarishingiz mumkin."
        />

        <div class="mt-8 overflow-x-auto">
            <table class="min-w-full text-left text-sm text-slate-700">
                <thead>
                    <tr class="border-b border-slate-200 text-xs uppercase tracking-[0.16em] text-slate-500">
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">Ism</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Rol</th>
                        <th class="px-4 py-3">Tugallangan darslar</th>
                        <th class="px-4 py-3">Quiz urinishlari</th>
                        <th class="px-4 py-3">O'rtacha quiz natijasi</th>
                        <th class="px-4 py-3">Ro'yxatdan o'tgan sana</th>
                        <th class="px-4 py-3">Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $managedUser)
                        <tr class="border-b border-slate-100 align-top">
                            <td class="px-4 py-4 font-semibold text-slate-900">{{ $managedUser->id }}</td>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-slate-900">{{ $managedUser->name }}</p>
                                @if ((int) auth()->id() === (int) $managedUser->id)
                                    <p class="mt-1 text-xs text-brand-700">Joriy foydalanuvchi</p>
                                @endif
                            </td>
                            <td class="px-4 py-4">{{ $managedUser->email }}</td>
                            <td class="px-4 py-4">
                                <span class="{{ $managedUser->is_admin ? 'bg-brand-100 text-brand-700' : 'bg-slate-100 text-slate-600' }} rounded-full px-3 py-1 text-xs font-semibold">
                                    {{ $managedUser->is_admin ? 'Admin' : 'Oddiy foydalanuvchi' }}
                                </span>
                            </td>
                            <td class="px-4 py-4">{{ $managedUser->completed_lessons_count }}</td>
                            <td class="px-4 py-4">{{ $managedUser->quiz_attempts_count }}</td>
                            <td class="px-4 py-4">{{ $managedUser->average_quiz_score !== null ? number_format((float) $managedUser->average_quiz_score, 0).'%' : '0%' }}</td>
                            <td class="px-4 py-4">{{ $managedUser->created_at?->format('d.m.Y H:i') }}</td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('admin.users.show', $managedUser) }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:border-brand-200 hover:text-brand-700">
                                        Ko'rish
                                    </a>
                                    <a href="{{ route('admin.users.edit', $managedUser) }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:border-brand-200 hover:text-brand-700">
                                        Tahrirlash
                                    </a>
                                    @if ((int) auth()->id() === (int) $managedUser->id)
                                        <span class="inline-flex items-center justify-center rounded-full bg-slate-200 px-4 py-2 text-xs font-semibold text-slate-500">
                                            O'chirish
                                        </span>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.destroy', $managedUser) }}" onsubmit="return confirm('Foydalanuvchini o\\'chirishni tasdiqlaysizmi?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-rose-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-rose-700">
                                                O'chirish
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-10 text-center text-sm text-slate-500">
                                Hozircha ma'lumot mavjud emas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
