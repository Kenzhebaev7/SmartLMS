<x-app-layout>
    <x-slot name="header">{{ $lesson->getTitleForLocale(app()->getLocale()) }} — SmartLMS</x-slot>

    <div class="max-w-4xl">
        @php
            $student = auth()->user();
            $levelKey = $student?->placementLevelKey();
            $sectionTrackLabel = $section->is_revision ? __('messages.dashboard_level_beginner') : __('messages.dashboard_level_advanced');
            $sectionGradeLabel = $section->grade ? __('messages.auth_grade_' . $section->grade) : __('messages.teacher_grade_all');
        @endphp
        <div class="mb-6 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/90 dark:bg-slate-800/70 px-5 py-4">
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $sectionGradeLabel }}</span>
                <span class="inline-flex items-center px-3 py-1 rounded-full {{ $section->is_revision ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200 border border-amber-200 dark:border-amber-700' : 'bg-violet-100 dark:bg-violet-900/30 text-violet-800 dark:text-violet-200 border border-violet-200 dark:border-violet-700' }} text-sm font-semibold">{{ $sectionTrackLabel }}</span>
            </div>
            <p class="text-sm text-slate-600 dark:text-slate-300">{{ __('messages.lessons_context_summary', ['section' => $section->getTitleForLocale(app()->getLocale()), 'level' => $levelKey ? __('messages.dashboard_level_' . $levelKey) : $sectionTrackLabel]) }}</p>
        </div>

        {{-- Текст урока --}}
        <div class="mb-8 p-6 rounded-xl border border-slate-100 bg-white dark:bg-slate-800 dark:border-slate-600">
            <div class="prose prose-slate dark:prose-invert max-w-none text-slate-700 dark:text-slate-200">
                {!! \Illuminate\Support\Str::markdown($lesson->getContentForLocale(app()->getLocale()) ?? '') !!}
            </div>
        </div>

        {{-- Видео: отдельные ссылки для RU / KK; YouTube embed через nocookie; запасная ссылка если embed недоступен --}}
        @php
            $loc = app()->getLocale();
            $embedUrl = $lesson->youtubeEmbedUrlForLocale($loc);
            $watchUrl = $lesson->youtubeWatchUrlForLocale($loc);
            $otherUrl = $lesson->nonYoutubeVideoUrlForLocale($loc);
            $showVideoBlock = $lesson->hasVideoDataForLocale($loc);
        @endphp
        @if($showVideoBlock)
            <div class="mb-8 animate-fade-up">
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-3">{{ __('messages.lessons_video') }}</h3>
                @if($embedUrl)
                    <div class="aspect-video w-full max-w-3xl rounded-2xl overflow-hidden bg-black shadow-sky-200 ring-2 ring-slate-200 dark:ring-sky-900/50">
                        <iframe
                            class="w-full h-full"
                            src="{{ $embedUrl }}"
                            title="{{ $lesson->getTitleForLocale($loc) }}"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin"
                            allowfullscreen
                            loading="lazy"
                        ></iframe>
                    </div>
                    @if($watchUrl)
                        <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">
                            <a href="{{ $watchUrl }}" target="_blank" rel="noopener noreferrer" class="text-sky-600 dark:text-sky-400 font-medium hover:underline">{{ __('messages.lessons_video_open_on_youtube') }}</a>
                        </p>
                    @endif
                @elseif($otherUrl)
                    <p class="text-slate-600 dark:text-slate-300 mb-3">{{ __('messages.lessons_video_external_hint') }}</p>
                    <a href="{{ $otherUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-sky-600 text-white font-semibold hover:bg-sky-700 transition-colors">{{ __('messages.lessons_video_open_external') }}</a>
                @else
                    <div class="rounded-xl border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-950/30 px-4 py-3 text-amber-900 dark:text-amber-100 text-sm">
                        <p class="font-medium">{{ __('messages.lessons_video_unavailable') }}</p>
                        <p class="mt-1 text-amber-800/90 dark:text-amber-200/90">{{ __('messages.lessons_video_check_link_hint') }}</p>
                    </div>
                @endif
                @if($loc === 'kk' && ($lesson->video_url_kk || $lesson->video_id_kk))
                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">{{ __('messages.lessons_video_kk_track_hint') }}</p>
                @endif
            </div>
        @endif

        {{-- Онлайн-компилятор для уроков по программированию (C++ / Python). Проверка по ID раздела и по названию (оба языка). --}}
        @php
            $cppIds = config('smartlms.ide_cpp_section_ids', []);
            $pythonIds = config('smartlms.ide_python_section_ids', []);
            $titleRu = mb_strtolower($section->title ?? '');
            $titleKk = mb_strtolower($section->title_kk ?? '');
            $isCpp = in_array((int) $section->id, $cppIds, true)
                || str_contains($titleRu, 'c++') || str_contains($titleKk, 'c++');
            $isPython = in_array((int) $section->id, $pythonIds, true)
                || str_contains($titleRu, 'python') || str_contains($titleKk, 'python');
            $isProgramming = $isCpp || $isPython
                || str_contains($titleRu, 'программирован') || str_contains($titleKk, 'бағдарлама');
        @endphp
        @if($isProgramming)
            @php
                $ideCppEmbed = config('smartlms.ide_cpp_embed_url');
                $idePyEmbed = config('smartlms.ide_python_embed_url');
                $ideCppOpen = config('smartlms.ide_cpp_open_url');
                $idePyOpen = config('smartlms.ide_python_open_url');
            @endphp
            <div class="mb-8 p-4 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-800/50">
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-3">{{ __('messages.lessons_online_ide') }}</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">{{ __('messages.lessons_ide_embed_hint') }}</p>
                @if($isCpp)
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">{{ __('messages.lessons_ide_cpp_hint') }}</p>
                    <div class="relative w-full overflow-hidden rounded-lg border border-slate-200 dark:border-slate-600 bg-white" style="height: min(70vh, 560px); min-height: 400px;">
                        <iframe
                            src="{{ $ideCppEmbed }}"
                            title="{{ __('messages.lessons_ide_cpp_title') }}"
                            class="absolute inset-0 h-full w-full border-0"
                            allow="fullscreen; clipboard-read; clipboard-write"
                            allowfullscreen
                            referrerpolicy="no-referrer-when-downgrade"
                            loading="lazy"
                        ></iframe>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                        {{ __('messages.lessons_ide_open_new_tab') }}:
                        <a href="{{ $ideCppOpen }}" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline font-medium">{{ __('messages.lessons_ide_open_cpp') }}</a>
                    </p>
                @endif
                @if($isPython)
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-2 {{ $isCpp ? 'mt-6' : '' }}">{{ __('messages.lessons_ide_python_hint') }}</p>
                    <div class="relative w-full overflow-hidden rounded-lg border border-slate-200 dark:border-slate-600 bg-white" style="height: min(70vh, 560px); min-height: 400px;">
                        <iframe
                            src="{{ $idePyEmbed }}"
                            title="{{ __('messages.lessons_ide_python_title') }}"
                            class="absolute inset-0 h-full w-full border-0"
                            allow="fullscreen; clipboard-read; clipboard-write"
                            allowfullscreen
                            referrerpolicy="no-referrer-when-downgrade"
                            loading="lazy"
                        ></iframe>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                        {{ __('messages.lessons_ide_open_new_tab') }}:
                        <a href="{{ $idePyOpen }}" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline font-medium">{{ __('messages.lessons_ide_open_python') }}</a>
                    </p>
                @endif
                @if(!$isCpp && !$isPython)
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">{{ __('messages.lessons_ide_cpp_hint') }}</p>
                    <div class="relative w-full overflow-hidden rounded-lg border border-slate-200 dark:border-slate-600 bg-white mb-4" style="height: min(65vh, 520px); min-height: 360px;">
                        <iframe
                            src="{{ $ideCppEmbed }}"
                            title="{{ __('messages.lessons_ide_cpp_title') }}"
                            class="absolute inset-0 h-full w-full border-0"
                            allow="fullscreen; clipboard-read; clipboard-write"
                            allowfullscreen
                            referrerpolicy="no-referrer-when-downgrade"
                            loading="lazy"
                        ></iframe>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 mb-4">
                        {{ __('messages.lessons_ide_open_new_tab') }}:
                        <a href="{{ $ideCppOpen }}" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline font-medium">{{ __('messages.lessons_ide_open_cpp') }}</a>
                    </p>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">{{ __('messages.lessons_ide_python_hint') }}</p>
                    <div class="relative w-full overflow-hidden rounded-lg border border-slate-200 dark:border-slate-600 bg-white" style="height: min(65vh, 520px); min-height: 360px;">
                        <iframe
                            src="{{ $idePyEmbed }}"
                            title="{{ __('messages.lessons_ide_python_title') }}"
                            class="absolute inset-0 h-full w-full border-0"
                            allow="fullscreen; clipboard-read; clipboard-write"
                            allowfullscreen
                            referrerpolicy="no-referrer-when-downgrade"
                            loading="lazy"
                        ></iframe>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                        {{ __('messages.lessons_ide_open_new_tab') }}:
                        <a href="{{ $idePyOpen }}" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline font-medium">{{ __('messages.lessons_ide_open_python') }}</a>
                    </p>
                @endif
            </div>
        @endif

        {{-- Скачать PDF-конспект --}}
        <div class="mb-6">
            <a href="{{ route('lessons.pdf', [$section, $lesson]) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl font-medium bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors border border-slate-200 dark:border-slate-600">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                {{ __('messages.lessons_download_pdf_handout') }}
            </a>
        </div>

        {{-- Файл для скачивания (лекция, материалы) --}}
        @if($lesson->file_path)
            <div class="mb-8 p-4 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-800/50">
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-2">{{ __('messages.lessons_download_heading') }}</h3>
                <a href="{{ asset('storage/' . $lesson->file_path) }}" download="{{ basename($lesson->file_path) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-5 py-3 bg-primary text-white rounded-xl font-medium hover:bg-primary-light transition-colors shadow-sm">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    {{ __('messages.lessons_download_materials') }}: {{ basename($lesson->file_path) }}
                </a>
            </div>
        @endif

        @if(session('status'))
            <div class="mb-6 rounded-xl bg-primary-50 dark:bg-primary/20 border border-primary-200 dark:border-primary/30 px-4 py-3 text-primary-700 dark:text-primary-200">{{ session('status') }}</div>
        @endif

        {{-- Завершить урок (Alpine.js: AJAX + зелёное состояние) --}}
        <div class="mb-8" x-data="lessonComplete({{ $lessonCompleted ? 'true' : 'false' }}, '{{ route('lessons.complete', [$section, $lesson]) }}', '{{ addslashes(__('messages.lessons_already_completed')) }}', '{{ addslashes(__('messages.lessons_complete_btn')) }}')">
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
                <span class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-300 font-medium">{{ __('messages.lessons_complete_btn') }}...</span>
            </template>
        </div>

        {{-- Ссылки на следующий / предыдущий урок и квиз — сразу после блока прохождения --}}
        <div class="mb-8 p-6 rounded-2xl border-2 border-emerald-200/90 dark:border-emerald-800 bg-gradient-to-br from-emerald-50/90 to-white dark:from-emerald-950/30 dark:to-slate-800 shadow-sm">
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    {{ __('messages.lessons_continue_course') }}
                </h3>
                <div class="flex flex-col sm:flex-row flex-wrap gap-3">
                    @if($prevLesson)
                        <a href="{{ route('lessons.show', [$section, $prevLesson]) }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl font-semibold border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 hover:border-emerald-400 dark:hover:border-emerald-600 hover:bg-emerald-50/80 dark:hover:bg-slate-700 transition-colors">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
                            <span class="text-left">{{ __('messages.lessons_prev_lesson') }}: <span class="font-medium text-slate-900 dark:text-white">{{ $prevLesson->getTitleForLocale(app()->getLocale()) }}</span></span>
                        </a>
                    @endif
                    @if($nextLesson)
                        <a href="{{ route('lessons.show', [$section, $nextLesson]) }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl font-semibold bg-emerald-600 text-white hover:bg-emerald-700 shadow-md hover:shadow-lg transition-all">
                            <span class="text-left">{{ __('messages.lessons_next_lesson') }}: <span class="font-semibold">{{ $nextLesson->getTitleForLocale(app()->getLocale()) }}</span></span>
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                        </a>
                    @elseif($section->quiz)
                        <p class="basis-full text-sm text-slate-600 dark:text-slate-300">{{ __('messages.lessons_quiz_gate_hint') }}</p>
                        <a href="{{ route('quiz.show', $section) }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl font-semibold bg-sky-600 text-white hover:bg-sky-700 shadow-md hover:shadow-lg transition-all">
                            {{ __('messages.lessons_to_section_quiz') }}
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                        </a>
                    @else
                        <p class="text-sm text-slate-600 dark:text-slate-400 max-w-xl">{{ __('messages.lessons_all_lessons_in_section_done') }}</p>
                        <a href="{{ route('sections.show', $section) }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl font-semibold bg-slate-700 text-white hover:bg-slate-800 dark:bg-slate-600 dark:hover:bg-slate-500 transition-colors">
                            {{ __('messages.lessons_back_section') }}
                        </a>
                    @endif
                </div>
            </div>

        {{-- Вопросы к уроку (форум) --}}
        <section class="mt-10 pt-8 border-t border-slate-100 dark:border-slate-700">
            <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100 mb-4">{{ __('messages.lessons_questions_heading') }}</h2>
            @forelse($questionThreads ?? [] as $thread)
                <div class="mb-4 p-4 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600">
                    <a href="{{ route('forum.show', $thread) }}" class="font-semibold text-slate-900 dark:text-slate-100 hover:text-primary dark:hover:text-primary-400">{{ $thread->title }}</a>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $thread->user->name }} · {{ $thread->created_at->diffForHumans() }}</p>
                </div>
            @empty
                <p class="text-slate-500 dark:text-slate-400">{{ __('messages.lessons_no_questions') }}</p>
            @endforelse

            <form action="{{ route('lessons.questions.store', [$section, $lesson]) }}" method="POST" class="mt-6 p-4 rounded-xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-600">
                @csrf
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('messages.lessons_ask_question') }}</label>
                <input type="text" name="title" required maxlength="255" placeholder="{{ __('messages.lessons_question_title') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-3 py-2 mb-3">
                <textarea name="body" required rows="3" placeholder="{{ __('messages.lessons_question_body') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-3 py-2 mb-3"></textarea>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-light transition-colors">{{ __('messages.lessons_submit_question') }}</button>
            </form>
        </section>

        {{-- Навигация внизу страницы (дублирует быстрые ссылки) --}}
        <div class="mt-10 pt-6 flex flex-wrap items-center gap-4 border-t border-slate-100 dark:border-slate-700">
            <a href="{{ route('sections.show', $section) }}" class="inline-flex items-center gap-2 text-primary font-semibold hover:underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                {{ __('messages.lessons_back_section') }}
            </a>
            @if($prevLesson ?? null)
                <a href="{{ route('lessons.show', [$section, $prevLesson]) }}" class="inline-flex items-center gap-2 text-slate-700 dark:text-slate-300 font-medium hover:text-emerald-600 dark:hover:text-emerald-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
                    {{ __('messages.lessons_prev_lesson') }}
                </a>
            @endif
            @if($nextLesson ?? null)
                <a href="{{ route('lessons.show', [$section, $nextLesson]) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white rounded-xl font-medium hover:bg-primary-light transition-colors">
                    {{ __('messages.lessons_next_lesson') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                </a>
            @elseif($section->quiz ?? null)
                <a href="{{ route('quiz.show', $section) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-600 text-white rounded-xl font-medium hover:bg-sky-700 transition-colors">
                    {{ __('messages.lessons_to_section_quiz') }}
                </a>
            @endif
        </div>
    </div>
</x-app-layout>
