<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function edit(Section $section): View
    {
        $quiz = $section->quiz ?? new Quiz([
            'section_id' => $section->id,
            'title' => 'Квиз: '.$section->title,
            'passing_percent' => 70,
        ]);

        if (!$quiz->exists) {
            $quiz->save();
        }

        $quiz->load('questions');

        return view('teacher.quiz.edit', ['section' => $section, 'quiz' => $quiz]);
    }

    public function update(Request $request, Section $section): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'title_kk' => ['nullable', 'string', 'max:255'],
            'passing_percent' => ['required', 'integer', 'min:1', 'max:100'],
            'questions' => ['nullable', 'array'],
            'questions.*.id' => ['nullable'],
            'questions.*.text' => ['required', 'string'],
            'questions.*.text_kk' => ['nullable', 'string'],
            'questions.*.type' => ['nullable', 'in:single,multiple'],
            'questions.*.options' => ['nullable'],
            'questions.*.options_kk' => ['nullable'],
            'questions.*.correct_answer' => ['required'],
            'questions.*.order' => ['nullable', 'integer'],
        ]);

        $quiz = $section->quiz ?? Quiz::create([
            'section_id' => $section->id,
            'title' => $data['title'],
            'passing_percent' => $data['passing_percent'],
        ]);

        $quiz->update([
            'title' => $data['title'],
            'title_kk' => $data['title_kk'] ?? null,
            'passing_percent' => $data['passing_percent'],
        ]);

        $keptQuestionIds = [];

        foreach ($data['questions'] ?? [] as $index => $questionData) {
            $question = null;
            if (!empty($questionData['id'])) {
                $question = $quiz->questions()->whereKey($questionData['id'])->first();
            }

            $correctAnswer = is_array($questionData['correct_answer'])
                ? $questionData['correct_answer']
                : array_values(array_filter(array_map('trim', explode(',', (string) $questionData['correct_answer']))));

            $options = is_array($questionData['options'] ?? null)
                ? $questionData['options']
                : (json_decode($questionData['options'] ?? '{}', true) ?: ['A' => 'Да', 'B' => 'Нет']);

            $optionsKk = isset($questionData['options_kk']) && $questionData['options_kk'] !== ''
                ? (is_array($questionData['options_kk']) ? $questionData['options_kk'] : (json_decode($questionData['options_kk'], true) ?: null))
                : null;

            $payload = [
                'text' => $questionData['text'],
                'text_kk' => $questionData['text_kk'] ?? null,
                'type' => $questionData['type'] ?? 'single',
                'options' => $options,
                'options_kk' => $optionsKk,
                'correct_answer' => $correctAnswer,
                'order' => $questionData['order'] ?? $index,
            ];

            if ($question) {
                $question->update($payload);
            } else {
                $question = $quiz->questions()->create($payload);
            }

            $keptQuestionIds[] = $question->id;
        }

        $quiz->questions()
            ->when($keptQuestionIds !== [], fn ($query) => $query->whereNotIn('id', $keptQuestionIds))
            ->when($keptQuestionIds === [], fn ($query) => $query)
            ->delete();

        return redirect()->route('teacher.sections.show', $section)
            ->with('status', __('messages.quiz_saved'));
    }
}
