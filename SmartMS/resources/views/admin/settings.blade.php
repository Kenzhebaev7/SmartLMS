<x-app-layout>
    <x-slot name="header">{{ __('admin.settings_title') }}</x-slot>

    <div class="max-w-2xl space-y-6">
        <section class="p-6 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
            <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-2">{{ __('admin.levels') }}</h2>
            <p class="text-slate-600 dark:text-slate-400 text-sm mb-2">{{ __('admin.levels_desc') }}</p>
            <ul class="list-disc list-inside text-slate-700 dark:text-slate-300">
                @foreach($levels ?? [] as $level)
                    <li>{{ $level }}</li>
                @endforeach
            </ul>
        </section>

        <section class="p-6 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
            <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-2">{{ __('admin.coming_soon') }}</h2>
            <p class="text-slate-600 dark:text-slate-400 text-sm">{{ __('admin.coming_soon_desc') }}</p>
        </section>
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.dashboard') }}" class="text-primary font-semibold hover:underline">← {{ __('admin.back_admin') }}</a>
    </div>
</x-app-layout>
