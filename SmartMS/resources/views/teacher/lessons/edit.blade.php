<x-app-layout>
    <x-slot name="header">{{ __('messages.teacher_edit_lesson_header') }} - {{ $lesson->section->getTitleForLocale(app()->getLocale()) }}</x-slot>

    <div class="grid gap-6 xl:grid-cols-[1.08fr_0.92fr]">
        <form action="{{ route('teacher.lessons.update', $lesson) }}" method="POST" enctype="multipart/form-data" class="space-y-6 rounded-[30px] border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')

            <div class="rounded-2xl border border-sky-200 bg-sky-50 px-4 py-4 text-sm text-sky-900">
                {{ __('messages.teacher_lesson_context_hint', ['grade' => $lesson->section->grade ? __('messages.auth_grade_' . $lesson->section->grade) : __('messages.teacher_grade_all'), 'level' => $lesson->section->is_revision ? __('messages.dashboard_level_beginner') : __('messages.dashboard_level_advanced')]) }}
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_title') }}</label>
                    <input type="text" name="title" value="{{ old('title', $lesson->title) }}" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-800">
                    @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_title') }} (KK)</label>
                    <input type="text" name="title_kk" value="{{ old('title_kk', $lesson->title_kk) }}" placeholder="{{ __('messages.teacher_placeholder_topic_kk') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-800">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_order') }}</label>
                    <input type="number" name="order" value="{{ old('order', $lesson->order) }}" min="0" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-800">
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_content') }} (RU)</label>
                    <textarea name="content" rows="9" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-800">{{ old('content', $lesson->content) }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_content') }} (KK)</label>
                    <textarea name="content_kk" rows="6" placeholder="{{ __('messages.teacher_placeholder_content_kk') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-800">{{ old('content_kk', $lesson->content_kk) }}</textarea>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.teacher_lesson_materials_title') }}</p>
                <h3 class="mt-2 text-xl font-bold text-slate-900">{{ __('messages.teacher_lesson_materials_desc') }}</h3>

                <div class="mt-5 grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_video_url') }}</label>
                        <input type="text" name="video_url" value="{{ old('video_url', $lesson->video_url) }}" placeholder="{{ __('messages.teacher_placeholder_video_url') }}" autocomplete="off" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-800">
                        <p class="mt-1 text-xs text-slate-500">{{ __('messages.teacher_video_url_hint') }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_video_id') }}</label>
                        <input type="text" name="video_id" value="{{ old('video_id', $lesson->video_id) }}" placeholder="{{ __('messages.teacher_placeholder_video_id') }}" autocomplete="off" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-800">
                        <p class="mt-1 text-xs text-slate-500">{{ __('messages.teacher_video_id_hint') }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_video_url_kk') }}</label>
                        <input type="text" name="video_url_kk" value="{{ old('video_url_kk', $lesson->video_url_kk) }}" placeholder="{{ __('messages.teacher_placeholder_video_url') }}" autocomplete="off" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-800">
                        <p class="mt-1 text-xs text-slate-500">{{ __('messages.teacher_video_url_kk_hint') }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_video_id_kk') }}</label>
                        <input type="text" name="video_id_kk" value="{{ old('video_id_kk', $lesson->video_id_kk) }}" placeholder="{{ __('messages.teacher_placeholder_video_id') }}" autocomplete="off" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-800">
                        <p class="mt-1 text-xs text-slate-500">{{ __('messages.teacher_video_id_kk_hint') }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_file') }}</label>
                        <input type="file" name="file" accept=".pdf,.doc,.docx,.txt,.ppt,.pptx" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-800">
                        <p class="mt-1 text-xs text-slate-500">{{ __('messages.teacher_file_hint') }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ __('messages.teacher_new_file_hint') }}</p>
                        @if($lesson->file_path)
                            <p class="mt-2 text-sm text-slate-600">{{ __('messages.teacher_current_file', ['name' => basename($lesson->file_path ?? '')]) }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.teacher_unlock_after_lesson') }}</p>
                <h3 class="mt-2 text-xl font-bold text-slate-900">{{ __('messages.teacher_lesson_unlock_title') }}</h3>
                <p class="mt-1 text-sm text-slate-600">{{ __('messages.teacher_unlock_after_hint') }}</p>

                <div class="mt-4">
                    <select name="unlock_after_lesson_id" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-800">
                        <option value="">{{ __('messages.teacher_unlock_after_none') }}</option>
                        @foreach($availableLessons ?? [] as $availableLesson)
                            <option value="{{ $availableLesson->id }}" @selected((string) old('unlock_after_lesson_id', $lesson->unlock_after_lesson_id) === (string) $availableLesson->id)>
                                {{ $availableLesson->getTitleForLocale(app()->getLocale()) }}
                            </option>
                        @endforeach
                    </select>
                    @error('unlock_after_lesson_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                    {{ __('messages.teacher_save') }}
                </button>
                <a href="{{ route('teacher.sections.show', $lesson->section) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    {{ __('messages.teacher_cancel') }}
                </a>
            </div>
        </form>

        <aside class="space-y-6 rounded-[30px] border border-slate-200 bg-gradient-to-br from-white to-sky-50/50 p-6 shadow-sm">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.teacher_lessons') }}</p>
                <h2 class="mt-2 text-2xl font-bold text-slate-900">{{ __('messages.teacher_lesson_edit_snapshot_title') }}</h2>
                <p class="mt-2 text-sm leading-7 text-slate-600">{{ __('messages.teacher_lesson_edit_snapshot_desc') }}</p>
            </div>

            <div class="space-y-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">{{ __('messages.teacher_order') }}</p>
                    <p class="mt-2 text-xl font-bold text-slate-900">{{ $lesson->order }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">{{ __('messages.teacher_lesson_materials_title') }}</p>
                    <p class="mt-2 text-sm text-slate-600">
                        {{ $lesson->file_path || $lesson->video_url || $lesson->video_id ? __('messages.teacher_lesson_edit_has_materials') : __('messages.teacher_lesson_issue_no_materials') }}
                    </p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">{{ __('messages.teacher_unlock_after_lesson') }}</p>
                    <p class="mt-2 text-sm text-slate-600">
                        {{ $lesson->unlockAfterLesson ? __('messages.teacher_unlock_after_display', ['lesson' => $lesson->unlockAfterLesson->getTitleForLocale(app()->getLocale())]) : __('messages.teacher_unlock_after_none') }}
                    </p>
                </div>
            </div>
        </aside>
    </div>
</x-app-layout>
