<x-app-layout>
    <x-slot name="header">{{ __('messages.forum_create_title') }}</x-slot>

    <div class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
        <form action="{{ route('forum.store') }}" method="POST" class="space-y-6 rounded-[30px] border border-slate-200 bg-white p-6 shadow-sm">
            @csrf

            <div class="rounded-2xl border border-sky-200 bg-sky-50 px-4 py-4 text-sm text-sky-900">
                {{ __('messages.forum_create_hint') }}
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.forum_section_project') }}</label>
                    <select name="section_id" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-800">
                        <option value="">{{ __('messages.forum_all_projects') }}</option>
                        @foreach($sections ?? [] as $s)
                            <option value="{{ $s->id }}" {{ old('section_id') == $s->id ? 'selected' : '' }}>{{ $s->getTitleForLocale(app()->getLocale()) }}</option>
                        @endforeach
                    </select>
                    @if(($sections ?? collect())->isEmpty())
                        <p class="mt-1 text-xs text-amber-700">{{ __('messages.forum_no_sections_hint') }}</p>
                    @endif
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.forum_tag_label') }}</label>
                    <select name="tag" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-800">
                        @foreach($tagOptions ?? [] as $tagOption)
                            <option value="{{ $tagOption }}" {{ old('tag', \App\Models\Thread::TAG_QUESTION) === $tagOption ? 'selected' : '' }}>
                                {{ __('messages.forum_tag_' . $tagOption) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.forum_create_topic_title') }}</label>
                    <input type="text" name="title" value="{{ old('title') }}" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-800">
                    @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.forum_create_body') }}</label>
                    <textarea name="body" rows="7" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-800">{{ old('body') }}</textarea>
                    @error('body')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            @if(auth()->user()?->isTeacher() || auth()->user()?->isAdmin())
                <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                    <input type="checkbox" name="is_pinned" value="1" @checked(old('is_pinned')) class="rounded border-slate-300 text-primary focus:ring-primary">
                    <span>
                        <span class="block text-sm font-semibold text-slate-900">{{ __('messages.forum_pin_topic') }}</span>
                        <span class="block text-xs text-slate-500">{{ __('messages.forum_pin_topic_hint') }}</span>
                    </span>
                </label>
            @endif

            <div class="flex flex-wrap gap-3">
                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                    {{ __('messages.forum_create_create') }}
                </button>
                <a href="{{ route('forum.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    {{ __('messages.teacher_cancel') }}
                </a>
            </div>
        </form>

        <aside class="rounded-[30px] border border-slate-200 bg-gradient-to-br from-white to-amber-50/50 p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.forum_topics') }}</p>
            <h2 class="mt-2 text-2xl font-bold text-slate-900">{{ __('messages.forum_create_sidebar_title') }}</h2>
            <p class="mt-2 text-sm leading-7 text-slate-600">{{ __('messages.forum_create_sidebar_desc') }}</p>

            <div class="mt-6 space-y-4">
                @foreach($tagOptions ?? [] as $tagOption)
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-sm font-semibold text-slate-900">{{ __('messages.forum_tag_' . $tagOption) }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ __('messages.forum_tag_help_' . $tagOption) }}</p>
                    </div>
                @endforeach
            </div>
        </aside>
    </div>
</x-app-layout>
