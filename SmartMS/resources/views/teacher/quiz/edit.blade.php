<x-app-layout>
    <x-slot name="header">{{ __('messages.teacher_quiz_section_header', ['title' => $section->getTitleForLocale(app()->getLocale())]) }}</x-slot>

    @php
        $questionCount = $quiz->questions->count();
    @endphp

    <form action="{{ route('teacher.sections.quiz.update', $section) }}" method="POST" class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <section class="rounded-[30px] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="rounded-2xl border border-sky-200 bg-sky-50 px-4 py-4 text-sm text-sky-900">
                    {{ __('messages.teacher_quiz_context_hint', ['grade' => $section->grade ? __('messages.auth_grade_' . $section->grade) : __('messages.teacher_grade_all'), 'level' => $section->is_revision ? __('messages.dashboard_level_beginner') : __('messages.dashboard_level_advanced')]) }}
                </div>

                <div class="mt-6 grid gap-5 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_quiz_title_label') }} (RU)</label>
                        <input type="text" name="title" value="{{ old('title', $quiz->title) }}" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-800">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_quiz_title_kk') }}</label>
                        <input type="text" name="title_kk" value="{{ old('title_kk', $quiz->title_kk) }}" placeholder="{{ __('messages.teacher_placeholder_quiz_title_kk') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-800">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_min_percent_label') }}</label>
                        <input type="number" name="passing_percent" value="{{ old('passing_percent', $quiz->passing_percent) }}" min="1" max="100" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-800">
                    </div>
                </div>
            </section>

            <section class="rounded-[30px] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.teacher_questions_label') }}</p>
                        <h3 class="mt-2 text-xl font-bold text-slate-900">{{ __('messages.teacher_quiz_builder_title') }}</h3>
                        <p class="mt-1 text-sm text-slate-500">{{ __('messages.teacher_quiz_builder_desc') }}</p>
                    </div>
                    <button type="button" id="add-question-btn" class="inline-flex items-center justify-center rounded-2xl border-2 border-dashed border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        {{ __('messages.teacher_add_question') }}
                    </button>
                </div>

                <div id="questions" class="mt-6 space-y-5">
                    @forelse($quiz->questions as $i => $q)
                        <div class="question-block rounded-[28px] border border-slate-200 bg-slate-50/60 p-5">
                            <div class="mb-4 flex items-center justify-between gap-3">
                                <h4 class="text-lg font-bold text-slate-900">{{ __('messages.teacher_question_card_title', ['number' => $i + 1]) }}</h4>
                                <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-500 border border-slate-200">{{ __('messages.teacher_single_choice') }}</span>
                            </div>

                            <input type="hidden" name="questions[{{ $i }}][id]" value="{{ $q->id }}">
                            <input type="hidden" name="questions[{{ $i }}][type]" value="single">

                            <div class="grid gap-4">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_question_text_placeholder') }} (RU)</label>
                                    <input type="text" name="questions[{{ $i }}][text]" value="{{ old("questions.{$i}.text", $q->text) }}" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-800">
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_question_text_kk') }}</label>
                                    <input type="text" name="questions[{{ $i }}][text_kk]" value="{{ old("questions.{$i}.text_kk", $q->text_kk) }}" placeholder="{{ __('messages.teacher_placeholder_question_kk') }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-800">
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_options_label') }} (RU)</label>
                                    <textarea name="questions[{{ $i }}][options]" rows="3" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-800" placeholder='{"A":"Yes","B":"No"}'>{{ json_encode($q->options ?? ['A' => 'Yes', 'B' => 'No'], JSON_UNESCAPED_UNICODE) }}</textarea>
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_options_kk') }}</label>
                                    <textarea name="questions[{{ $i }}][options_kk]" rows="3" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-800" placeholder='{"A":"Ia","B":"Joq"}'>{{ $q->options_kk ? json_encode($q->options_kk, JSON_UNESCAPED_UNICODE) : '' }}</textarea>
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_correct_answer_placeholder') }}</label>
                                    <input type="text" name="questions[{{ $i }}][correct_answer]" value="{{ is_array($q->correct_answer) ? implode(',', $q->correct_answer) : $q->correct_answer }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-800" placeholder="A">
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="question-block rounded-[28px] border border-slate-200 bg-slate-50/60 p-5">
                            <div class="mb-4 flex items-center justify-between gap-3">
                                <h4 class="text-lg font-bold text-slate-900">{{ __('messages.teacher_question_card_title', ['number' => 1]) }}</h4>
                                <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-500 border border-slate-200">{{ __('messages.teacher_single_choice') }}</span>
                            </div>

                            <input type="hidden" name="questions[0][id]" value="">
                            <input type="hidden" name="questions[0][type]" value="single">

                            <div class="grid gap-4">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_question_text_placeholder') }} (RU)</label>
                                    <input type="text" name="questions[0][text]" value="{{ old('questions.0.text') }}" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-800">
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_question_text_kk') }}</label>
                                    <input type="text" name="questions[0][text_kk]" value="{{ old('questions.0.text_kk') }}" placeholder="{{ __('messages.teacher_placeholder_question_kk') }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-800">
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_options_label') }} (RU)</label>
                                    <textarea name="questions[0][options]" rows="3" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-800">{"A":"Yes","B":"No"}</textarea>
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_options_kk') }}</label>
                                    <textarea name="questions[0][options_kk]" rows="3" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-800" placeholder='{"A":"Ia","B":"Joq"}'></textarea>
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_correct_answer_placeholder') }}</label>
                                    <input type="text" name="questions[0][correct_answer]" value="{{ old('questions.0.correct_answer', 'A') }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-800" placeholder="A">
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="mt-6 flex flex-wrap gap-3">
                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                        {{ __('messages.teacher_save_quiz') }}
                    </button>
                    <a href="{{ route('teacher.sections.show', $section) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        {{ __('messages.teacher_cancel') }}
                    </a>
                </div>
            </section>
        </div>

        <aside class="space-y-6">
            <section class="rounded-[30px] border border-slate-200 bg-gradient-to-br from-white to-sky-50/50 p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.teacher_section_quiz') }}</p>
                <h2 class="mt-2 text-2xl font-bold text-slate-900">{{ __('messages.teacher_quiz_summary_title') }}</h2>
                <p class="mt-2 text-sm leading-7 text-slate-600">{{ __('messages.teacher_quiz_summary_desc') }}</p>

                <div class="mt-6 grid gap-4">
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">{{ __('messages.teacher_questions_label') }}</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">{{ $questionCount }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">{{ __('messages.teacher_min_percent_label') }}</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">{{ old('passing_percent', $quiz->passing_percent) }}%</p>
                    </div>
                    <div class="rounded-2xl border {{ $questionCount < 3 ? 'border-amber-200 bg-amber-50' : 'border-emerald-200 bg-emerald-50' }} p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] {{ $questionCount < 3 ? 'text-amber-700' : 'text-emerald-700' }}">{{ __('messages.teacher_quiz_health_title') }}</p>
                        <p class="mt-2 text-sm font-semibold text-slate-900">
                            {{ $questionCount < 3 ? __('messages.teacher_sections_issue_few_questions') : __('messages.teacher_quiz_health_ready') }}
                        </p>
                    </div>
                </div>
            </section>
        </aside>
    </form>

    <script>
        (function () {
            var container = document.getElementById('questions');
            var btn = document.getElementById('add-question-btn');
            if (!container || !btn) return;

            var textPlaceholder = @json(__('messages.teacher_question_text_placeholder'));
            var textKkLabel = @json(__('messages.teacher_question_text_kk'));
            var optionsLabel = @json(__('messages.teacher_options_label'));
            var optionsKkLabel = @json(__('messages.teacher_options_kk'));
            var correctPlaceholder = @json(__('messages.teacher_correct_answer_placeholder'));
            var questionTitle = @json(__('messages.teacher_question_card_title', ['number' => '__number__']));
            var singleChoice = @json(__('messages.teacher_single_choice'));

            btn.addEventListener('click', function () {
                var n = container.querySelectorAll('.question-block').length;
                var div = document.createElement('div');
                div.className = 'question-block rounded-[28px] border border-slate-200 bg-slate-50/60 p-5';
                div.innerHTML =
                    '<div class="mb-4 flex items-center justify-between gap-3">' +
                        '<h4 class="text-lg font-bold text-slate-900">' + questionTitle.replace('__number__', String(n + 1)) + '</h4>' +
                        '<span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-500 border border-slate-200">' + singleChoice + '</span>' +
                    '</div>' +
                    '<input type="hidden" name="questions[' + n + '][id]" value="">' +
                    '<input type="hidden" name="questions[' + n + '][type]" value="single">' +
                    '<div class="grid gap-4">' +
                        '<div><label class="mb-1 block text-sm font-medium text-slate-700">' + textPlaceholder + ' (RU)</label><input type="text" name="questions[' + n + '][text]" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-800"></div>' +
                        '<div><label class="mb-1 block text-sm font-medium text-slate-700">' + textKkLabel + '</label><input type="text" name="questions[' + n + '][text_kk]" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-800"></div>' +
                        '<div><label class="mb-1 block text-sm font-medium text-slate-700">' + optionsLabel + ' (RU)</label><textarea name="questions[' + n + '][options]" rows="3" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-800">{\"A\":\"Р”Р°\",\"B\":\"РќРµС‚\"}</textarea></div>' +
                        '<div><label class="mb-1 block text-sm font-medium text-slate-700">' + optionsKkLabel + '</label><textarea name="questions[' + n + '][options_kk]" rows="3" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-800">{\"A\":\"РУ™\",\"B\":\"Р–РѕТ›\"}</textarea></div>' +
                        '<div><label class="mb-1 block text-sm font-medium text-slate-700">' + correctPlaceholder + '</label><input type="text" name="questions[' + n + '][correct_answer]" value="A" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-800" placeholder="A"></div>' +
                    '</div>';
                container.appendChild(div);
            });
        })();
    </script>
</x-app-layout>

