@extends('layouts.admin')

@section('title', "Modullar | Admin panel")

@section('content')
    <section class="card-surface p-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <x-section-heading
                kicker="Modullar"
                title="Modullar ro'yxati"
                description="Kurs tarkibidagi modullarni tartiblash, tahrirlash yoki o'chirish uchun ushbu jadvaldan foydalaning."
            />

            <a href="{{ route('admin.modules.create') }}" class="inline-flex items-center justify-center rounded-full bg-brand-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700">
                Yangi modul qo'shish
            </a>
        </div>

        <div class="mt-8 overflow-x-auto">
            <table class="min-w-full text-left text-sm text-slate-700">
                <thead>
                    <tr class="border-b border-slate-200 text-xs uppercase tracking-[0.16em] text-slate-500">
                        <th class="px-4 py-3">Kurs</th>
                        <th class="px-4 py-3">Tartib</th>
                        <th class="px-4 py-3">Sarlavha</th>
                        <th class="px-4 py-3">Davomiylik</th>
                        <th class="px-4 py-3">Holat</th>
                        <th class="px-4 py-3">Darslar</th>
                        <th class="px-4 py-3">Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($modules as $module)
                        <tr class="border-b border-slate-100 align-top">
                            <td class="px-4 py-4">
                                <p class="font-semibold text-slate-900">{{ $module->course?->title }}</p>
                            </td>
                            <td class="px-4 py-4">{{ $module->sort_order }}</td>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-slate-900">{{ $module->title }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $module->slug }}</p>
                            </td>
                            <td class="px-4 py-4">{{ $module->estimated_duration_minutes ? $module->estimated_duration_minutes.' daqiqa' : "Kiritilmagan" }}</td>
                            <td class="px-4 py-4">
                                <span class="{{ $module->is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }} rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em]">
                                    {{ $module->is_published ? 'Faol' : 'Nofaol' }}
                                </span>
                            </td>
                            <td class="px-4 py-4">{{ $module->lessons_count }}</td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('admin.modules.edit', $module) }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:border-brand-200 hover:text-brand-700">
                                        Tahrirlash
                                    </a>
                                    <form method="POST" action="{{ route('admin.modules.destroy', $module) }}" onsubmit="return confirm('Modulni o\\'chirishni tasdiqlaysizmi?')">
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
                                Hozircha modul mavjud emas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
