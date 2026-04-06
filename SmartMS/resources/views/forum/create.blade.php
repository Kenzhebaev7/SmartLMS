<x-app-layout>
    <x-slot name="header">{{ __('messages.forum_create_title') }}</x-slot>

    <form action="{{ route('forum.store') }}" method="POST" class="max-w-xl space-y-4">
        @csrf
        <div class="rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-900">
            {{ __('messages.forum_create_hint') }}
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('messages.forum_section_project') }}</label>
            <select name="section_id" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-4 py-2">
                <option value="">{{ __('messages.forum_all_projects') }}</option>
                @foreach($sections ?? [] as $s)
                    <option value="{{ $s->id }}" {{ old('section_id') == $s->id ? 'selected' : '' }}>{{ $s->getTitleForLocale(app()->getLocale()) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('messages.forum_create_topic_title') }}</label>
            <input type="text" name="title" value="{{ old('title') }}" required class="w-full rounded-lg border border-gray-300 px-4 py-2">
            @error('title')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('messages.forum_create_body') }}</label>
            <textarea name="body" rows="6" required class="w-full rounded-lg border border-gray-300 px-4 py-2">{{ old('body') }}</textarea>
            @error('body')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="flex gap-3">
            <button type="submit" class="px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary-light transition-colors">{{ __('messages.forum_create_create') }}</button>
            <a href="{{ route('forum.index') }}" class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700">{{ __('messages.teacher_cancel') }}</a>
        </div>
    </form>
</x-app-layout>
