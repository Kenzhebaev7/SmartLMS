<x-app-layout>
    <x-slot name="header">{{ __('quiz.title', ['section' => $section->getTitleForLocale(app()->getLocale())]) }}</x-slot>

    @if(session('failed'))
        <div class="mb-6 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 px-4 py-3 text-amber-800 dark:text-amber-200">
            {{ session('message') }}
            <p class="mt-2 font-semibold">{{ __('quiz.try_again') }}</p>
        </div>
    @endif

    <div class="mb-6 p-4 rounded-xl bg-sky-50 dark:bg-sky-900/20 border border-sky-200 dark:border-sky-700">
        <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('quiz.choose_language') }}</p>
        <div class="flex gap-2">
            <a href="{{ route('quiz.show', $section) }}?quiz_locale=ru" class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors {{ ($quizLocale ?? 'ru') === 'ru' ? 'bg-primary text-white' : 'bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-600' }}">Русский</a>
            <a href="{{ route('quiz.show', $section) }}?quiz_locale=kk" class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors {{ ($quizLocale ?? 'ru') === 'kk' ? 'bg-primary text-white' : 'bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-600' }}">Қазақша</a>
        </div>
    </div>

    <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100 mb-2">{{ $quiz->getTitleForLocale($quizLocale ?? 'ru') }}</h2>
    <p class="text-slate-600 dark:text-slate-400 mb-8">{{ __('quiz.min_pass', ['percent' => $quiz->passing_percent]) }}</p>

    <form method="POST" action="{{ route('quiz.submit', $section) }}" class="space-y-8">
        @csrf
        @foreach($quiz->questions ?? [] as $index => $q)
            @php
                $questionText = $q->getTextForLocale($quizLocale ?? 'ru');
                $questionOptions = $q->getOptionsForLocale($quizLocale ?? 'ru');
            @endphp
            <div class="p-6 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800">
                <h3 class="font-bold text-slate-800 dark:text-slate-100 mb-4">{{ $index + 1 }}. {{ $questionText }}</h3>
                <div class="space-y-2">
                    @foreach($questionOptions as $key => $label)
                        <label class="flex items-center gap-2 cursor-pointer text-slate-700 dark:text-slate-300">
                            <input type="radio" name="q_{{ $q->id }}" value="{{ $key }}" class="text-primary border-slate-300 focus:ring-primary dark:bg-slate-700">
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach
        <div class="flex gap-4">
            <button type="submit" class="px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary-light transition-colors">
                {{ __('quiz.submit') }}
            </button>
            <a href="{{ route('sections.show', $section) }}" class="px-6 py-3 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">{{ __('quiz.cancel') }}</a>
        </div>
    </form>
</x-app-layout>
