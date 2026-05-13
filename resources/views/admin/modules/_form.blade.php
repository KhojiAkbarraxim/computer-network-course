@php($isEditing = $module->exists)

<div class="grid gap-6 md:grid-cols-2">
    <div>
        <x-input-label for="course_id" value="Kurs" />
        <select id="course_id" name="course_id" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-brand-300 focus:ring-2 focus:ring-brand-100">
            <option value="">Kursni tanlang</option>
            @foreach ($courses as $course)
                <option value="{{ $course->id }}" @selected((string) old('course_id', $module->course_id) === (string) $course->id)>{{ $course->title }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('course_id')" />
    </div>

    <div>
        <x-input-label for="sort_order" value="Tartib raqami" />
        <x-text-input id="sort_order" name="sort_order" type="number" min="1" class="mt-1" :value="old('sort_order', $module->sort_order)" required />
        <x-input-error class="mt-2" :messages="$errors->get('sort_order')" />
    </div>

    <div>
        <x-input-label for="title" value="Sarlavha" />
        <x-text-input id="title" name="title" type="text" class="mt-1" :value="old('title', $module->title)" required />
        <x-input-error class="mt-2" :messages="$errors->get('title')" />
    </div>

    <div>
        <x-input-label for="slug" value="Slug" />
        <x-text-input id="slug" name="slug" type="text" class="mt-1" :value="old('slug', $module->slug)" required />
        <x-input-error class="mt-2" :messages="$errors->get('slug')" />
    </div>

    <div>
        <x-input-label for="difficulty_level" value="Qiyinchilik darajasi" />
        <x-text-input id="difficulty_level" name="difficulty_level" type="text" class="mt-1" :value="old('difficulty_level', $module->difficulty_level)" />
        <x-input-error class="mt-2" :messages="$errors->get('difficulty_level')" />
    </div>

    <div>
        <x-input-label for="estimated_duration_minutes" value="Davomiylik (daqiqada)" />
        <x-text-input id="estimated_duration_minutes" name="estimated_duration_minutes" type="number" min="1" class="mt-1" :value="old('estimated_duration_minutes', $module->estimated_duration_minutes)" />
        <x-input-error class="mt-2" :messages="$errors->get('estimated_duration_minutes')" />
    </div>
</div>

<div class="mt-6">
    <x-input-label for="short_description" value="Qisqacha tavsif" />
    <textarea id="short_description" name="short_description" rows="4" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-brand-300 focus:ring-2 focus:ring-brand-100">{{ old('short_description', $module->short_description) }}</textarea>
    <x-input-error class="mt-2" :messages="$errors->get('short_description')" />
</div>

<label class="mt-6 flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
    <input type="checkbox" name="is_published" value="1" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500" @checked(old('is_published', $module->is_published ?? true))>
    <span class="text-sm font-semibold text-slate-700">Faol</span>
</label>

<div class="mt-8 flex flex-wrap items-center gap-3">
    <x-primary-button>Saqlash</x-primary-button>
    <a href="{{ route('admin.modules.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-brand-200 hover:text-brand-700">
        Bekor qilish
    </a>
</div>
