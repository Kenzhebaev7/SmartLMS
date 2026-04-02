<x-app-layout>
    <x-slot name="header">{{ __('messages.admin_dashboard_title') }}</x-slot>

    @if(session('status'))
        <div class="mb-6 rounded-xl bg-primary-50 border border-primary-200 px-4 py-3 text-primary-light">{{ session('status') }}</div>
    @endif

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3 mb-8">
        <div class="p-6 rounded-2xl border-2 border-slate-200 bg-white">
            <p class="text-sm text-slate-500">{{ __('messages.admin_users') }}</p>
            <p class="text-2xl font-bold text-slate-800">{{ $stats['users'] ?? 0 }}</p>
        </div>
        <div class="p-6 rounded-2xl border-2 border-slate-200 bg-white">
            <p class="text-sm text-slate-500">{{ __('messages.admin_students') }}</p>
            <p class="text-2xl font-bold text-slate-800">{{ $stats['students'] ?? 0 }}</p>
        </div>
        <div class="p-6 rounded-2xl border-2 border-slate-200 bg-white">
            <p class="text-sm text-slate-500">{{ __('messages.admin_teachers') }}</p>
            <p class="text-2xl font-bold text-slate-800">{{ $stats['teachers'] ?? 0 }}</p>
        </div>
        <div class="p-6 rounded-2xl border-2 border-slate-200 bg-white">
            <p class="text-sm text-slate-500">{{ __('messages.admin_sections') }}</p>
            <p class="text-2xl font-bold text-slate-800">{{ $stats['sections'] ?? 0 }}</p>
        </div>
        <div class="p-6 rounded-2xl border-2 border-slate-200 bg-white">
            <p class="text-sm text-slate-500">{{ __('messages.admin_threads') }}</p>
            <p class="text-2xl font-bold text-slate-800">{{ $stats['threads'] ?? 0 }}</p>
        </div>
        <div class="p-6 rounded-2xl border-2 border-slate-200 bg-white">
            <p class="text-sm text-slate-500">{{ __('messages.admin_quiz_attempts') }}</p>
            <p class="text-2xl font-bold text-slate-800">{{ $stats['quiz_attempts'] ?? 0 }}</p>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <a href="{{ route('admin.users.index') }}" class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 flex flex-col gap-1 hover:border-primary hover:bg-primary-pale dark:hover:bg-slate-700 transition-colors">
            <span class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('messages.admin_block_users') }}</span>
            <span class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ __('messages.admin_manage_users') }}</span>
        </a>
        <a href="{{ route('teacher.sections.index') }}" class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 flex flex-col gap-1 hover:border-primary hover:bg-primary-pale dark:hover:bg-slate-700 transition-colors">
            <span class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('messages.admin_block_content') }}</span>
            <span class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ __('messages.admin_manage_content') }}</span>
        </a>
        <a href="{{ route('forum.index') }}" class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 flex flex-col gap-1 hover:border-primary hover:bg-primary-pale dark:hover:bg-slate-700 transition-colors">
            <span class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('messages.admin_block_forum') }}</span>
            <span class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ __('messages.admin_manage_forum') }}</span>
        </a>
        <a href="{{ route('exam-trainer.index') }}" class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 flex flex-col gap-1 hover:border-primary hover:bg-primary-pale dark:hover:bg-slate-700 transition-colors">
            <span class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('messages.admin_block_exam') }}</span>
            <span class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ __('messages.admin_manage_exam') }}</span>
        </a>
        <a href="{{ route('search.index') }}" class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 flex flex-col gap-1 hover:border-primary hover:bg-primary-pale dark:hover:bg-slate-700 transition-colors">
            <span class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('messages.admin_block_search') }}</span>
            <span class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ __('messages.admin_manage_search') }}</span>
        </a>
        <a href="{{ route('admin.settings.index') }}" class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 flex flex-col gap-1 hover:border-primary hover:bg-primary-pale dark:hover:bg-slate-700 transition-colors">
            <span class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('messages.admin_block_settings') }}</span>
            <span class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ __('messages.admin_settings') }}</span>
        </a>
    </div>
</x-app-layout>
