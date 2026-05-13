<div class="grid gap-6 md:grid-cols-2">
    <div>
        <x-input-label for="module_id" value="Modul" />
        <select id="module_id" name="module_id" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-brand-300 focus:ring-2 focus:ring-brand-100">
            <option value="">Modulni tanlang</option>
            @foreach ($modules as $moduleOption)
                <option value="{{ $moduleOption->id }}" @selected((string) old('module_id', $lesson->module_id) === (string) $moduleOption->id)>
                    {{ $moduleOption->course?->title }} • {{ $moduleOption->sort_order }}. {{ $moduleOption->title }}
                </option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('module_id')" />
    </div>

    <div>
        <x-input-label for="sort_order" value="Tartib raqami" />
        <x-text-input id="sort_order" name="sort_order" type="number" min="1" class="mt-1" :value="old('sort_order', $lesson->sort_order)" required />
        <x-input-error class="mt-2" :messages="$errors->get('sort_order')" />
    </div>

    <div>
        <x-input-label for="title" value="Sarlavha" />
        <x-text-input id="title" name="title" type="text" class="mt-1" :value="old('title', $lesson->title)" required />
        <x-input-error class="mt-2" :messages="$errors->get('title')" />
    </div>

    <div>
        <x-input-label for="slug" value="Slug" />
        <x-text-input id="slug" name="slug" type="text" class="mt-1" :value="old('slug', $lesson->slug)" required />
        <x-input-error class="mt-2" :messages="$errors->get('slug')" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="short_description" value="Qisqacha tavsif" />
        <textarea id="short_description" name="short_description" rows="3" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-brand-300 focus:ring-2 focus:ring-brand-100">{{ old('short_description', $lesson->short_description) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('short_description')" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="content" value="Dars matni" />
        <textarea id="content" name="content" rows="8" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-brand-300 focus:ring-2 focus:ring-brand-100">{{ old('content', $lesson->content) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('content')" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="important_note" value="Muhim eslatma" />
        <textarea id="important_note" name="important_note" rows="4" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-brand-300 focus:ring-2 focus:ring-brand-100">{{ old('important_note', $importantNote) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('important_note')" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="key_terms_text" value="Kalit atamalar" />
        <textarea id="key_terms_text" name="key_terms_text" rows="5" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-brand-300 focus:ring-2 focus:ring-brand-100" placeholder="IP manzil | Tarmoqdagi qurilmani aniqlovchi manzil">{{ old('key_terms_text', $keyTermsText) }}</textarea>
        <p class="mt-2 text-xs text-slate-500">Har bir satrni `Atama | Izoh` ko'rinishida kiriting.</p>
        <x-input-error class="mt-2" :messages="$errors->get('key_terms_text')" />
    </div>

    <div>
        <x-input-label for="duration_minutes" value="Davomiylik (daqiqada)" />
        <x-text-input id="duration_minutes" name="duration_minutes" type="number" min="1" class="mt-1" :value="old('duration_minutes', $lesson->duration_minutes)" />
        <x-input-error class="mt-2" :messages="$errors->get('duration_minutes')" />
    </div>
</div>

<label class="mt-6 flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
    <input type="checkbox" name="is_published" value="1" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500" @checked(old('is_published', $lesson->is_published ?? true))>
    <span class="text-sm font-semibold text-slate-700">Faol</span>
</label>

<div class="mt-8 flex flex-wrap items-center gap-3">
    <x-primary-button>Saqlash</x-primary-button>
    <a href="{{ route('admin.lessons.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-brand-200 hover:text-brand-700">
        Bekor qilish
    </a>
</div>
