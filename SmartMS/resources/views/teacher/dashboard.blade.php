<x-app-layout>
    <x-slot name="header">{{ __('messages.teacher_dashboard_title') }}</x-slot>

    <div class="grid gap-6 md:grid-cols-2 mb-8">
        <a href="{{ route('teacher.sections.index') }}" class="block p-6 rounded-2xl border-2 border-primary bg-white dark:bg-slate-800 dark:border-slate-600 hover:bg-primary-pale dark:hover:bg-slate-700 transition-colors">
            <h3 class="font-bold text-slate-800 dark:text-slate-100">{{ __('messages.teacher_sections_lessons') }}</h3>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">{{ __('messages.teacher_sections_lessons_desc') }}</p>
        </a>
        <a href="{{ route('teacher.progress.index') }}" class="block p-6 rounded-2xl border-2 border-accent bg-white dark:bg-slate-800 dark:border-slate-600 hover:bg-accent-pale dark:hover:bg-slate-700 transition-colors">
            <h3 class="font-bold text-slate-800 dark:text-slate-100">{{ __('messages.teacher_progress') }}</h3>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">{{ __('messages.teacher_progress_desc') }}</p>
        </a>
    </div>

    <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100 mb-4">{{ __('messages.teacher_students_by_grade') }}</h2>
    <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800">
        <table class="min-w-full">
            <thead class="bg-slate-50 dark:bg-slate-700">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-800 dark:text-slate-100">{{ __('messages.teacher_student') }}</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-800 dark:text-slate-100">{{ __('messages.dashboard_grade') }}</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-800 dark:text-slate-100">{{ __('messages.teacher_lessons_completed') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-600">
                @forelse($students ?? [] as $student)
                    <tr>
                        <td class="px-4 py-3 font-medium text-slate-800 dark:text-slate-100">{{ $student->name }}</td>
                        <td class="px-4 py-3">{{ $student->grade ? __('messages.auth_grade_' . $student->grade) : '—' }}</td>
                        <td class="px-4 py-3">{{ $student->lesson_progresses_count ?? 0 }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-slate-500 dark:text-slate-400">{{ __('messages.teacher_no_students') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ route('teacher.progress.index') }}" class="text-primary dark:text-sky-400 hover:underline">{{ __('messages.teacher_view_full_progress') }}</a>
    </p>
</x-app-layout>
