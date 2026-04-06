<x-app-layout>
    <x-slot name="header">{{ __('messages.teacher_dashboard_title') }}</x-slot>

    <div class="mb-8 grid gap-4 lg:grid-cols-[1.25fr_0.85fr]">
        <div class="rounded-[28px] border border-slate-200 dark:border-slate-700 bg-gradient-to-br from-white via-amber-50/60 to-sky-50/60 dark:from-slate-800 dark:via-slate-800 dark:to-slate-900 p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400 mb-3">{{ __('messages.teacher_workspace_label') }}</p>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-slate-100 mb-2">{{ __('messages.teacher_workspace_title') }}</h2>
            <p class="text-sm text-slate-600 dark:text-slate-300 max-w-2xl">{{ __('messages.teacher_workspace_desc') }}</p>

            <div class="mt-5 grid gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-white/80 dark:border-slate-700 bg-white/80 dark:bg-slate-800/80 px-4 py-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('messages.teacher_student') }}</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $studentsCount ?? 0 }}</p>
                </div>
                <div class="rounded-2xl border border-white/80 dark:border-slate-700 bg-white/80 dark:bg-slate-800/80 px-4 py-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('messages.nav_sections') }}</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $sectionsCount ?? 0 }}</p>
                </div>
                <div class="rounded-2xl border border-white/80 dark:border-slate-700 bg-white/80 dark:bg-slate-800/80 px-4 py-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('messages.teacher_certificates_title') }}</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $certificatesCount ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-[28px] border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400 mb-4">{{ __('messages.teacher_quick_actions') }}</p>
            <div class="grid gap-3">
                <a href="{{ route('teacher.sections.index') }}" class="rounded-2xl border border-slate-200 dark:border-slate-700 px-4 py-4 hover:border-sky-300 hover:bg-sky-50/70 dark:hover:bg-slate-700 transition-colors">
                    <p class="font-semibold text-slate-900 dark:text-slate-100">{{ __('messages.teacher_sections_lessons') }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ __('messages.teacher_sections_lessons_desc') }}</p>
                </a>
                <a href="{{ route('teacher.progress.index') }}" class="rounded-2xl border border-slate-200 dark:border-slate-700 px-4 py-4 hover:border-amber-300 hover:bg-amber-50/70 dark:hover:bg-slate-700 transition-colors">
                    <p class="font-semibold text-slate-900 dark:text-slate-100">{{ __('messages.teacher_progress') }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ __('messages.teacher_progress_desc') }}</p>
                </a>
                <a href="{{ route('teacher.certificates.index') }}" class="rounded-2xl border border-slate-200 dark:border-slate-700 px-4 py-4 hover:border-emerald-300 hover:bg-emerald-50/70 dark:hover:bg-slate-700 transition-colors">
                    <p class="font-semibold text-slate-900 dark:text-slate-100">{{ __('messages.teacher_certificates_title') }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ __('messages.teacher_certificates_desc') }}</p>
                </a>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3 mb-8">
        @foreach(($studentsByGrade ?? []) as $grade => $count)
            <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-5 py-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('messages.auth_grade_' . $grade) }}</p>
                <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $count }}</p>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ __('messages.teacher_students_in_grade') }}</p>
            </div>
        @endforeach
    </div>

    <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100 mb-4">{{ __('messages.teacher_students_by_grade') }}</h2>
    <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800">
        <table class="min-w-full">
            <thead class="bg-slate-50 dark:bg-slate-700">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-800 dark:text-slate-100">{{ __('messages.teacher_student') }}</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-800 dark:text-slate-100">{{ __('messages.dashboard_grade') }}</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-800 dark:text-slate-100">{{ __('messages.dashboard_level') }}</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-800 dark:text-slate-100">{{ __('messages.teacher_lessons_completed') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-600">
                @forelse($students ?? [] as $student)
                    <tr>
                        <td class="px-4 py-3 font-medium text-slate-800 dark:text-slate-100">{{ $student->name }}</td>
                        <td class="px-4 py-3">{{ $student->grade ? __('messages.auth_grade_' . $student->grade) : '—' }}</td>
                        <td class="px-4 py-3">{{ $student->placementLevelKey() ? __('messages.dashboard_level_' . $student->placementLevelKey()) : __('messages.teacher_level_pending') }}</td>
                        <td class="px-4 py-3">{{ $student->lesson_progresses_count ?? 0 }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-slate-500 dark:text-slate-400">{{ __('messages.teacher_no_students') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ route('teacher.progress.index') }}" class="text-primary dark:text-sky-400 hover:underline">{{ __('messages.teacher_view_full_progress') }}</a>
    </p>
</x-app-layout>
