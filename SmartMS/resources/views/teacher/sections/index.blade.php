<x-app-layout>
    <x-slot name="header">{{ __('messages.teacher_sections_index') }}</x-slot>

    @php
        $sectionsCollection = collect($sections ?? []);
        $totalSections = $sectionsCollection->count();
        $beginnerSections = $sectionsCollection->where('is_revision', true)->count();
        $advancedSections = $sectionsCollection->where('is_revision', false)->count();
        $problemSections = $sectionsCollection->filter(function ($section) {
            $questionCount = $section->quiz?->questions?->count() ?? 0;

            return $section->lessons_count === 0
                || blank($section->description)
                || !$section->quiz
                || $questionCount < 3;
        })->count();
    @endphp

    @if(session('status'))
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 shadow-sm">
            {{ session('status') }}
        </div>
    @endif

    <div
        class="space-y-8"
        x-data="{ gradeFilter: 'all', levelFilter: 'all', issueFilter: 'all' }"
    >
        <section class="rounded-[30px] border border-slate-200 bg-gradient-to-br from-white via-amber-50/40 to-sky-50/60 p-6 shadow-sm">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
                <div class="max-w-3xl">
                    <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 border border-slate-200">
                        {{ __('messages.teacher_workspace_label') }}
                    </span>
                    <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-900">
                        {{ __('messages.teacher_sections_lessons') }}
                    </h2>
                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        {{ __('messages.teacher_sections_lessons_desc') }}
                    </p>
                </div>

                <a
                    href="{{ route('teacher.sections.create') }}"
                    class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800"
                >
                    {{ __('messages.teacher_add_section') }}
                </a>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-2 2xl:grid-cols-4">
                <div class="rounded-2xl border border-white bg-white/90 p-4 shadow-sm text-fit">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.nav_sections') }}</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $totalSections }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ __('messages.teacher_sections_stat_total') }}</p>
                </div>
                <div class="rounded-2xl border border-white bg-white/90 p-4 shadow-sm text-fit">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.dashboard_level_beginner') }}</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $beginnerSections }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ __('messages.teacher_sections_stat_beginner') }}</p>
                </div>
                <div class="rounded-2xl border border-white bg-white/90 p-4 shadow-sm text-fit">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.dashboard_level_advanced') }}</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $advancedSections }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ __('messages.teacher_sections_stat_advanced') }}</p>
                </div>
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm text-fit">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-amber-700">{{ __('messages.teacher_sections_stat_attention') }}</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $problemSections }}</p>
                    <p class="mt-1 text-sm text-amber-800">{{ __('messages.teacher_sections_stat_attention_desc') }}</p>
                </div>
            </div>
        </section>

        <section class="rounded-[30px] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.teacher_filters_label') }}</p>
                    <h3 class="mt-2 text-xl font-bold text-slate-900">{{ __('messages.teacher_sections_filter_title') }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ __('messages.teacher_sections_filter_desc') }}</p>
                </div>
                <div class="grid gap-3 lg:grid-cols-3">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">{{ __('messages.dashboard_grade') }}</label>
                        <select x-model="gradeFilter" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700">
                            <option value="all">{{ __('messages.teacher_grade_all') }}</option>
                            <option value="9">{{ __('messages.auth_grade_9') }}</option>
                            <option value="10">{{ __('messages.auth_grade_10') }}</option>
                            <option value="11">{{ __('messages.auth_grade_11') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">{{ __('messages.dashboard_level') }}</label>
                        <select x-model="levelFilter" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700">
                            <option value="all">{{ __('messages.teacher_all_levels') }}</option>
                            <option value="beginner">{{ __('messages.dashboard_level_beginner') }}</option>
                            <option value="advanced">{{ __('messages.dashboard_level_advanced') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">{{ __('messages.teacher_sections_issue_filter') }}</label>
                        <select x-model="issueFilter" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700">
                            <option value="all">{{ __('messages.teacher_sections_issue_all') }}</option>
                            <option value="issues">{{ __('messages.teacher_sections_issue_has') }}</option>
                            <option value="healthy">{{ __('messages.teacher_sections_issue_clean') }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-5">
            @foreach($sectionsCollection as $section)
                @php
                    $gradeKey = $section->grade ? (string) $section->grade : 'all';
                    $levelKey = $section->is_revision ? 'beginner' : 'advanced';
                    $questionCount = $section->quiz?->questions?->count() ?? 0;
                    $issues = collect([
                        $section->lessons_count === 0 ? __('messages.teacher_sections_issue_no_lessons') : null,
                        blank($section->description) ? __('messages.teacher_sections_issue_no_description') : null,
                        !$section->quiz ? __('messages.teacher_sections_issue_no_quiz') : null,
                        $section->quiz && $questionCount < 3 ? __('messages.teacher_sections_issue_few_questions') : null,
                    ])->filter()->values();
                    $hasIssues = $issues->isNotEmpty();
                @endphp

                <article
                    x-show="(gradeFilter === 'all' || gradeFilter === '{{ $gradeKey }}')
                        && (levelFilter === 'all' || levelFilter === '{{ $levelKey }}')
                        && (issueFilter === 'all'
                            || (issueFilter === 'issues' && {{ $hasIssues ? 'true' : 'false' }})
                            || (issueFilter === 'healthy' && {{ $hasIssues ? 'false' : 'true' }}))"
                    x-transition.opacity.duration.200ms
                    class="rounded-[30px] border {{ $hasIssues ? 'border-amber-200 bg-amber-50/50' : 'border-slate-200 bg-white' }} p-6 shadow-sm"
                    style="display: none;"
                >
                    <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600">
                                    {{ $section->grade ? __('messages.auth_grade_' . $section->grade) : __('messages.teacher_grade_all') }}
                                </span>
                                <span class="inline-flex items-center rounded-full border {{ $section->is_revision ? 'border-amber-200 bg-amber-100 text-amber-800' : 'border-sky-200 bg-sky-100 text-sky-800' }} px-3 py-1 text-xs font-semibold">
                                    {{ $section->is_revision ? __('messages.dashboard_level_beginner') : __('messages.dashboard_level_advanced') }}
                                </span>
                                <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600">
                                    {{ __('messages.teacher_order_short') }}: {{ $section->order }}
                                </span>
                                @if($hasIssues)
                                    <span class="inline-flex items-center rounded-full border border-amber-200 bg-white px-3 py-1 text-xs font-semibold text-amber-800">
                                        {{ __('messages.teacher_sections_issue_badge', ['count' => $issues->count()]) }}
                                    </span>
                                @endif
                            </div>

                            <h3 class="mt-4 text-2xl font-bold text-slate-900">
                                {{ $section->getTitleForLocale(app()->getLocale()) }}
                            </h3>

                            <p class="mt-2 max-w-3xl text-sm leading-7 text-slate-600">
                                {{ $section->description ?: __('messages.teacher_sections_missing_description_fallback') }}
                            </p>

                            <div class="mt-5 grid gap-3 lg:grid-cols-3">
                                <div class="rounded-2xl border border-slate-200 bg-white px-4 py-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">{{ __('messages.teacher_lessons') }}</p>
                                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ $section->lessons_count }}</p>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white px-4 py-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">{{ __('messages.teacher_section_quiz') }}</p>
                                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ $questionCount }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $section->quiz ? __('messages.teacher_sections_quiz_ready') : __('messages.teacher_sections_quiz_missing') }}</p>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white px-4 py-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">{{ __('messages.teacher_section_target') }}</p>
                                    <p class="mt-2 text-sm font-semibold text-slate-900">
                                        {{ __('messages.teacher_section_target_summary', ['grade' => $section->grade ? __('messages.auth_grade_' . $section->grade) : __('messages.teacher_grade_all'), 'level' => $section->is_revision ? __('messages.dashboard_level_beginner') : __('messages.dashboard_level_advanced')]) }}
                                    </p>
                                </div>
                            </div>

                            @if($hasIssues)
                                <div class="mt-5 flex flex-wrap gap-2">
                                    @foreach($issues as $issue)
                                        <span class="inline-flex items-center rounded-full border border-amber-200 bg-white px-3 py-1.5 text-xs font-semibold text-amber-800">
                                            {{ $issue }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="flex shrink-0 flex-col gap-3 xl:w-52">
                            <a href="{{ route('teacher.sections.show', $section) }}" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                                {{ __('messages.teacher_open') }}
                            </a>
                            <a href="{{ route('teacher.sections.edit', $section) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                {{ __('messages.teacher_edit') }}
                            </a>
                            <a href="{{ route('teacher.sections.quiz.edit', $section) }}" class="inline-flex items-center justify-center rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm font-semibold text-sky-800 transition hover:bg-sky-100">
                                {{ __('messages.teacher_section_quiz') }}
                            </a>
                            <form action="{{ route('teacher.sections.destroy', $section) }}" method="POST" onsubmit="return confirm('{{ __('messages.teacher_delete_section_confirm') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 transition hover:bg-red-100">
                                    {{ __('messages.teacher_delete') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </section>
    </div>
</x-app-layout>
