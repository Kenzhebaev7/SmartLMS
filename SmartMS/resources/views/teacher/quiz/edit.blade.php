<x-app-layout>
    <x-slot name="header">{{ __('teacher.quiz_section_header', ['title' => $section->title]) }}</x-slot>

    <form action="{{ route('teacher.sections.quiz.update', $section) }}" method="POST" class="max-w-2xl space-y-6">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('teacher.quiz_title_label') }} (RU)</label>
            <input type="text" name="title" value="{{ old('title', $quiz->title) }}" required class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-4 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('teacher.quiz_title_kk') }}</label>
            <input type="text" name="title_kk" value="{{ old('title_kk', $quiz->title_kk) }}" placeholder="Квиз атауы (қазақша)" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-4 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('teacher.min_percent_label') }}</label>
            <input type="number" name="passing_percent" value="{{ old('passing_percent', $quiz->passing_percent) }}" min="1" max="100" class="w-full rounded-lg border border-gray-300 px-4 py-2">
        </div>

        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">{{ __('teacher.questions_label') }}</h3>
        <div id="questions">
            @forelse($quiz->questions as $i => $q)
                <div class="p-4 rounded-xl border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-800 mb-4 question-block">
                    <input type="hidden" name="questions[{{ $i }}][id]" value="{{ $q->id }}">
                    <label class="block text-sm text-slate-600 dark:text-slate-400 mb-1">{{ __('teacher.question_text_placeholder') }} (RU)</label>
                    <input type="text" name="questions[{{ $i }}][text]" value="{{ old("questions.{$i}.text", $q->text) }}" required class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-4 py-2 mb-2">
                    <label class="block text-sm text-slate-600 dark:text-slate-400 mb-1">{{ __('teacher.question_text_kk') }}</label>
                    <input type="text" name="questions[{{ $i }}][text_kk]" value="{{ old("questions.{$i}.text_kk", $q->text_kk) }}" placeholder="Сұрақ (қазақша)" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-4 py-2 mb-2">
                    <input type="hidden" name="questions[{{ $i }}][type]" value="single">
                    <label class="block text-sm text-slate-600 dark:text-slate-400 mt-2">{{ __('teacher.options_label') }} (RU)</label>
                    <textarea name="questions[{{ $i }}][options]" rows="2" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-4 py-2" placeholder='{"A":"Да","B":"Нет"}'>{{ json_encode($q->options ?? ['A' => 'Да', 'B' => 'Нет']) }}</textarea>
                    <label class="block text-sm text-slate-600 dark:text-slate-400 mt-1">{{ __('teacher.options_kk') }}</label>
                    <textarea name="questions[{{ $i }}][options_kk]" rows="2" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-4 py-2" placeholder='{"A":"Иә","B":"Жоқ"}'>{{ $q->options_kk ? json_encode($q->options_kk) : '' }}</textarea>
                    <input type="text" name="questions[{{ $i }}][correct_answer]" value="{{ is_array($q->correct_answer) ? implode(',', $q->correct_answer) : $q->correct_answer }}" placeholder="{{ __('teacher.correct_answer_placeholder') }}" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-4 py-2 mt-2">
                </div>
            @empty
                <div class="p-4 rounded-xl border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-800 mb-4 question-block">
                    <input type="hidden" name="questions[0][id]" value="">
                    <label class="block text-sm text-slate-600 dark:text-slate-400 mb-1">{{ __('teacher.question_text_placeholder') }} (RU)</label>
                    <input type="text" name="questions[0][text]" value="{{ old('questions.0.text') }}" required class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-4 py-2 mb-2">
                    <label class="block text-sm text-slate-600 dark:text-slate-400 mb-1">{{ __('teacher.question_text_kk') }}</label>
                    <input type="text" name="questions[0][text_kk]" value="{{ old('questions.0.text_kk') }}" placeholder="Сұрақ (қазақша)" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-4 py-2 mb-2">
                    <input type="hidden" name="questions[0][type]" value="single">
                    <label class="block text-sm text-slate-600 dark:text-slate-400 mt-2">{{ __('teacher.options_label') }} (RU)</label>
                    <textarea name="questions[0][options]" rows="2" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-4 py-2">{"A":"Да","B":"Нет"}</textarea>
                    <label class="block text-sm text-slate-600 dark:text-slate-400 mt-1">{{ __('teacher.options_kk') }}</label>
                    <textarea name="questions[0][options_kk]" rows="2" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-4 py-2" placeholder='{"A":"Иә","B":"Жоқ"}'></textarea>
                    <input type="text" name="questions[0][correct_answer]" value="{{ old('questions.0.correct_answer', 'A') }}" placeholder="{{ __('teacher.correct_answer_placeholder') }}" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-4 py-2 mt-2">
                </div>
            @endforelse
        </div>
        <button type="button" id="add-question-btn" class="mb-4 px-4 py-2 rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
            {{ __('teacher.add_question') }}
        </button>

        <div class="flex gap-3">
            <button type="submit" class="px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary-light transition-colors">{{ __('teacher.save_quiz') }}</button>
            <a href="{{ route('teacher.sections.show', $section) }}" class="px-6 py-3 border border-gray-300 dark:border-slate-600 rounded-xl text-gray-700 dark:text-slate-300">{{ __('teacher.cancel') }}</a>
        </div>
    </form>

    <script>
        (function() {
            var container = document.getElementById('questions');
            var btn = document.getElementById('add-question-btn');
            if (!container || !btn) return;
            var optionsJson = '{"A":"Да","B":"Нет"}';
            var optionsLabel = '{{ __("teacher.options_label") }}';
            var textPlaceholder = '{{ __("teacher.question_text_placeholder") }}';
            var correctPlaceholder = '{{ __("teacher.correct_answer_placeholder") }}';
            btn.addEventListener('click', function() {
                var n = container.querySelectorAll('.question-block').length;
                var div = document.createElement('div');
                div.className = 'p-4 rounded-xl border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-800 mb-4 question-block';
                div.innerHTML =
                    '<label class="block text-sm text-slate-600 dark:text-slate-400 mb-1">' + textPlaceholder + ' (RU)</label>' +
                    '<input type="hidden" name="questions[' + n + '][id]" value="">' +
                    '<input type="text" name="questions[' + n + '][text]" required class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-4 py-2 mb-2">' +
                    '<label class="block text-sm text-slate-600 dark:text-slate-400 mb-1">Сұрақ (ҚК)</label>' +
                    '<input type="text" name="questions[' + n + '][text_kk]" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-4 py-2 mb-2">' +
                    '<input type="hidden" name="questions[' + n + '][type]" value="single">' +
                    '<label class="block text-sm text-slate-600 dark:text-slate-400 mt-2">' + optionsLabel + ' (RU)</label>' +
                    '<textarea name="questions[' + n + '][options]" rows="2" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-4 py-2">' + optionsJson + '</textarea>' +
                    '<label class="block text-sm text-slate-600 dark:text-slate-400 mt-1">Нұсқалар (ҚК)</label>' +
                    '<textarea name="questions[' + n + '][options_kk]" rows="2" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-4 py-2" placeholder=\'{"A":"Иә","B":"Жоқ"}\'></textarea>' +
                    '<input type="text" name="questions[' + n + '][correct_answer]" value="A" placeholder="' + correctPlaceholder + '" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-4 py-2 mt-2">';
                container.appendChild(div);
            });
        })();
    </script>
</x-app-layout>
