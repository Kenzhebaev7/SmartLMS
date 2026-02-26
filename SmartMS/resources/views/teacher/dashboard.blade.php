<x-app-layout>
    <x-slot name="header">{{ __('teacher.dashboard_title') }}</x-slot>

    <div class="grid gap-6 md:grid-cols-2">
        <a href="{{ route('teacher.sections.index') }}" class="block p-6 rounded-2xl border-2 border-primary bg-white hover:bg-primary-pale transition-colors">
            <h3 class="font-bold text-slate-800">{{ __('teacher.sections_lessons') }}</h3>
            <p class="text-sm text-gray-500 mt-1">{{ __('teacher.sections_lessons_desc') }}</p>
        </a>
        <a href="{{ route('teacher.progress.index') }}" class="block p-6 rounded-2xl border-2 border-accent bg-white hover:bg-accent-pale transition-colors">
            <h3 class="font-bold text-slate-800">{{ __('teacher.progress') }}</h3>
            <p class="text-sm text-gray-500 mt-1">{{ __('teacher.progress_desc') }}</p>
        </a>
    </div>
</x-app-layout>
