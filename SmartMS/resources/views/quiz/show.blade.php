<x-app-layout>
    <x-slot name="header">{{ __('messages.quiz_title', ['section' => $section->getTitleForLocale(app()->getLocale())]) }}</x-slot>

    @php
        $student = auth()->user();
        $levelKey = $student?->placementLevelKey();
        $sectionTrackLabel = $section->is_revision ? __('messages.dashboard_level_beginner') : __('messages.dashboard_level_advanced');
        $sectionGradeLabel = $section->grade ? __('messages.auth_grade_' . $section->grade) : __('messages.teacher_grade_all');
        $questionsCollection = collect($quiz->questions ?? []);
        $questionTotal = $questionsCollection->count();
    @endphp

    @if(session('failed'))
        <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-4 text-amber-800 shadow-sm">
            <p>{{ session('message') }}</p>
            <p class="mt-2 font-semibold">{{ __('messages.quiz_try_again') }}</p>
        </div>
    @endif

    <div class="space-y-8" x-data="{ currentQuestion: 1, totalQuestions: {{ $questionTotal }} }">
        <section class="rounded-[30px] border border-slate-200 bg-gradient-to-br from-white via-sky-50/40 to-amber-50/30 p-6 shadow-sm">
            <div class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600">{{ $sectionGradeLabel }}</span>
                        <span class="inline-flex items-center rounded-full {{ $section->is_revision ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-sky-100 text-sky-800 border border-sky-200' }} px-3 py-1 text-xs font-semibold">{{ $sectionTrackLabel }}</span>
                    </div>
                    <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-900">{{ $quiz->getTitleForLocale($quizLocale ?? 'ru') }}</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-600">{{ __('messages.quiz_progression_hint', ['section' => $section->getTitleForLocale(app()->getLocale()), 'level' => $levelKey ? __('messages.dashboard_level_' . $levelKey) : $sectionTrackLabel]) }}</p>

                    <div class="mt-6 h-2 overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-gradient-to-r from-sky-500 to-emerald-500 transition-all duration-300" :style="{ width: ((currentQuestion / Math.max(totalQuestions, 1)) * 100) + '%' }"></div>
                    </div>
                    <p class="mt-3 text-sm text-slate-500">
                        {{ __('messages.quiz_min_pass', ['percent' => $quiz->passing_percent]) }}
                    </p>
                </div>

                <div class="grid gap-4">
                    <div class="rounded-2xl border border-white bg-white/90 p-4 shadow-sm text-fit">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.teacher_questions_label') }}</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">{{ $questionTotal }}</p>
                    </div>
                    @if($latestResult)
                        <div class="rounded-2xl border {{ $latestResult->passed ? 'border-emerald-200 bg-emerald-50' : 'border-amber-200 bg-amber-50' }} p-4 shadow-sm text-fit">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] {{ $latestResult->passed ? 'text-emerald-700' : 'text-amber-700' }}">{{ __('messages.quiz_last_attempt_title') }}</p>
                            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $latestResult->score }}%</p>
                            <p class="mt-1 text-sm {{ $latestResult->passed ? 'text-emerald-800' : 'text-amber-800' }}">
                                {{ $latestResult->passed ? __('messages.quiz_last_attempt_passed') : __('messages.quiz_last_attempt_retry') }}
                            </p>
                        </div>
                    @endif
                    @if(($timeLimitSeconds ?? null) || ($deadlineAt ?? null))
                    <div class="rounded-2xl border border-slate-200 bg-white/90 p-4 shadow-sm text-fit">
                            @if($timeLimitSeconds ?? null)
                                @php
                                    $startSeconds = max(0, (int) ($remainingSeconds ?? $timeLimitSeconds));
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
                                                    this.$refs.quizForm?.submit();
                                                }
                                            };
                                            setTimeout(tick, 1000);
                                        },
                                        label() {
                                            const m = Math.floor(this.remaining / 60);
                                            const s = this.remaining % 60;
                                            return m + ':' + String(s).padStart(2, '0');
                                        }
                                    }"
                                >
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.quiz_time_limit_label') }}</p>
                                    <p class="mt-2 text-2xl font-bold text-slate-900" x-text="label()"></p>
                                </div>
                            @endif
                            @if($deadlineAt ?? null)
                                <p class="mt-3 text-sm text-slate-500">{{ __('messages.quiz_deadline_label') }}: {{ $deadlineAt->format('d.m.Y H:i') }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <section class="rounded-[30px] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.quiz_choose_language') }}</p>
                    <div class="mt-3 flex gap-2">
                        <a href="{{ route('quiz.show', $section) }}?quiz_locale=ru" class="rounded-2xl px-4 py-3 text-sm font-semibold transition-colors {{ ($quizLocale ?? 'ru') === 'ru' ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }}">Русский</a>
                        <a href="{{ route('quiz.show', $section) }}?quiz_locale=kk" class="rounded-2xl px-4 py-3 text-sm font-semibold transition-colors {{ ($quizLocale ?? 'ru') === 'kk' ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }}">Қазақша</a>
                    </div>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                    {{ __('messages.quiz_retake_hint') }}
                </div>
            </div>
        </section>

        <form method="POST" action="{{ route('quiz.submit', $section) }}" class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]" x-ref="quizForm">
            @csrf

            <div class="space-y-5">
                @foreach($questionsCollection as $index => $q)
                    @php
                        $questionText = $q->getTextForLocale($quizLocale ?? 'ru');
                        $questionOptions = $q->getOptionsForLocale($quizLocale ?? 'ru');
                    @endphp
                    <div class="rounded-[30px] border border-slate-200 bg-white p-6 shadow-sm" @mouseenter="currentQuestion = {{ $index + 1 }}">
                        <div class="flex items-center justify-between gap-3">
                            <h3 class="text-xl font-bold text-slate-900">{{ $index + 1 }}. {{ $questionText }}</h3>
                            <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">
                                {{ __('messages.quiz_question_badge', ['current' => $index + 1, 'total' => $questionTotal]) }}
                            </span>
                        </div>

                        <div class="mt-5 space-y-3">
                            @foreach($questionOptions as $key => $label)
                                <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50/60 px-4 py-4 transition hover:border-sky-300 hover:bg-sky-50/50">
                                    <input type="radio" name="q_{{ $q->id }}" value="{{ $key }}" class="mt-1 text-primary border-slate-300 focus:ring-primary">
                                    <span class="text-sm leading-7 text-slate-700">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                        {{ __('messages.quiz_submit') }}
                    </button>
                    <a href="{{ route('sections.show', $section) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        {{ __('messages.quiz_cancel') }}
                    </a>
                </div>
            </div>

            <aside class="space-y-6">
                <div class="rounded-[30px] border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.quiz_review_title') }}</p>
                    <h3 class="mt-2 text-xl font-bold text-slate-900">{{ __('messages.quiz_review_focus') }}</h3>
                    <p class="mt-2 text-sm leading-7 text-slate-600">{{ __('messages.quiz_review_desc') }}</p>
                </div>
            </aside>
        </form>
    </div>
</x-app-layout>
