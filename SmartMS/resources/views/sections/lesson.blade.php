<x-app-layout>
    <x-slot name="header">{{ $lesson->getTitleForLocale(app()->getLocale()) }} - SmartLMS</x-slot>

    @php
        $student = auth()->user();
        $levelKey = $student?->placementLevelKey();
        $sectionTrackLabel = $section->is_revision ? __('messages.dashboard_level_beginner') : __('messages.dashboard_level_advanced');
        $sectionGradeLabel = $section->grade ? __('messages.auth_grade_' . $section->grade) : __('messages.teacher_grade_all');
        $loc = app()->getLocale();
        $embedUrl = $lesson->youtubeEmbedUrlForLocale($loc);
        $watchUrl = $lesson->youtubeWatchUrlForLocale($loc);
        $otherUrl = $lesson->nonYoutubeVideoUrlForLocale($loc);
        $showVideoBlock = $lesson->hasVideoDataForLocale($loc);
        $content = $lesson->getContentForLocale($loc) ?? '';
        $hasPdf = true;
        $hasFile = filled($lesson->file_path);
        $hasChecklist = filled(trim(strip_tags(\Illuminate\Support\Str::markdown($content))));
        $hasQuestions = collect($questionThreads ?? [])->isNotEmpty();
        $resourceCount = collect([$showVideoBlock, $hasFile, $hasPdf])->filter()->count();
    @endphp

    <div class="space-y-8">
        <section class="rounded-[32px] border border-slate-200 bg-gradient-to-br from-white via-sky-50/40 to-emerald-50/30 p-6 shadow-sm">
            <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600">{{ $sectionGradeLabel }}</span>
                        <span class="inline-flex items-center rounded-full {{ $section->is_revision ? 'border-amber-200 bg-amber-100 text-amber-800' : 'border-sky-200 bg-sky-100 text-sky-800' }} px-3 py-1 text-xs font-semibold">{{ $sectionTrackLabel }}</span>
                        @if($lessonCompleted)
                            <span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">
                                {{ __('messages.teacher_legend_completed') }}
                            </span>
                        @endif
                    </div>

                    <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-900">{{ $lesson->getTitleForLocale($loc) }}</h1>
                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        {{ __('messages.lessons_context_summary', ['section' => $section->getTitleForLocale($loc), 'level' => $levelKey ? __('messages.dashboard_level_' . $levelKey) : $sectionTrackLabel]) }}
                    </p>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ route('sections.show', $section) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            {{ __('messages.lessons_back_section') }}
                        </a>
                        @if($nextLesson)
                            <a href="{{ route('lessons.show', [$section, $nextLesson]) }}" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                                {{ __('messages.lessons_next_lesson') }}
                            </a>
                        @elseif($section->quiz)
                            <a href="{{ route('quiz.show', $section) }}" class="inline-flex items-center justify-center rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-sky-700">
                                {{ __('messages.lessons_to_section_quiz') }}
                            </a>
                        @endif
                    </div>
                </div>

                <div class="grid gap-4">
                    <div class="rounded-[28px] border border-white bg-white/90 p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.dashboard_next_step') }}</p>
                        <h3 class="mt-2 text-xl font-bold text-slate-900">{{ __('messages.lessons_continue_course') }}</h3>
                        <p class="mt-2 text-sm text-slate-600">
                            @if($nextLesson)
                                {{ $nextLesson->getTitleForLocale($loc) }}
                            @elseif($lockedNextLesson)
                                {{ __('messages.lessons_next_locked_hint', ['lesson' => $lockedNextLesson->getTitleForLocale($loc)]) }}
                            @elseif($section->quiz)
                                {{ __('messages.lessons_quiz_gate_hint') }}
                            @else
                                {{ __('messages.lessons_all_lessons_in_section_done') }}
                            @endif
                        </p>
                    </div>
                    <div class="rounded-[28px] border border-white bg-white/90 p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.teacher_lesson_materials_title') }}</p>
                        <div class="mt-3 grid gap-3 sm:grid-cols-3">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3 text-center">
                                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">{{ __('messages.lessons_video') }}</p>
                                <p class="mt-2 text-lg font-bold text-slate-900">{{ $showVideoBlock ? '1' : '0' }}</p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3 text-center">
                                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">{{ __('messages.teacher_file') }}</p>
                                <p class="mt-2 text-lg font-bold text-slate-900">{{ $hasFile ? '1' : '0' }}</p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3 text-center">
                                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">{{ __('messages.lessons_download_pdf_handout') }}</p>
                                <p class="mt-2 text-lg font-bold text-slate-900">{{ $resourceCount }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[0.92fr_1.08fr]">
            <aside class="space-y-6">
                <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.teacher_lessons') }}</p>
                    <h2 class="mt-2 text-xl font-bold text-slate-900">{{ __('messages.lessons_checklist_title') }}</h2>
                    <div class="mt-5 space-y-3">
                        <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full {{ $hasChecklist ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">1</span>
                            <span class="text-sm font-medium text-slate-700">{{ __('messages.lessons_checklist_read') }}</span>
                        </div>
                        <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full {{ $showVideoBlock ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">2</span>
                            <span class="text-sm font-medium text-slate-700">{{ __('messages.lessons_checklist_watch') }}</span>
                        </div>
                        <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full {{ $hasFile ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">3</span>
                            <span class="text-sm font-medium text-slate-700">{{ __('messages.lessons_checklist_download') }}</span>
                        </div>
                        <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full {{ $lessonCompleted ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">4</span>
                            <span class="text-sm font-medium text-slate-700">{{ __('messages.lessons_checklist_complete') }}</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.teacher_lesson_materials_title') }}</p>
                    <h3 class="mt-2 text-xl font-bold text-slate-900">{{ __('messages.lessons_resources_title') }}</h3>
                    <div class="mt-4 space-y-3">
                        <a href="{{ route('lessons.pdf', [$section, $lesson]) }}" target="_blank" rel="noopener" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 transition hover:bg-slate-100">
                            <span class="text-sm font-semibold text-slate-800">{{ __('messages.lessons_download_pdf_handout') }}</span>
                            <span class="text-xs font-semibold text-slate-500">PDF</span>
                        </a>
                        @if($hasFile)
                            <a href="{{ asset('storage/' . $lesson->file_path) }}" download="{{ basename($lesson->file_path) }}" target="_blank" rel="noopener" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 transition hover:bg-slate-100">
                                <span class="text-sm font-semibold text-slate-800">{{ __('messages.lessons_download_materials') }}</span>
                                <span class="text-xs font-semibold text-slate-500">{{ pathinfo($lesson->file_path, PATHINFO_EXTENSION) }}</span>
                            </a>
                        @endif
                        @if($watchUrl)
                            <a href="{{ $watchUrl }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 transition hover:bg-slate-100">
                                <span class="text-sm font-semibold text-slate-800">{{ __('messages.lessons_video_open_on_youtube') }}</span>
                                <span class="text-xs font-semibold text-slate-500">YouTube</span>
                            </a>
                        @elseif($otherUrl)
                            <a href="{{ $otherUrl }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 transition hover:bg-slate-100">
                                <span class="text-sm font-semibold text-slate-800">{{ __('messages.lessons_video_open_external') }}</span>
                                <span class="text-xs font-semibold text-slate-500">URL</span>
                            </a>
                        @endif
                    </div>
                </div>
            </aside>

            <div class="space-y-6">
                <section class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="prose prose-slate max-w-none text-slate-700">
                        {!! \Illuminate\Support\Str::markdown($content) !!}
                    </div>
                </section>

                @if($showVideoBlock)
                    <section class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-xl font-bold text-slate-900">{{ __('messages.lessons_video') }}</h3>
                        @if($embedUrl)
                            <div class="mt-4 aspect-video w-full overflow-hidden rounded-3xl bg-black ring-1 ring-slate-200">
                                <iframe
                                    class="h-full w-full"
                                    src="{{ $embedUrl }}"
                                    title="{{ $lesson->getTitleForLocale($loc) }}"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    referrerpolicy="strict-origin-when-cross-origin"
                                    allowfullscreen
                                    loading="lazy"
                                ></iframe>
                            </div>
                        @elseif($otherUrl)
                            <p class="mt-3 text-sm text-slate-600">{{ __('messages.lessons_video_external_hint') }}</p>
                            <a href="{{ $otherUrl }}" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex items-center justify-center rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-sky-700">
                                {{ __('messages.lessons_video_open_external') }}
                            </a>
                        @else
                            <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-4 text-sm text-amber-900">
                                {{ __('messages.lessons_video_unavailable') }}
                            </div>
                        @endif
                    </section>
                @endif

                @if(session('status'))
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 shadow-sm">
                        {{ session('status') }}
                    </div>
                @endif

                <section class="rounded-[28px] border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-6 shadow-sm" x-data="lessonComplete({{ $lessonCompleted ? 'true' : 'false' }}, '{{ route('lessons.complete', [$section, $lesson]) }}', '{{ addslashes(__('messages.lessons_already_completed')) }}', '{{ addslashes(__('messages.lessons_complete_btn')) }}')">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">{{ __('messages.dashboard_next_step') }}</p>
                            <h3 class="mt-2 text-xl font-bold text-slate-900">{{ __('messages.lessons_complete_title') }}</h3>
                            <p class="mt-2 text-sm text-slate-600">{{ __('messages.lessons_complete_desc') }}</p>
                        </div>

                        <div>
                            <template x-if="completed">
                                <p class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white">
                                    <span x-text="completedLabel"></span>
                                </p>
                            </template>
                            <template x-if="!completed && !loading">
                                <button type="button" @click="submitComplete()" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                                    <span x-text="completeBtnLabel"></span>
                                </button>
                            </template>
                            <template x-if="loading">
                                <span class="inline-flex items-center justify-center rounded-2xl bg-slate-200 px-5 py-3 text-sm font-semibold text-slate-700">{{ __('messages.lessons_complete_btn') }}...</span>
                            </template>
                        </div>
                    </div>

                    <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                        @if($prevLesson)
                            <a href="{{ route('lessons.show', [$section, $prevLesson]) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                {{ __('messages.lessons_prev_lesson') }}
                            </a>
                        @endif
                        @if($nextLesson)
                            <a href="{{ route('lessons.show', [$section, $nextLesson]) }}" class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">
                                {{ __('messages.lessons_next_lesson') }}: {{ $nextLesson->getTitleForLocale($loc) }}
                            </a>
                        @elseif($lockedNextLesson)
                            <p class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                                {{ __('messages.lessons_next_locked_hint', ['lesson' => $lockedNextLesson->getTitleForLocale($loc)]) }}
                            </p>
                        @elseif($section->quiz)
                            <a href="{{ route('quiz.show', $section) }}" class="inline-flex items-center justify-center rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-sky-700">
                                {{ __('messages.lessons_to_section_quiz') }}
                            </a>
                        @endif
                    </div>
                </section>

                <section class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.lessons_questions_heading') }}</p>
                            <h3 class="mt-2 text-xl font-bold text-slate-900">{{ __('messages.forum_discussion_title') }}</h3>
                        </div>
                        <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">
                            {{ collect($questionThreads ?? [])->count() }}
                        </span>
                    </div>

                    <div class="mt-5 space-y-3">
                        @forelse($questionThreads ?? [] as $thread)
                            <a href="{{ route('forum.show', $thread) }}" class="block rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 transition hover:bg-slate-100">
                                <p class="font-semibold text-slate-900">{{ $thread->title }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $thread->user->name }} / {{ $thread->created_at->diffForHumans() }}</p>
                            </a>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-sm text-slate-500">
                                {{ __('messages.lessons_no_questions') }}
                            </div>
                        @endforelse
                    </div>

                    <form action="{{ route('lessons.questions.store', [$section, $lesson]) }}" method="POST" class="mt-6 rounded-3xl border border-slate-200 bg-slate-50 p-5">
                        @csrf
                        <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.lessons_ask_question') }}</label>
                        <input type="text" name="title" required maxlength="255" placeholder="{{ __('messages.lessons_question_title') }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-800">
                        <textarea name="body" required rows="4" placeholder="{{ __('messages.lessons_question_body') }}" class="mt-3 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-800"></textarea>
                        <button type="submit" class="mt-3 inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                            {{ __('messages.lessons_submit_question') }}
                        </button>
                    </form>
                </section>
            </div>
        </section>
    </div>
</x-app-layout>
