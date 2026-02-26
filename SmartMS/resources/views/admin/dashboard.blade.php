<x-app-layout>
    <x-slot name="header">{{ __('admin.dashboard_title') }}</x-slot>

    @if(session('status'))
        <div class="mb-6 rounded-xl bg-primary-50 border border-primary-200 px-4 py-3 text-primary-light">{{ session('status') }}</div>
    @endif

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3 mb-8">
        <div class="p-6 rounded-2xl border-2 border-slate-200 bg-white">
            <p class="text-sm text-slate-500">{{ __('admin.users') }}</p>
            <p class="text-2xl font-bold text-slate-800">{{ $stats['users'] ?? 0 }}</p>
        </div>
        <div class="p-6 rounded-2xl border-2 border-slate-200 bg-white">
            <p class="text-sm text-slate-500">{{ __('admin.students') }}</p>
            <p class="text-2xl font-bold text-slate-800">{{ $stats['students'] ?? 0 }}</p>
        </div>
        <div class="p-6 rounded-2xl border-2 border-slate-200 bg-white">
            <p class="text-sm text-slate-500">{{ __('admin.teachers') }}</p>
            <p class="text-2xl font-bold text-slate-800">{{ $stats['teachers'] ?? 0 }}</p>
        </div>
        <div class="p-6 rounded-2xl border-2 border-slate-200 bg-white">
            <p class="text-sm text-slate-500">{{ __('admin.sections') }}</p>
            <p class="text-2xl font-bold text-slate-800">{{ $stats['sections'] ?? 0 }}</p>
        </div>
        <div class="p-6 rounded-2xl border-2 border-slate-200 bg-white">
            <p class="text-sm text-slate-500">{{ __('admin.threads') }}</p>
            <p class="text-2xl font-bold text-slate-800">{{ $stats['threads'] ?? 0 }}</p>
        </div>
        <div class="p-6 rounded-2xl border-2 border-slate-200 bg-white">
            <p class="text-sm text-slate-500">{{ __('admin.quiz_attempts') }}</p>
            <p class="text-2xl font-bold text-slate-800">{{ $stats['quiz_attempts'] ?? 0 }}</p>
        </div>
    </div>

    <div class="flex flex-wrap gap-3">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-6 py-3 bg-slate-800 text-white rounded-xl font-semibold hover:bg-slate-700">{{ __('admin.manage_users') }} →</a>
        <a href="{{ route('admin.settings.index') }}" class="inline-flex items-center px-6 py-3 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 rounded-xl font-semibold hover:bg-slate-100 dark:hover:bg-slate-700">{{ __('admin.settings') }} →</a>
    </div>
</x-app-layout>
