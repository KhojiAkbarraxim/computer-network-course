@extends('layouts.admin')

@section('title', "Darslar | Admin panel")

@section('content')
    <section class="card-surface p-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <x-section-heading
                kicker="Darslar"
                title="Darslar ro'yxati"
                description="Har bir darsni modul kesimida tahrirlash, o'chirish yoki yangi dars qo'shish uchun ushbu jadvaldan foydalaning."
            />

            <a href="{{ route('admin.lessons.create') }}" class="inline-flex items-center justify-center rounded-full bg-brand-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700">
                Yangi dars qo'shish
            </a>
        </div>

        <div class="mt-8 overflow-x-auto">
            <table class="min-w-full text-left text-sm text-slate-700">
                <thead>
                    <tr class="border-b border-slate-200 text-xs uppercase tracking-[0.16em] text-slate-500">
                        <th class="px-4 py-3">Modul</th>
                        <th class="px-4 py-3">Tartib</th>
                        <th class="px-4 py-3">Sarlavha</th>
                        <th class="px-4 py-3">Davomiylik</th>
                        <th class="px-4 py-3">Quiz</th>
                        <th class="px-4 py-3">Holat</th>
                        <th class="px-4 py-3">Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lessons as $lesson)
                        <tr class="border-b border-slate-100 align-top">
                            <td class="px-4 py-4">
                                <p class="font-semibold text-slate-900">{{ $lesson->module?->title }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $lesson->module?->course?->title }}</p>
                            </td>
                            <td class="px-4 py-4">{{ $lesson->sort_order }}</td>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-slate-900">{{ $lesson->title }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $lesson->slug }}</p>
                            </td>
                            <td class="px-4 py-4">{{ $lesson->duration_minutes ? $lesson->duration_minutes.' daqiqa' : "Kiritilmagan" }}</td>
                            <td class="px-4 py-4">{{ $lesson->quiz ? "Mavjud" : "Yo'q" }}</td>
                            <td class="px-4 py-4">
                                <span class="{{ $lesson->is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }} rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em]">
                                    {{ $lesson->is_published ? 'Faol' : 'Nofaol' }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('admin.lessons.edit', $lesson) }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:border-brand-200 hover:text-brand-700">
                                        Tahrirlash
                                    </a>
                                    <form method="POST" action="{{ route('admin.lessons.destroy', $lesson) }}" onsubmit="return confirm('Darsni o\\'chirishni tasdiqlaysizmi?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center rounded-full bg-rose-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-rose-700">
                                            O'chirish
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-500">
                                Hozircha dars mavjud emas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
