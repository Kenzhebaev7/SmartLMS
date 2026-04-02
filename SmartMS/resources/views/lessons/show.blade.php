<x-app-layout>
    <x-slot name="header">{{ $lesson->getTitleForLocale(app()->getLocale()) }} — SmartLMS</x-slot>

    <div class="max-w-4xl">
        <div class="prose prose-slate dark:prose-invert max-w-none mb-8">
            {!! \Illuminate\Support\Str::markdown($lesson->getContentForLocale(app()->getLocale()) ?? '') !!}
        </div>

        @if($lesson->video_url ?? $lesson->video_id)
            <div class="mb-8">
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-2">{{ __('messages.lessons_video') }}</h3>
                @php
                    $embed = $lesson->video_id
                        ? 'https://www.youtube.com/embed/' . $lesson->video_id
                        : (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $lesson->video_url ?? '', $m) ? 'https://www.youtube.com/embed/' . $m[1] : ($lesson->video_url ?? ''));
                @endphp
                <div class="aspect-video w-full rounded-xl overflow-hidden bg-black">
                    <iframe class="w-full h-full" src="{{ $embed }}" title="{{ $lesson->getTitleForLocale(app()->getLocale()) }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
            </div>
        @endif

        @if($lesson->file_path)
            <div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">{{ __('messages.lessons_download_heading') }}</h3>
                <a href="{{ asset('storage/' . $lesson->file_path) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-light transition-colors">
                    {{ __('messages.lessons_download') }}
                </a>
            </div>
        @endif

        @if(session('status'))
            <div class="mt-6 rounded-xl bg-primary-50 dark:bg-primary/20 border border-primary-200 dark:border-primary/30 px-4 py-3 text-primary-light dark:text-primary-200">{{ session('status') }}</div>
        @endif

        <section class="mt-10 pt-8 border-t border-slate-200 dark:border-slate-700">
            <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100 mb-4">{{ __('messages.lessons_questions_heading') }}</h2>
            @forelse($questionThreads ?? [] as $thread)
                <div class="mb-4 p-4 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600">
                    <a href="{{ route('forum.show', $thread) }}" class="font-semibold text-slate-900 dark:text-slate-100 hover:text-primary dark:hover:text-primary-400">{{ $thread->title }}</a>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $thread->user->name }} · {{ $thread->created_at->diffForHumans() }}</p>
                </div>
            @empty
                <p class="text-slate-500 dark:text-slate-400">{{ __('messages.lessons_no_questions') }}</p>
            @endforelse

            <form action="{{ route('lessons.questions.store', [$section, $lesson]) }}" method="POST" class="mt-6 p-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600">
                @csrf
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('messages.lessons_ask_question') }}</label>
                <input type="text" name="title" required maxlength="255" placeholder="{{ __('messages.lessons_question_title') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-3 py-2 mb-3">
                <textarea name="body" required rows="3" placeholder="{{ __('messages.lessons_question_body') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-3 py-2 mb-3"></textarea>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-light transition-colors">{{ __('messages.lessons_submit_question') }}</button>
            </form>
        </section>

        <div class="mt-8">
            <a href="{{ route('sections.show', $section) }}" class="text-primary font-semibold hover:underline">← {{ __('messages.lessons_back_section') }}</a>
        </div>
    </div>
</x-app-layout>
