<x-app-layout>
    <x-slot name="header">{{ __('teacher.edit_section_header') }}</x-slot>

    <form action="{{ route('teacher.sections.update', $section) }}" method="POST" class="max-w-xl space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('teacher.title') }}</label>
            <input type="text" name="title" value="{{ old('title', $section->title) }}" required class="w-full rounded-lg border border-gray-300 px-4 py-2">
            @error('title')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('teacher.description') }} (RU)</label>
            <textarea name="description" rows="3" class="w-full rounded-lg border border-gray-300 px-4 py-2">{{ old('description', $section->description) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('teacher.title') }} / {{ __('teacher.description') }} (ҚК)</label>
            <input type="text" name="title_kk" value="{{ old('title_kk', $section->title_kk) }}" placeholder="Атауы (қазақша)" class="w-full rounded-lg border border-gray-300 px-4 py-2 mb-2">
            <textarea name="description_kk" rows="2" placeholder="Сипаттама (қазақша)" class="w-full rounded-lg border border-gray-300 px-4 py-2">{{ old('description_kk', $section->description_kk) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('teacher.level') }}</label>
            <select name="level" class="w-full rounded-lg border border-gray-300 px-4 py-2">
                <option value="" {{ old('level', $section->level) === null ? 'selected' : '' }}>{{ __('teacher.level_all') }}</option>
                <option value="beginner" {{ old('level', $section->level) === 'beginner' ? 'selected' : '' }}>{{ __('teacher.level_beginner') }}</option>
                <option value="advanced" {{ old('level', $section->level) === 'advanced' ? 'selected' : '' }}>{{ __('teacher.level_advanced') }}</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('teacher.order') }}</label>
            <input type="number" name="order" value="{{ old('order', $section->order) }}" min="0" class="w-full rounded-lg border border-gray-300 px-4 py-2">
        </div>
        <div class="flex gap-3">
            <button type="submit" class="px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary-light transition-colors">{{ __('teacher.save') }}</button>
            <a href="{{ route('teacher.sections.index') }}" class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700">{{ __('teacher.cancel') }}</a>
        </div>
    </form>
</x-app-layout>
