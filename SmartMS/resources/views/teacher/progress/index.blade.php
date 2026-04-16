<x-app-layout>
    <x-slot name="header">{{ __('messages.teacher_progress_index') }}</x-slot>

    @if(session('status'))
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 shadow-sm">
            {{ session('status') }}
        </div>
    @endif

    @if(($students ?? collect())->isEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white px-6 py-10 text-center text-slate-500">
            {{ __('messages.teacher_no_students') }}
        </div>
    @else
        <div class="space-y-6">
            @foreach($students ?? [] as $student)
                @php
                    $card = $lessonProgressCards[$student->id] ?? ['completedLessonsCount' => 0, 'totalLessonsCount' => 0, 'sections' => []];
                    $completedPercent = $card['totalLessonsCount'] > 0 ? (int) round(($card['completedLessonsCount'] / $card['totalLessonsCount']) * 100) : 0;
                    $studentMasters = ($masters ?? collect())->where('user_id', $student->id)->values();
                @endphp

                <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 bg-gradient-to-r from-slate-50 via-white to-sky-50/70 px-5 py-5 sm:px-6">
                        <div class="flex flex-col gap-5 2xl:flex-row 2xl:items-start 2xl:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-3">
                                    <h2 class="text-2xl font-bold text-slate-900">{{ $student->name }}</h2>
                                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-sm font-medium text-slate-700">
                                        {{ $student->grade ? __('messages.auth_grade_' . $student->grade) : '-' }}
                                    </span>
                                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-sm font-medium text-slate-700">
                                        {{ $student->placementLevelKey() ? __('messages.dashboard_level_' . $student->placementLevelKey()) : __('messages.teacher_level_pending') }}
                                    </span>
                                </div>

                                <div class="mt-4 grid gap-3 lg:grid-cols-3">
                                    <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-fit">
                                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.nav_progress') }}</p>
                                        <p class="mt-2 text-2xl font-bold text-slate-900">{{ $completedPercent }}%</p>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-fit">
                                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.teacher_lessons_view_label') }}</p>
                                        <p class="mt-2 text-2xl font-bold text-slate-900">{{ $card['completedLessonsCount'] }}/{{ $card['totalLessonsCount'] }}</p>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-fit">
                                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.teacher_master_sections_title') }}</p>
                                        <p class="mt-2 text-2xl font-bold text-slate-900">{{ $studentMasters->count() }}</p>
                                    </div>
                                </div>

                                <div class="mt-4 h-2 overflow-hidden rounded-full bg-slate-100">
                                    <div class="h-full rounded-full bg-gradient-to-r from-sky-500 to-emerald-500 transition-all duration-500" style="width: {{ $completedPercent }}%"></div>
                                </div>
                            </div>

                            <div class="grid w-full max-w-full gap-3 2xl:max-w-[360px]">
                                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-amber-600">{{ __('messages.teacher_master_sections_title') }}</p>
                                            <p class="mt-1 text-sm text-amber-900">{{ __('messages.teacher_master_assign_label') }}</p>
                                        </div>
                                    </div>

                                    @if($studentMasters->isNotEmpty())
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            @foreach($studentMasters as $master)
                                                <span class="inline-flex rounded-full border border-amber-300 bg-white px-3 py-1 text-xs font-semibold text-amber-800">
                                                    {{ $master->section->getTitleForLocale(app()->getLocale()) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="mt-3 text-sm text-amber-800">{{ __('messages.teacher_master_empty') }}</p>
                                    @endif

                                    <form action="{{ route('teacher.progress.master') }}" method="POST" class="mt-4 flex flex-col gap-2 sm:flex-row">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $student->id }}">
                                        <select name="section_id" class="min-w-0 w-full flex-1 rounded-xl border border-amber-200 bg-white px-3 py-2 text-sm text-slate-700">
                                            @foreach($sections ?? [] as $section)
                                                <option value="{{ $section->id }}">{{ $section->getTitleForLocale(app()->getLocale()) }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="w-full rounded-xl bg-amber-500 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-amber-600 sm:w-auto">
                                            {{ __('messages.teacher_assign_master_short') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-5 px-5 py-5 sm:px-6 xl:grid-cols-[1.35fr_0.95fr]">
                        <div class="space-y-4">
                            @forelse($card['sections'] as $sectionData)
                                @php
                                    $sectionCompletedCount = collect($sectionData['lessons'])->where('completed', true)->count();
                                    $sectionLessonsCount = collect($sectionData['lessons'])->count();
                                    $sectionPercent = $sectionLessonsCount > 0 ? (int) round(($sectionCompletedCount / $sectionLessonsCount) * 100) : 0;
                                @endphp
                                <details class="group rounded-2xl border border-slate-200 bg-slate-50/80 p-4" open>
                                    <summary class="flex cursor-pointer list-none flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                                        <div>
                                            <h3 class="font-semibold text-slate-900">{{ $sectionData['section']->getTitleForLocale(app()->getLocale()) }}</h3>
                                            <p class="mt-1 text-sm text-slate-500">
                                                {{ __('messages.teacher_lessons_progress_label', ['completed' => $sectionCompletedCount, 'total' => $sectionLessonsCount]) }}
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="w-28">
                                                <div class="h-2 overflow-hidden rounded-full bg-slate-200">
                                                    <div class="h-full rounded-full bg-gradient-to-r from-sky-500 to-emerald-500" style="width: {{ $sectionPercent }}%"></div>
                                                </div>
                                            </div>
                                            <span class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-600 border border-slate-200">
                                                {{ $sectionPercent }}%
                                            </span>
                                        </div>
                                    </summary>

                                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                        @foreach($sectionData['lessons'] as $lessonData)
                                            <div class="rounded-2xl border px-3 py-3 {{ $lessonData['completed'] ? 'border-emerald-200 bg-emerald-50/80' : ($lessonData['unlocked'] ? 'border-sky-200 bg-sky-50/80' : 'border-slate-200 bg-white') }}">
                                                <div class="flex items-start justify-between gap-2">
                                                    <p class="text-sm font-semibold text-slate-900">{{ $lessonData['lesson']->getTitleForLocale(app()->getLocale()) }}</p>
                                                    <span class="shrink-0 inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $lessonData['completed'] ? 'bg-emerald-100 text-emerald-800' : ($lessonData['unlocked'] ? 'bg-sky-100 text-sky-800' : 'bg-slate-200 text-slate-600') }}">
                                                        {{ $lessonData['completed'] ? __('messages.teacher_passed') : ($lessonData['unlocked'] ? __('messages.teacher_lesson_open') : __('messages.lessons_locked')) }}
                                                    </span>
                                                </div>
                                                @if(!$lessonData['unlocked'] && $lessonData['unlock_after_lesson'])
                                                    <p class="mt-2 text-xs text-slate-500">
                                                        {{ __('messages.lessons_locked_after_hint', ['lesson' => $lessonData['unlock_after_lesson']->getTitleForLocale(app()->getLocale())]) }}
                                                    </p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </details>
                            @empty
                                <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-4 text-sm text-slate-500">
                                    {{ __('messages.teacher_no_lessons_for_student') }}
                                </div>
                            @endforelse
                        </div>

                        <div class="space-y-4">
                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.teacher_achievements') }}</p>
                                <p class="mt-2 text-sm text-slate-500">{{ __('messages.teacher_achievement_panel_hint') }}</p>
                                @if(isset($achievements) && $achievements->isNotEmpty())
                                    <form action="{{ route('teacher.students.achievements.award', $student) }}" method="POST" class="mt-4 flex flex-wrap gap-2">
                                        @csrf
                                        <select name="achievement_key" class="min-w-0 flex-1 rounded-xl border border-gray-300 px-3 py-2 text-sm">
                                            @foreach($achievements as $achievement)
                                                <option value="{{ $achievement->key }}">{{ $achievement->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                                            {{ __('messages.teacher_award_achievement') }}
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.teacher_feedback') }}</p>
                                <p class="mt-2 text-sm text-slate-500">{{ __('messages.teacher_feedback_panel_hint') }}</p>
                                <form action="{{ route('teacher.progress.feedback') }}" method="POST" class="mt-4">
                                    @csrf
                                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                                    <textarea name="body" rows="4" placeholder="{{ __('messages.teacher_feedback_placeholder') }}" class="min-h-[132px] w-full rounded-2xl border border-gray-300 px-3 py-3 text-sm">{{ isset($feedbacks[$student->id]) ? $feedbacks[$student->id]->body : '' }}</textarea>
                                    <button type="submit" class="mt-3 inline-flex rounded-xl bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-light">
                                        {{ __('messages.teacher_save_feedback') }}
                                    </button>
                                </form>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-xs text-slate-500">
                                <div class="flex flex-wrap gap-3">
                                    <span>{{ __('messages.teacher_legend_completed') }}</span>
                                    <span>{{ __('messages.teacher_legend_open') }}</span>
                                    <span>{{ __('messages.teacher_legend_locked') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-app-layout>
