<div class="rounded-3xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm text-slate-600">
    <p class="font-semibold text-slate-900">{{ $question->question_text }}</p>
    <p class="mt-1">{{ $quiz->title }}</p>
    <p class="mt-2 text-xs">Bir savolda bitta to'g'ri javob saqlanadi. Agar ushbu javobni to'g'ri deb belgilasangiz, avvalgi to'g'ri javob avtomatik ravishda yangilanadi.</p>
</div>

<div class="mt-6 grid gap-6 md:grid-cols-2">
    <div>
        <x-input-label for="sort_order" value="Tartib raqami" />
        <x-text-input id="sort_order" name="sort_order" type="number" min="1" class="mt-1" :value="old('sort_order', $answer->sort_order)" required />
        <x-input-error class="mt-2" :messages="$errors->get('sort_order')" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="answer_text" value="Javob matni" />
        <textarea id="answer_text" name="answer_text" rows="4" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-brand-300 focus:ring-2 focus:ring-brand-100" required>{{ old('answer_text', $answer->answer_text) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('answer_text')" />
    </div>
</div>

<label class="mt-6 flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
    <input type="checkbox" name="is_correct" value="1" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500" @checked(old('is_correct', $answer->is_correct ?? false))>
    <span class="text-sm font-semibold text-slate-700">To'g'ri javob</span>
</label>
<x-input-error class="mt-2" :messages="$errors->get('is_correct')" />

<div class="mt-8 flex flex-wrap items-center gap-3">
    <x-primary-button>Saqlash</x-primary-button>
    <a href="{{ route('admin.questions.answers.index', $question) }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-brand-200 hover:text-brand-700">
        Bekor qilish
    </a>
</div>
