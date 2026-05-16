<div class="rounded-3xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm text-slate-600">
    <p class="font-semibold text-slate-900">{{ $quiz->title }}</p>
    <p class="mt-1">{{ $quiz->lesson?->title ?? "Noma'lum dars" }}</p>
    <p class="mt-2 text-xs">Savol turi avtomatik ravishda bitta javobli nazorat sifatida saqlanadi.</p>
</div>

<div class="mt-6 grid gap-6">
    <div class="md:max-w-xs">
        <x-input-label for="sort_order" value="Tartib raqami" />
        <x-text-input id="sort_order" name="sort_order" type="number" min="1" class="mt-1" :value="old('sort_order', $question->sort_order)" required />
        <x-input-error class="mt-2" :messages="$errors->get('sort_order')" />
    </div>

    <div>
        <x-input-label for="question_text" value="Savol matni" />
        <textarea id="question_text" name="question_text" rows="6" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-brand-300 focus:ring-2 focus:ring-brand-100" required>{{ old('question_text', $question->question_text) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('question_text')" />
    </div>
</div>

<div class="mt-8 flex flex-wrap items-center gap-3">
    <x-primary-button>Saqlash</x-primary-button>
    <a href="{{ route('admin.quizzes.questions.index', $quiz) }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-brand-200 hover:text-brand-700">
        Bekor qilish
    </a>
</div>
