<x-app-layout>
    <x-slot name="header">{{ __('messages.teacher_new_section_header') }}</x-slot>

    <form action="{{ route('teacher.sections.store') }}" method="POST" class="max-w-xl space-y-4">
        @csrf
        <div class="rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-900">
            {{ __('messages.teacher_targeting_hint') }}
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('messages.teacher_title') }}</label>
            <input type="text" name="title" value="{{ old('title') }}" required class="w-full rounded-lg border border-gray-300 px-4 py-2">
            @error('title')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('messages.teacher_description') }} (RU)</label>
            <textarea name="description" rows="3" class="w-full rounded-lg border border-gray-300 px-4 py-2">{{ old('description') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('messages.teacher_title') }} / {{ __('messages.teacher_description') }} (ҚК)</label>
            <input type="text" name="title_kk" value="{{ old('title_kk') }}" placeholder="{{ __('messages.teacher_placeholder_title_kk') }}" class="w-full rounded-lg border border-gray-300 px-4 py-2 mb-2">
            <textarea name="description_kk" rows="2" placeholder="{{ __('messages.teacher_placeholder_description_kk') }}" class="w-full rounded-lg border border-gray-300 px-4 py-2">{{ old('description_kk') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('messages.dashboard_grade') }}</label>
            <select name="grade" class="w-full rounded-lg border border-gray-300 px-4 py-2">
                <option value="">{{ __('messages.teacher_grade_all') }}</option>
                <option value="9" {{ old('grade') === '9' ? 'selected' : '' }}>{{ __('messages.auth_grade_9') }}</option>
                <option value="10" {{ old('grade') === '10' ? 'selected' : '' }}>{{ __('messages.auth_grade_10') }}</option>
                <option value="11" {{ old('grade') === '11' ? 'selected' : '' }}>{{ __('messages.auth_grade_11') }}</option>
            </select>
            <label class="mt-3 flex items-center gap-2">
                <input type="checkbox" name="is_revision" value="1" {{ old('is_revision') ? 'checked' : '' }} class="rounded border-gray-300">
                <span class="text-sm text-slate-700">{{ __('messages.teacher_is_revision') }}</span>
            </label>
            <p class="mt-2 text-xs text-slate-500">{{ __('messages.teacher_revision_hint') }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('messages.teacher_order') }}</label>
            <input type="number" name="order" value="{{ old('order', 0) }}" min="0" class="w-full rounded-lg border border-gray-300 px-4 py-2">
        </div>
        <div class="flex gap-3">
            <button type="submit" class="px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary-light transition-colors">{{ __('messages.teacher_create') }}</button>
            <a href="{{ route('teacher.sections.index') }}" class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700">{{ __('messages.teacher_cancel') }}</a>
        </div>
    </form>
</x-app-layout>
