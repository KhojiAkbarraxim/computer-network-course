<div class="grid gap-6 md:grid-cols-2">
    <div class="md:col-span-2">
        <x-input-label for="lesson_id" value="Bog'liq dars" />
        <select id="lesson_id" name="lesson_id" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-brand-300 focus:ring-2 focus:ring-brand-100">
            <option value="">Darsni tanlang</option>
            @foreach ($lessons as $lessonOption)
                <option value="{{ $lessonOption->id }}" @selected((string) old('lesson_id', $quiz->lesson_id) === (string) $lessonOption->id)>
                    {{ $lessonOption->module?->course?->title }} • {{ $lessonOption->module?->title }} • {{ $lessonOption->title }}
                </option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('lesson_id')" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="title" value="Sarlavha" />
        <x-text-input id="title" name="title" type="text" class="mt-1" :value="old('title', $quiz->title)" required />
        <x-input-error class="mt-2" :messages="$errors->get('title')" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="description" value="Tavsif" />
        <textarea id="description" name="description" rows="5" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-brand-300 focus:ring-2 focus:ring-brand-100">{{ old('description', $quiz->description) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </div>
</div>

<label class="mt-6 flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
    <input type="checkbox" name="is_published" value="1" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500" @checked(old('is_published', $quiz->is_published ?? true))>
    <span class="text-sm font-semibold text-slate-700">Faol</span>
</label>

<div class="mt-8 flex flex-wrap items-center gap-3">
    <x-primary-button>Saqlash</x-primary-button>
    <a href="{{ route('admin.quizzes.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-brand-200 hover:text-brand-700">
        Bekor qilish
    </a>
</div>
