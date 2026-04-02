<x-app-layout>
    <x-slot name="header">{{ __('messages.search_title') }}</x-slot>

    <div class="max-w-4xl space-y-6">
        <form action="{{ route('search.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 items-stretch">
            <input
                type="text"
                name="q"
                value="{{ $query }}"
                placeholder="{{ __('messages.search_placeholder') }}"
                class="flex-1 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 px-4 py-2 text-sm"
            >
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary text-white font-semibold hover:bg-primary-light transition-colors">
                {{ __('messages.search_button') }}
            </button>
        </form>

        @if($query === '')
            <p class="text-slate-500 dark:text-slate-400 text-sm">
                {{ __('messages.search_hint') }}
            </p>
        @else
            <p class="text-slate-600 dark:text-slate-400 text-sm">
                {{ __('messages.search_results_for', ['query' => $query]) }} ({{ $results->count() }})
            </p>

            @if($results->isEmpty())
                <p class="text-slate-500 dark:text-slate-400 text-sm">
                    {{ __('messages.search_no_results') }}
                </p>
            @else
                <div class="space-y-3">
                    @foreach($results as $lesson)
                        <a href="{{ route('lessons.show', [$lesson->section, $lesson]) }}"
                           class="block p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-primary hover:bg-primary-pale dark:hover:bg-slate-700 transition-colors">
                            <div class="text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500 mb-1">
                                {{ $lesson->section?->getTitleForLocale(app()->getLocale()) }}
                            </div>
                            <h3 class="font-semibold text-slate-900 dark:text-slate-100">
                                {{ $lesson->getTitleForLocale(app()->getLocale()) }}
                            </h3>
                        </a>
                    @endforeach
                </div>
            @endif
        @endif
    </div>
</x-app-layout>

