<x-app-layout>
    <x-slot name="header">{{ $section->getTitleForLocale(app()->getLocale()) }} — SmartLMS</x-slot>

    @if(session('status'))
        <div class="mb-6 rounded-xl bg-primary-50 border border-primary-200 px-4 py-3 text-primary-light">{{ session('status') }}</div>
    @endif

    @if($isMaster)
        <div class="mb-6 rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-amber-800 font-medium">
            {{ __('sections.master_badge') }}
        </div>
    @endif

    <p class="text-gray-600 mb-8">{{ $section->description }}</p>

    <h3 class="text-lg font-bold text-slate-800 mb-4">{{ __('sections.lessons') }}</h3>
    <div class="space-y-3">
        @foreach($section->lessons ?? [] as $lesson)
            <a href="{{ route('lessons.show', [$section, $lesson]) }}" class="block p-4 rounded-xl border border-slate-200 bg-white hover:border-primary hover:bg-primary-pale transition-colors">
                <span class="font-semibold text-slate-800 dark:text-slate-100">{{ $lesson->getTitleForLocale(app()->getLocale()) }}</span>
            </a>
        @endforeach
    </div>

    @if($section->quiz)
        <div class="mt-8 pt-8 border-t border-slate-200 dark:border-slate-600">
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">{{ __('sections.quiz_unlock_hint') }}</p>
            <a href="{{ route('quiz.show', $section) }}" class="inline-flex items-center px-6 py-3 bg-accent text-white rounded-xl font-semibold hover:bg-accent-dark transition-colors">
                {{ __('sections.to_quiz') }}
            </a>
        </div>
    @endif
</x-app-layout>
