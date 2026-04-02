<x-app-layout>
    <x-slot name="header">{{ __('messages.exam_trainer_title') }}</x-slot>

    @if(session('status'))
        <div class="mb-6 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 px-4 py-3 text-emerald-800 dark:text-emerald-200">{{ session('status') }}</div>
    @endif

    <p class="text-slate-600 dark:text-slate-400 mb-6">{{ __('messages.exam_trainer_intro', ['grade' => $grade]) }}</p>

    <div class="grid gap-4 md:grid-cols-2">
        @forelse($quizzes ?? [] as $quiz)
            <a href="{{ route('exam-trainer.show', $quiz) }}" class="block p-6 rounded-2xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 hover:border-sky-300 dark:hover:border-sky-600 hover:shadow-lg transition-all">
                <h3 class="font-bold text-slate-800 dark:text-slate-100">{{ $quiz->getTitleForLocale(app()->getLocale()) }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $quiz->questions_count }} {{ __('messages.exam_trainer_questions_count') }} · {{ __('messages.exam_trainer_min_pass') }} {{ $quiz->passing_percent }}%</p>
                <span class="mt-3 inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold bg-sky-500 text-white hover:bg-sky-600 transition-colors">{{ __('messages.exam_trainer_start_quiz') }}</span>
            </a>
        @empty
            <p class="text-slate-500 dark:text-slate-400 col-span-2">{{ __('messages.exam_trainer_no_quizzes') }}</p>
        @endforelse
    </div>
</x-app-layout>
