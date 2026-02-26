<x-app-layout>
    <x-slot name="header">{{ $lesson->getTitleForLocale(app()->getLocale()) }} — SmartLMS</x-slot>

    <div class="max-w-4xl">
        {{-- Текст урока --}}
        <div class="mb-8 p-6 rounded-xl border border-slate-100 bg-white dark:bg-slate-800 dark:border-slate-600">
            <div class="prose prose-slate dark:prose-invert max-w-none text-slate-700 dark:text-slate-200">
                {!! nl2br(e($lesson->getContentForLocale(app()->getLocale()) ?? '')) !!}
            </div>
        </div>

        {{-- Видео YouTube: video_id или разбор video_url --}}
        @if($lesson->video_id ?? $lesson->video_url)
            <div class="mb-8 animate-fade-up">
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-3">{{ __('lessons.video') }}</h3>
                @php
                    $embed = $lesson->video_id
                        ? 'https://www.youtube.com/embed/' . $lesson->video_id
                        : (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $lesson->video_url ?? '', $m) ? 'https://www.youtube.com/embed/' . $m[1] : ($lesson->video_url ?? ''));
                @endphp
                <div class="aspect-video w-full max-w-3xl rounded-2xl overflow-hidden bg-black shadow-sky-200 ring-2 ring-slate-200 dark:ring-sky-900/50">
                    <iframe class="w-full h-full" src="{{ $embed }}" title="{{ $lesson->getTitleForLocale(app()->getLocale()) }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
            </div>
        @endif

        {{-- Файл для скачивания --}}
        @if($lesson->file_path)
            <div class="mb-8">
                <a href="{{ asset('storage/' . $lesson->file_path) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-5 py-3 bg-primary text-white rounded-xl font-medium hover:bg-primary-light transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    {{ __('lessons.download_materials') }}
                </a>
            </div>
        @endif

        @if(session('status'))
            <div class="mb-6 rounded-xl bg-primary-50 dark:bg-primary/20 border border-primary-200 dark:border-primary/30 px-4 py-3 text-primary-700 dark:text-primary-200">{{ session('status') }}</div>
        @endif

        {{-- Завершить урок (Alpine.js: AJAX + зелёное состояние) --}}
        <div class="mb-8" x-data="lessonComplete({{ $lessonCompleted ? 'true' : 'false' }}, '{{ route('lessons.complete', [$section, $lesson]) }}', '{{ addslashes(__('lessons.already_completed')) }}', '{{ addslashes(__('lessons.complete_btn')) }}')">
            <template x-if="completed">
                <p class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-200 font-medium shadow-sky-100 transition-all duration-300 scale-100">
                    <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                    <span x-text="completedLabel"></span>
                </p>
            </template>
            <template x-if="!completed && !loading">
                <button type="button" @click="submitComplete()"
                    class="inline-flex items-center gap-2 px-6 py-3.5 bg-emerald-600 text-white rounded-xl font-semibold hover:bg-emerald-700 transition-all duration-200 hover:scale-105 active:scale-100 shadow-sky-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    <span x-text="completeBtnLabel"></span>
                </button>
            </template>
            <template x-if="loading">
                <span class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-300 font-medium">{{ __('lessons.complete_btn') }}...</span>
            </template>
        </div>

        {{-- Вопросы к уроку (форум) --}}
        <section class="mt-10 pt-8 border-t border-slate-100 dark:border-slate-700">
            <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100 mb-4">{{ __('lessons.questions_heading') }}</h2>
            @forelse($questionThreads ?? [] as $thread)
                <div class="mb-4 p-4 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600">
                    <a href="{{ route('forum.show', $thread) }}" class="font-semibold text-slate-900 dark:text-slate-100 hover:text-primary dark:hover:text-primary-400">{{ $thread->title }}</a>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $thread->user->name }} · {{ $thread->created_at->diffForHumans() }}</p>
                </div>
            @empty
                <p class="text-slate-500 dark:text-slate-400">{{ __('lessons.no_questions') }}</p>
            @endforelse

            <form action="{{ route('lessons.questions.store', [$section, $lesson]) }}" method="POST" class="mt-6 p-4 rounded-xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-600">
                @csrf
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('lessons.ask_question') }}</label>
                <input type="text" name="title" required maxlength="255" placeholder="{{ __('lessons.question_title') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-3 py-2 mb-3">
                <textarea name="body" required rows="3" placeholder="{{ __('lessons.question_body') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-3 py-2 mb-3"></textarea>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-light transition-colors">{{ __('lessons.submit_question') }}</button>
            </form>
        </section>

        {{-- Навигация: Назад к разделу / Следующий урок --}}
        <div class="mt-10 pt-6 flex flex-wrap items-center gap-4 border-t border-slate-100 dark:border-slate-700">
            <a href="{{ route('sections.show', $section) }}" class="inline-flex items-center gap-2 text-primary font-semibold hover:underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                {{ __('lessons.back_section') }}
            </a>
            @if($nextLesson ?? null)
                <a href="{{ route('lessons.show', [$section, $nextLesson]) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white rounded-xl font-medium hover:bg-primary-light transition-colors">
                    {{ __('lessons.next_lesson') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                </a>
            @endif
        </div>
    </div>
</x-app-layout>
