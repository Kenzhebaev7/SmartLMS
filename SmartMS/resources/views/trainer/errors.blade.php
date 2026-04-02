<x-app-layout>
    <x-slot name="header">{{ __('messages.trainer_errors_title') }}</x-slot>

    <div class="max-w-4xl space-y-6">
        <p class="text-sm text-slate-600 dark:text-slate-400">
            {{ __('messages.trainer_errors_intro') }}
        </p>

        @if($hardQuestions->isEmpty())
            <p class="text-slate-500 dark:text-slate-400">
                {{ __('messages.trainer_errors_no_data') }}
            </p>
        @else
            <div class="space-y-3">
                @foreach($hardQuestions as $row)
                    @php
                        $question = $row->question;
                        $quiz = $question?->quiz;
                        $section = $quiz?->section;
                    @endphp
                    @if($question && $quiz && $section)
                        <a href="{{ route('quiz.show', $section) }}"
                           class="block p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-primary hover:bg-primary-pale dark:hover:bg-slate-700 transition-colors">
                            <div class="flex justify-between items-start gap-3">
                                <div>
                                    <div class="text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500 mb-1">
                                        {{ $section->getTitleForLocale(app()->getLocale()) }} · {{ $quiz->getTitleForLocale(app()->getLocale()) }}
                                    </div>
                                    <p class="text-sm text-slate-800 dark:text-slate-100">
                                        {{ $question->getTextForLocale(app()->getLocale()) }}
                                    </p>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 dark:bg-rose-900/40 dark:text-rose-200">
                                    {{ $row->wrong_count }} / {{ $row->total_count }}
                                </span>
                            </div>
                        </a>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>

