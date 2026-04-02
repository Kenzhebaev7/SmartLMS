<x-app-layout>
    <x-slot name="header">{{ __('messages.teacher_edit_lesson_header') }} — {{ $lesson->section->getTitleForLocale(app()->getLocale()) }}</x-slot>

    <form action="{{ route('teacher.lessons.update', $lesson) }}" method="POST" enctype="multipart/form-data" class="max-w-xl space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('messages.teacher_title') }}</label>
            <input type="text" name="title" value="{{ old('title', $lesson->title) }}" required class="w-full rounded-lg border border-gray-300 px-4 py-2">
            @error('title')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('messages.teacher_content') }} (RU)</label>
            <textarea name="content" rows="5" class="w-full rounded-lg border border-gray-300 px-4 py-2">{{ old('content', $lesson->content) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('messages.teacher_title') }} / {{ __('messages.teacher_content') }} (ҚК)</label>
            <input type="text" name="title_kk" value="{{ old('title_kk', $lesson->title_kk) }}" placeholder="{{ __('messages.teacher_placeholder_topic_kk') }}" class="w-full rounded-lg border border-gray-300 px-4 py-2 mb-2">
            <textarea name="content_kk" rows="3" placeholder="{{ __('messages.teacher_placeholder_content_kk') }}" class="w-full rounded-lg border border-gray-300 px-4 py-2">{{ old('content_kk', $lesson->content_kk) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('messages.teacher_video_url') }}</label>
            <input type="url" name="video_url" value="{{ old('video_url', $lesson->video_url) }}" placeholder="{{ __('messages.teacher_placeholder_video_url') }}" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-4 py-2">
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ __('messages.teacher_video_url_hint') }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('messages.teacher_video_id') }}</label>
            <input type="text" name="video_id" value="{{ old('video_id', $lesson->video_id) }}" placeholder="{{ __('messages.teacher_placeholder_video_id') }}" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-4 py-2">
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ __('messages.teacher_video_id_hint') }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('messages.teacher_file') }}</label>
            <input type="file" name="file" accept=".pdf,.doc,.docx,.txt,.ppt,.pptx" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 px-4 py-2">
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ __('messages.teacher_file_hint') }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ __('messages.teacher_new_file_hint') }}</p>
            @if($lesson->file_path)
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">{{ __('messages.teacher_current_file', ['name' => basename($lesson->file_path ?? '')]) }}</p>
            @endif
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('messages.teacher_order') }}</label>
            <input type="number" name="order" value="{{ old('order', $lesson->order) }}" min="0" class="w-full rounded-lg border border-gray-300 px-4 py-2">
        </div>
        <div class="flex gap-3">
            <button type="submit" class="px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary-light transition-colors">{{ __('messages.teacher_save') }}</button>
            <a href="{{ route('teacher.sections.show', $lesson->section) }}" class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700">{{ __('messages.teacher_cancel') }}</a>
        </div>
    </form>
</x-app-layout>
