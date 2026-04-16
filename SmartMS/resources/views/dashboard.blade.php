<x-app-layout>
    <x-slot name="header">{{ __('messages.dashboard_title') }}</x-slot>

    @php
        $student = auth()->user();
        $sectionsCollection = collect($sections ?? []);
        $sectionsTotal = $sectionsTotal ?? $sectionsCollection->count();
        $sectionsPassedCount = $sectionsPassedCount ?? 0;
        $unlockedIds = $unlockedSectionIds ?? [];
        $unlockedCount = $sectionsCollection->filter(fn ($section) => in_array($section->id, $unlockedIds))->count();
        $lockedCount = max($sectionsTotal - $unlockedCount, 0);
        $completionPercent = $sectionsTotal > 0 ? (int) round(($sectionsPassedCount / $sectionsTotal) * 100) : 0;
        $achievementCount = $student?->achievements?->count() ?? 0;
        $certificateCount = $student?->certificates()->count() ?? 0;
        $levelKey = $student?->placementLevelKey();
        $gradeValue = (int) ($student?->grade ?? config('smartlms.default_student_grade', 9));
        $recommendedSection = $sectionsCollection->firstWhere('id', $recommendedSectionId ?? null);
        $nextActionRoute = $recommendedSection ? route('sections.show', $recommendedSection) : route('sections.index');
        $nextActionLabel = $recommendedSection ? __('messages.dashboard_resume_recommended') : __('messages.dashboard_open_program');
        $nextActionDescription = $recommendedSection
            ? __('messages.dashboard_recommended_card_desc', ['title' => $recommendedSection->getTitleForLocale(app()->getLocale())])
            : __('messages.dashboard_open_program_desc');
        $totalLessons = $sectionsCollection->sum(fn ($section) => $section->lessons->count());
        $completedLessonsTotal = collect($progressBySection ?? [])->sum(fn ($meta) => is_array($meta) ? ($meta['completed'] ?? 0) : 0);
        $quizCount = $sectionsCollection->filter(fn ($section) => $section->quiz)->count();
        $timelineSections = $sectionsCollection->take(4);
    @endphp

    @if(session('status'))
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 shadow-sm">
            {{ session('status') }}
        </div>
    @endif

    <div class="space-y-8" x-data="{ sectionFilter: 'all' }">
        <section class="relative overflow-hidden rounded-[32px] border border-slate-200 bg-gradient-to-br from-slate-50 via-white to-sky-50/70 p-6 sm:p-8 shadow-sm">
            <div class="absolute inset-y-0 right-0 w-1/2 bg-[radial-gradient(circle_at_top_right,_rgba(14,165,233,0.16),_transparent_58%)] pointer-events-none"></div>

            <div class="relative grid gap-6 xl:grid-cols-[1.35fr_0.9fr] xl:items-start">
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-4">
                        <span class="inline-flex items-center rounded-full bg-slate-900 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-white">
                            {{ __('messages.dashboard_student_hub') }}
                        </span>
                        <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-medium text-slate-600 border border-slate-200">
                            {{ __('messages.auth_grade_' . $gradeValue) }}
                        </span>
                        @if($levelKey)
                            <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-800 border border-amber-200">
                                {{ __('messages.dashboard_level_' . $levelKey) }}
                            </span>
                        @endif
                    </div>

                    <h2 class="max-w-3xl text-3xl sm:text-4xl font-bold tracking-tight text-slate-900">
                        {{ __('messages.dashboard_student_title') }}
                    </h2>
                    <p class="mt-3 max-w-2xl text-sm sm:text-base text-slate-600 leading-7">
                        {{ __('messages.dashboard_student_subtitle') }}
                    </p>

                    <div class="mt-6 grid gap-3 sm:grid-cols-2 2xl:grid-cols-4">
                        <div class="rounded-2xl border border-white bg-white/90 px-4 py-4 shadow-sm text-fit">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.dashboard_completed') }}</p>
                            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $sectionsPassedCount }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ __('messages.dashboard_sections_passed_short', ['total' => $sectionsTotal]) }}</p>
                        </div>
                        <div class="rounded-2xl border border-white bg-white/90 px-4 py-4 shadow-sm text-fit">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.dashboard_available_now') }}</p>
                            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $unlockedCount }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ __('messages.dashboard_open_sections_now') }}</p>
                        </div>
                        <div class="rounded-2xl border border-white bg-white/90 px-4 py-4 shadow-sm text-fit">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.nav_certificates') }}</p>
                            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $certificateCount }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ __('messages.dashboard_certificates_short') }}</p>
                        </div>
                        <div class="rounded-2xl border border-white bg-white/90 px-4 py-4 shadow-sm text-fit">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.dashboard_achievements') }}</p>
                            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $achievementCount }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ __('messages.dashboard_achievements_short') }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ $nextActionRoute }}" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                            {{ $nextActionLabel }}
                        </a>
                        <a href="{{ route('forum.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            {{ __('messages.nav_forum') }}
                        </a>
                    </div>
                </div>

                <div class="grid gap-4">
                    <div class="rounded-[28px] border border-slate-200 bg-white/95 p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.dashboard_next_step') }}</p>
                                <h3 class="mt-2 text-xl font-bold text-slate-900">
                                    {{ $recommendedSection ? $recommendedSection->getTitleForLocale(app()->getLocale()) : __('messages.dashboard_all_caught_up') }}
                                </h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600">
                                    {{ $nextActionDescription }}
                                </p>
                            </div>
                            <div class="shrink-0 rounded-2xl bg-sky-50 px-4 py-3 text-center border border-sky-100">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-500">{{ __('messages.nav_progress') }}</p>
                                <p class="mt-1 text-2xl font-bold text-slate-900">{{ $completionPercent }}%</p>
                            </div>
                        </div>

                        <div class="mt-4 h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-to-r from-sky-500 to-emerald-500 transition-all duration-500" style="width: {{ $completionPercent }}%"></div>
                        </div>
                        <p class="mt-3 text-sm text-slate-500">
                            {{ __('messages.dashboard_sections_passed', ['passed' => $sectionsPassedCount, 'total' => $sectionsTotal]) }}
                        </p>
                    </div>

                    <div class="rounded-[28px] border border-slate-200 bg-white/95 p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.dashboard_quick_actions') }}</p>
                        <div class="mt-4 grid gap-3 lg:grid-cols-3">
                            <a href="{{ route('sections.index') }}" class="rounded-2xl border border-slate-200 px-4 py-4 transition hover:border-slate-300 hover:bg-slate-50">
                                <p class="font-semibold text-slate-900">{{ __('messages.dashboard_open_program') }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ __('messages.dashboard_open_program_desc') }}</p>
                            </a>
                            <a href="{{ route('certificates.index') }}" class="rounded-2xl border border-slate-200 px-4 py-4 transition hover:border-emerald-300 hover:bg-emerald-50/70">
                                <p class="font-semibold text-slate-900">{{ __('messages.dashboard_open_certificates') }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ __('messages.dashboard_certificates_desc') }}</p>
                            </a>
                            <a href="{{ route('profile.edit') }}" class="rounded-2xl border border-slate-200 px-4 py-4 transition hover:border-sky-300 hover:bg-sky-50/70">
                                <p class="font-semibold text-slate-900">{{ __('messages.nav_profile') }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ __('messages.dashboard_profile_desc') }}</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-4 xl:grid-cols-[0.95fr_1.05fr]">
            <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.dashboard_learning_map') }}</p>
                        <h3 class="mt-2 text-xl font-bold text-slate-900">{{ __('messages.dashboard_learning_map_title') }}</h3>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                        {{ __('messages.dashboard_route_label') }}
                    </span>
                </div>

                <div class="mt-6 space-y-4">
                    <div class="rounded-2xl bg-slate-50 p-4 border border-slate-200">
                        <p class="text-sm font-semibold text-slate-900">{{ __('messages.dashboard_map_current_track') }}</p>
                        <p class="mt-1 text-sm text-slate-600">
                            {{ __('messages.dashboard_track_summary', ['grade' => __('messages.auth_grade_' . $gradeValue), 'level' => $levelKey ? __('messages.dashboard_level_' . $levelKey) : '-']) }}
                        </p>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-3">
                        <div class="rounded-2xl border border-slate-200 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.dashboard_map_done') }}</p>
                            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $sectionsPassedCount }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.dashboard_map_open') }}</p>
                            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $unlockedCount }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.dashboard_map_locked') }}</p>
                            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $lockedCount }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white to-amber-50/60 p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.dashboard_achievements') }}</p>
                <h3 class="mt-2 text-xl font-bold text-slate-900">{{ __('messages.dashboard_motivation_title') }}</h3>
                <p class="mt-2 text-sm text-slate-600">{{ __('messages.dashboard_motivation_desc') }}</p>

                @if($achievementCount > 0)
                    <div class="mt-5 flex flex-wrap gap-2">
                        @foreach($student->achievements->take(6) as $achievement)
                            <span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-sm font-medium text-emerald-800" title="{{ $achievement->description ?? '' }}">
                                {{ $achievement->name }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <div class="mt-5 rounded-2xl border border-dashed border-slate-300 bg-white/80 px-4 py-6 text-sm text-slate-500">
                        {{ __('messages.dashboard_no_achievements_yet') }}
                    </div>
                @endif

                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    <a href="{{ route('certificates.index') }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-4 transition hover:border-emerald-300 hover:bg-emerald-50/70">
                        <p class="font-semibold text-slate-900">{{ __('messages.dashboard_open_certificates') }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ __('messages.dashboard_certificates_desc') }}</p>
                    </a>
                    <a href="{{ route('forum.index') }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-4 transition hover:border-amber-300 hover:bg-amber-50/70">
                        <p class="font-semibold text-slate-900">{{ __('messages.nav_forum') }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ __('messages.dashboard_forum_desc') }}</p>
                    </a>
                </div>
            </div>
        </section>

        <section class="grid gap-4 xl:grid-cols-[1.05fr_0.95fr]">
            <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.dashboard_next_step') }}</p>
                        <h3 class="mt-2 text-xl font-bold text-slate-900">{{ __('messages.dashboard_timeline_title') }}</h3>
                    </div>
                    <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">
                        {{ __('messages.dashboard_route_label') }}
                    </span>
                </div>

                <div class="mt-6 space-y-4">
                    @foreach($timelineSections as $timelineSection)
                        @php
                            $timelineMeta = $progressBySection[$timelineSection->id] ?? ['percent' => 0, 'completed' => 0, 'total' => 0];
                            $timelineUnlocked = in_array($timelineSection->id, $unlockedIds);
                            $timelineDone = $timelineUnlocked
                                ? \App\Http\Controllers\SectionController::isSectionCompletedByStudent($student, $timelineSection, is_array($timelineMeta) ? $timelineMeta : null)
                                : false;
                        @endphp
                        <div class="flex items-start gap-4">
                            <div class="mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl {{ $timelineDone ? 'bg-emerald-100 text-emerald-800' : ($timelineUnlocked ? 'bg-sky-100 text-sky-800' : 'bg-slate-100 text-slate-500') }} text-sm font-bold">
                                {{ $loop->iteration }}
                            </div>
                            <div class="min-w-0 flex-1 rounded-2xl border border-slate-200 bg-slate-50/70 px-4 py-4">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="font-semibold text-slate-900">{{ $timelineSection->getTitleForLocale(app()->getLocale()) }}</p>
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $timelineDone ? 'bg-emerald-100 text-emerald-800' : ($timelineUnlocked ? 'bg-sky-100 text-sky-800' : 'bg-slate-100 text-slate-500') }}">
                                        {{ $timelineDone ? __('messages.dashboard_filter_done') : ($timelineUnlocked ? __('messages.dashboard_filter_active') : __('messages.dashboard_filter_locked')) }}
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-slate-500">
                                    {{ __('messages.dashboard_card_progress_line', ['completed' => is_array($timelineMeta) ? ($timelineMeta['completed'] ?? 0) : 0, 'total' => is_array($timelineMeta) ? ($timelineMeta['total'] ?? 0) : 0]) }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white to-sky-50/60 p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.dashboard_student_hub') }}</p>
                <h3 class="mt-2 text-xl font-bold text-slate-900">{{ __('messages.dashboard_stats_title') }}</h3>
                <p class="mt-2 text-sm text-slate-600">{{ __('messages.dashboard_stats_desc') }}</p>

                <div class="mt-5 grid gap-3 lg:grid-cols-3">
                    <div class="rounded-2xl border border-white bg-white/90 p-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">{{ __('messages.teacher_lessons') }}</p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">{{ $totalLessons }}</p>
                    </div>
                    <div class="rounded-2xl border border-white bg-white/90 p-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">{{ __('messages.teacher_lessons_completed') }}</p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">{{ $completedLessonsTotal }}</p>
                    </div>
                    <div class="rounded-2xl border border-white bg-white/90 p-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">{{ __('messages.teacher_section_quiz') }}</p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">{{ $quizCount }}</p>
                    </div>
                </div>

                <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-4">
                    <p class="text-sm font-semibold text-slate-900">{{ __('messages.dashboard_certificate_focus_title') }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ __('messages.dashboard_certificate_focus_desc') }}</p>
                    <div class="mt-4 flex flex-wrap gap-3">
                        <a href="{{ route('certificates.index') }}" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                            {{ __('messages.dashboard_open_certificates') }}
                        </a>
                        <a href="{{ route('profile.edit') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            {{ __('messages.dashboard_achievements') }}
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">{{ __('messages.dashboard_sections') }}</h2>
                    <p class="mt-2 text-sm text-slate-600">{{ __('messages.dashboard_continue_learning') }}</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button type="button" @click="sectionFilter = 'all'" :class="sectionFilter === 'all' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'" class="rounded-full border px-4 py-2 text-sm font-semibold transition-colors">
                        {{ __('messages.dashboard_filter_all') }}
                    </button>
                    <button type="button" @click="sectionFilter = 'active'" :class="sectionFilter === 'active' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'" class="rounded-full border px-4 py-2 text-sm font-semibold transition-colors">
                        {{ __('messages.dashboard_filter_active') }}
                    </button>
                    <button type="button" @click="sectionFilter = 'done'" :class="sectionFilter === 'done' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'" class="rounded-full border px-4 py-2 text-sm font-semibold transition-colors">
                        {{ __('messages.dashboard_filter_done') }}
                    </button>
                    <button type="button" @click="sectionFilter = 'locked'" :class="sectionFilter === 'locked' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'" class="rounded-full border px-4 py-2 text-sm font-semibold transition-colors">
                        {{ __('messages.dashboard_filter_locked') }}
                    </button>
                </div>
            </div>

            <div class="mt-6 grid gap-5 xl:grid-cols-2">
                @foreach($sectionsCollection as $index => $section)
                    @php
                        $unlocked = in_array($section->id, $unlockedIds);
                        $progressMeta = $progressBySection[$section->id] ?? ['percent' => 0, 'completed' => 0, 'total' => 0];
                        $progress = is_array($progressMeta) ? ($progressMeta['percent'] ?? 0) : 0;
                        $completedLessons = is_array($progressMeta) ? ($progressMeta['completed'] ?? 0) : 0;
                        $totalLessons = is_array($progressMeta) ? ($progressMeta['total'] ?? 0) : 0;
                        $sectionPassed = $unlocked
                            ? \App\Http\Controllers\SectionController::isSectionCompletedByStudent($student, $section, is_array($progressMeta) ? $progressMeta : null)
                            : false;
                        $statusKey = !$unlocked ? 'locked' : ($sectionPassed ? 'done' : 'active');
                        $sectionGrade = (int) ($section->grade ?: $gradeValue);
                        $trackLabel = $section->is_revision ? __('messages.dashboard_level_beginner') : __('messages.dashboard_level_advanced');
                        $cardBorder = !$unlocked
                            ? 'border-slate-200 bg-slate-50/80'
                            : ($sectionPassed ? 'border-emerald-200 bg-emerald-50/50' : 'border-sky-200 bg-white');
                        if ($sectionPassed && $totalLessons > 0) {
                            $progress = 100;
                        }
                    @endphp

                    <div
                        x-show="sectionFilter === 'all' || sectionFilter === '{{ $statusKey }}'"
                        x-transition.opacity.duration.200ms
                        class="rounded-[28px] border {{ $cardBorder }} p-6 shadow-sm"
                        style="display: none;"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-3">
                                    <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-600 border border-slate-200">
                                        {{ __('messages.auth_grade_' . $sectionGrade) }}
                                    </span>
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold border {{ $sectionPassed ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : ($unlocked ? 'bg-sky-100 text-sky-800 border-sky-200' : 'bg-slate-100 text-slate-600 border-slate-200') }}">
                                        {{ $sectionPassed ? __('messages.dashboard_filter_done') : ($unlocked ? __('messages.dashboard_filter_active') : __('messages.dashboard_filter_locked')) }}
                                    </span>
                                    <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-600 border border-slate-200">
                                        {{ $trackLabel }}
                                    </span>
                                </div>

                                <h3 class="text-xl font-bold text-slate-900">{{ $section->getTitleForLocale(app()->getLocale()) }}</h3>
                                <p class="mt-2 text-sm text-slate-600">
                                    {{ __('messages.dashboard_card_progress_line', ['completed' => $completedLessons, 'total' => $totalLessons]) }}
                                </p>
                            </div>

                            <div class="shrink-0 rounded-2xl px-4 py-3 text-center border {{ $sectionPassed ? 'bg-emerald-50 border-emerald-200' : ($unlocked ? 'bg-sky-50 border-sky-200' : 'bg-slate-100 border-slate-200') }}">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] {{ $sectionPassed ? 'text-emerald-600' : ($unlocked ? 'text-sky-600' : 'text-slate-500') }}">{{ __('messages.nav_progress') }}</p>
                                <p class="mt-1 text-2xl font-bold text-slate-900">{{ $progress }}%</p>
                            </div>
                        </div>

                        <div class="mt-4 h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full rounded-full {{ $sectionPassed ? 'bg-emerald-500' : ($unlocked ? 'bg-sky-500' : 'bg-slate-300') }} transition-all duration-500" style="width: {{ $progress }}%"></div>
                        </div>

                        <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="text-sm text-slate-500">
                                {{ trans_choice('messages.dashboard_lessons_n', $section->lessons->count()) }}
                            </div>

                            @if($unlocked)
                                <a href="{{ route('sections.show', $section) }}" class="inline-flex items-center justify-center rounded-2xl px-5 py-3 text-sm font-semibold text-white {{ $sectionPassed ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-slate-900 hover:bg-slate-800' }} transition-colors">
                                    {{ $sectionPassed ? __('messages.dashboard_review_section') : __('messages.dashboard_start') }}
                                </a>
                            @else
                                <div class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-amber-700">
                                    {{ __('messages.sections_forbidden_quiz') }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                <a href="{{ route('sections.index') }}" class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    {{ __('messages.dashboard_all_sections') }}
                </a>
            </div>
        </section>
    </div>
</x-app-layout>
