<x-app-layout>
    <x-slot name="header">{{ $section->getTitleForLocale(app()->getLocale()) }} {{ __('messages.teacher_section_header') }}</x-slot>

    @php
        $lessonCollection = collect($section->lessons ?? []);
        $quizQuestionCount = $section->quiz?->questions?->count() ?? 0;
        $issues = collect([
            $lessonCollection->isEmpty() ? __('messages.teacher_sections_issue_no_lessons') : null,
            blank($section->description) ? __('messages.teacher_sections_issue_no_description') : null,
            !$section->quiz ? __('messages.teacher_sections_issue_no_quiz') : null,
            $section->quiz && $quizQuestionCount < 3 ? __('messages.teacher_sections_issue_few_questions') : null,
        ])->filter()->values();
    @endphp

    @if(session('status'))
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 shadow-sm">
            {{ session('status') }}
        </div>
    @endif

    <div class="space-y-8">
        <section class="rounded-[30px] border border-slate-200 bg-gradient-to-br from-white via-sky-50/50 to-amber-50/40 p-6 shadow-sm">
            <div class="flex flex-col gap-6 2xl:flex-row 2xl:items-start 2xl:justify-between">
                <div class="max-w-3xl">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600">
                            {{ $section->grade ? __('messages.auth_grade_' . $section->grade) : __('messages.teacher_grade_all') }}
                        </span>
                        <span class="inline-flex items-center rounded-full border {{ $section->is_revision ? 'border-amber-200 bg-amber-100 text-amber-800' : 'border-sky-200 bg-sky-100 text-sky-800' }} px-3 py-1 text-xs font-semibold">
                            {{ $section->is_revision ? __('messages.dashboard_level_beginner') : __('messages.dashboard_level_advanced') }}
                        </span>
                    </div>

                    <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-900">
                        {{ $section->getTitleForLocale(app()->getLocale()) }}
                    </h2>
                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        {{ $section->description ?: __('messages.teacher_sections_missing_description_fallback') }}
                    </p>
                </div>

                <div class="grid w-full gap-3 sm:grid-cols-2 2xl:grid-cols-1 2xl:w-52">
                    <a href="{{ route('teacher.sections.lessons.create', $section) }}" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                        {{ __('messages.teacher_add_lesson') }}
                    </a>
                    <a href="{{ route('teacher.sections.quiz.edit', $section) }}" class="inline-flex items-center justify-center rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm font-semibold text-sky-800 transition hover:bg-sky-100">
                        {{ __('messages.teacher_section_quiz') }}
                    </a>
                </div>
            </div>

            <div class="mt-6 grid gap-4 lg:grid-cols-3">
                <div class="rounded-2xl border border-white bg-white/90 p-4 shadow-sm text-fit">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.teacher_lessons') }}</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $lessonCollection->count() }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ __('messages.teacher_sections_lessons_summary') }}</p>
                </div>
                <div class="rounded-2xl border border-white bg-white/90 p-4 shadow-sm text-fit">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.teacher_section_quiz') }}</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $quizQuestionCount }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $section->quiz ? __('messages.teacher_sections_quiz_ready') : __('messages.teacher_sections_quiz_missing') }}</p>
                </div>
                <div class="rounded-2xl border {{ $issues->isNotEmpty() ? 'border-amber-200 bg-amber-50' : 'border-emerald-200 bg-emerald-50' }} p-4 shadow-sm text-fit">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] {{ $issues->isNotEmpty() ? 'text-amber-700' : 'text-emerald-700' }}">{{ __('messages.teacher_sections_health_title') }}</p>
                    <p class="mt-2 text-lg font-bold text-slate-900">
                        {{ $issues->isNotEmpty() ? __('messages.teacher_sections_need_attention') : __('messages.teacher_sections_ready_state') }}
                    </p>
                    <p class="mt-1 text-sm {{ $issues->isNotEmpty() ? 'text-amber-800' : 'text-emerald-800' }}">
                        {{ $issues->isNotEmpty() ? __('messages.teacher_sections_health_desc_bad') : __('messages.teacher_sections_health_desc_good') }}
                    </p>
                </div>
            </div>

            @if($issues->isNotEmpty())
                <div class="mt-5 flex flex-wrap gap-2">
                    @foreach($issues as $issue)
                        <span class="inline-flex items-center rounded-full border border-amber-200 bg-white px-3 py-1.5 text-xs font-semibold text-amber-800">
                            {{ $issue }}
                        </span>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="rounded-[30px] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.teacher_lessons') }}</p>
                    <h3 class="mt-2 text-xl font-bold text-slate-900">{{ __('messages.teacher_sections_lessons_overview') }}</h3>
                </div>
                <a href="{{ route('teacher.sections.edit', $section) }}" class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    {{ __('messages.teacher_edit') }}
                </a>
            </div>

            <div class="mt-6 grid gap-4">
                @forelse($lessonCollection as $lesson)
                    @php
                        $lessonWarnings = collect([
                            blank($lesson->content) ? __('messages.teacher_lesson_issue_empty_content') : null,
                            blank($lesson->file_path) && blank($lesson->video_url) && blank($lesson->video_id) ? __('messages.teacher_lesson_issue_no_materials') : null,
                        ])->filter();
                    @endphp

                    <article class="rounded-3xl border {{ $lessonWarnings->isNotEmpty() ? 'border-amber-200 bg-amber-50/40' : 'border-slate-200 bg-slate-50/60' }} p-5">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-600 border border-slate-200">
                                        {{ __('messages.teacher_order_short') }}: {{ $lesson->order }}
                                    </span>
                                    <span class="inline-flex items-center rounded-full {{ $lesson->unlockAfterLesson ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200' }} px-3 py-1 text-xs font-semibold">
                                        {{ $lesson->unlockAfterLesson ? __('messages.teacher_unlock_after_display', ['lesson' => $lesson->unlockAfterLesson->getTitleForLocale(app()->getLocale())]) : __('messages.teacher_unlock_after_open') }}
                                    </span>
                                </div>

                                <h4 class="mt-3 text-xl font-bold text-slate-900">{{ $lesson->getTitleForLocale(app()->getLocale()) }}</h4>
                                <p class="mt-2 text-sm leading-7 text-slate-600">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($lesson->getContentForLocale(app()->getLocale()) ?? ''), 180) ?: __('messages.teacher_lesson_issue_empty_content') }}
                                </p>

                                <div class="mt-4 flex flex-wrap gap-2">
                                    @if($lesson->file_path)
                                        <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600">
                                            {{ __('messages.teacher_lesson_material_file') }}
                                        </span>
                                    @endif
                                    @if($lesson->video_url || $lesson->video_id)
                                        <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600">
                                            {{ __('messages.teacher_lesson_material_video') }}
                                        </span>
                                    @endif
                                    @foreach($lessonWarnings as $warning)
                                        <span class="inline-flex items-center rounded-full border border-amber-200 bg-white px-3 py-1 text-xs font-semibold text-amber-800">
                                            {{ $warning }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="flex shrink-0 flex-col gap-3 lg:w-44">
                                <a href="{{ route('teacher.lessons.edit', $lesson) }}" class="inline-flex items-center justify-center rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-slate-700 border border-slate-200 transition hover:bg-slate-50">
                                    {{ __('messages.teacher_edit_lesson') }}
                                </a>
                                <form action="{{ route('teacher.lessons.destroy', $lesson) }}" method="POST" onsubmit="return confirm('{{ __('messages.teacher_delete_confirm') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 transition hover:bg-red-100">
                                        {{ __('messages.teacher_delete') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center text-sm text-slate-500">
                        {{ __('messages.teacher_sections_issue_no_lessons') }}
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
