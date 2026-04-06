<x-app-layout>
    <x-slot name="header">{{ __('messages.quiz_title', ['section' => $section->getTitleForLocale(app()->getLocale())]) }}</x-slot>

    @php
        $student = auth()->user();
        $levelKey = $student?->placementLevelKey();
        $sectionTrackLabel = $section->is_revision ? __('messages.dashboard_level_beginner') : __('messages.dashboard_level_advanced');
        $sectionGradeLabel = $section->grade ? __('messages.auth_grade_' . $section->grade) : __('messages.teacher_grade_all');
    @endphp

    @if(session('failed'))
        <div class="mb-6 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 px-4 py-3 text-amber-800 dark:text-amber-200">
            {{ session('message') }}
            <p class="mt-2 font-semibold">{{ __('messages.quiz_try_again') }}</p>
        </div>
    @endif

    <div class="mb-6 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/90 dark:bg-slate-800/70 px-5 py-4">
        <div class="flex flex-wrap items-center gap-2 mb-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $sectionGradeLabel }}</span>
            <span class="inline-flex items-center px-3 py-1 rounded-full {{ $section->is_revision ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200 border border-amber-200 dark:border-amber-700' : 'bg-violet-100 dark:bg-violet-900/30 text-violet-800 dark:text-violet-200 border border-violet-200 dark:border-violet-700' }} text-sm font-semibold">{{ $sectionTrackLabel }}</span>
        </div>
        <p class="text-sm text-slate-600 dark:text-slate-300">{{ __('messages.quiz_progression_hint', ['section' => $section->getTitleForLocale(app()->getLocale()), 'level' => $levelKey ? __('messages.dashboard_level_' . $levelKey) : $sectionTrackLabel]) }}</p>
    </div>

    @if(($timeLimitSeconds ?? null) || ($deadlineAt ?? null))
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            @if($timeLimitSeconds ?? null)
                @php
                    $startSeconds = $remainingSeconds ?? $timeLimitSeconds;
                    $startSeconds = max(0, (int) $startSeconds);
                @endphp
                <div
                    x-data="{
                        total: {{ $timeLimitSeconds }},
                        remaining: {{ $startSeconds }},
                        init() {
                            if (!this.total || !this.remaining) return;
                            const tick = () => {
                                if (this.remaining > 0) {
                                    this.remaining--;
                                    setTimeout(tick, 1000);
                                } else {
                                    const form = this.$refs.quizForm;
                                    if (form) form.submit();
                                }
                            };
                            setTimeout(tick, 1000);
                        },
                        percent() {
                            if (!this.total) return 0;
                            return Math.round((this.remaining / this.total) * 100);
                        },
                        label() {
                            const m = Math.floor(this.remaining / 60);
                            const s = this.remaining % 60;
                            return m + ':' + String(s).padStart(2, '0');
                        }
                    }"
                    class="flex-1 p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-sky-50 dark:bg-sky-900/20"
                >
                    <p class="text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">
                        {{ __('messages.quiz_time_limit_label') }}
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="flex-1 h-2 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                            <div class="h-2 rounded-full bg-emerald-500 transition-all duration-500"
                                 :style="{ width: percent() + '%' }"></div>
                        </div>
                        <span class="text-sm font-mono font-semibold text-slate-800 dark:text-slate-100" x-text="label()"></span>
                    </div>
                </div>
            @endif
            @if($deadlineAt ?? null)
                <div class="px-4 py-3 rounded-xl border border-rose-200 dark:border-rose-700 bg-rose-50 dark:bg-rose-900/30">
                    <p class="text-xs font-semibold text-rose-700 dark:text-rose-200 mb-1">{{ __('messages.quiz_deadline_label') }}</p>
                    <p class="text-sm text-rose-800 dark:text-rose-100">
                        {{ $deadlineAt->format('d.m.Y H:i') }}
                    </p>
                </div>
            @endif
        </div>
    @endif

    <div class="mb-6 p-4 rounded-xl bg-sky-50 dark:bg-sky-900/20 border border-sky-200 dark:border-sky-700">
        <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('messages.quiz_choose_language') }}</p>
        <div class="flex gap-2">
            <a href="{{ route('quiz.show', $section) }}?quiz_locale=ru" class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors {{ ($quizLocale ?? 'ru') === 'ru' ? 'bg-primary text-white' : 'bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-600' }}">Русский</a>
            <a href="{{ route('quiz.show', $section) }}?quiz_locale=kk" class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors {{ ($quizLocale ?? 'ru') === 'kk' ? 'bg-primary text-white' : 'bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-600' }}">Қазақша</a>
        </div>
    </div>

    <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100 mb-2">{{ $quiz->getTitleForLocale($quizLocale ?? 'ru') }}</h2>
    <p class="text-slate-600 dark:text-slate-400 mb-8">{{ __('messages.quiz_min_pass', ['percent' => $quiz->passing_percent]) }}</p>

    <form method="POST" action="{{ route('quiz.submit', $section) }}" class="space-y-8" x-ref="quizForm">
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
                {{ __('messages.quiz_submit') }}
            </button>
            <a href="{{ route('sections.show', $section) }}" class="px-6 py-3 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">{{ __('messages.quiz_cancel') }}</a>
        </div>
    </form>
</x-app-layout>
