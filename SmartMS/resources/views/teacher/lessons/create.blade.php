<x-app-layout>
    <x-slot name="header">{{ __('teacher.new_lesson') }} — {{ $section->title }}</x-slot>

    <form action="{{ route('teacher.sections.lessons.store', $section) }}" method="POST" enctype="multipart/form-data" class="max-w-xl space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('teacher.title') }}</label>
            <input type="text" name="title" value="{{ old('title') }}" required class="w-full rounded-lg border border-gray-300 px-4 py-2">
            @error('title')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('teacher.content') }} (RU)</label>
            <textarea name="content" rows="5" class="w-full rounded-lg border border-gray-300 px-4 py-2">{{ old('content') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('teacher.title') }} / {{ __('teacher.content') }} (ҚК)</label>
            <input type="text" name="title_kk" value="{{ old('title_kk') }}" placeholder="Тақырыбы (қазақша)" class="w-full rounded-lg border border-gray-300 px-4 py-2 mb-2">
            <textarea name="content_kk" rows="3" placeholder="Мәтіні (қазақша)" class="w-full rounded-lg border border-gray-300 px-4 py-2">{{ old('content_kk') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('teacher.video_url') }}</label>
            <input type="url" name="video_url" value="{{ old('video_url') }}" placeholder="https://www.youtube.com/watch?v=... или https://youtu.be/..." class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-4 py-2">
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ __('teacher.video_url_hint') }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('teacher.video_id') }}</label>
            <input type="text" name="video_id" value="{{ old('video_id') }}" placeholder="t-JRMxluNz8" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-4 py-2">
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ __('teacher.video_id_hint') }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('teacher.file') }}</label>
            <input type="file" name="file" accept=".pdf,.doc,.docx" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 px-4 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('teacher.order') }}</label>
            <input type="number" name="order" value="{{ old('order', 0) }}" min="0" class="w-full rounded-lg border border-gray-300 px-4 py-2">
        </div>
        <div class="flex gap-3">
            <button type="submit" class="px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary-light transition-colors">{{ __('teacher.create') }}</button>
            <a href="{{ route('teacher.sections.show', $section) }}" class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700">{{ __('teacher.cancel') }}</a>
        </div>
    </form>
</x-app-layout>
