<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function edit(Section $section): View
    {
        $quiz = $section->quiz ?? new Quiz(['section_id' => $section->id, 'title' => 'Квиз: ' . $section->title, 'passing_percent' => 70]);
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

        $quiz = $section->quiz ?? Quiz::create(['section_id' => $section->id, 'title' => $data['title'], 'passing_percent' => $data['passing_percent']]);
        $quiz->update([
            'title' => $data['title'],
            'title_kk' => $data['title_kk'] ?? null,
            'passing_percent' => $data['passing_percent'],
        ]);

        $order = 0;
        foreach ($data['questions'] ?? [] as $q) {
            $correct = is_array($q['correct_answer']) ? $q['correct_answer'] : array_filter(explode(',', $q['correct_answer']));
            $opts = is_array($q['options'] ?? null) ? $q['options'] : (json_decode($q['options'] ?? '{}', true) ?: ['A' => 'Да', 'B' => 'Нет']);
            $optsKk = isset($q['options_kk']) && $q['options_kk'] !== '' ? (is_array($q['options_kk']) ? $q['options_kk'] : (json_decode($q['options_kk'], true) ?: null)) : null;
            $type = $q['type'] ?? 'single';
            $payload = [
                'text' => $q['text'],
                'text_kk' => $q['text_kk'] ?? null,
                'type' => $type,
                'options' => $opts,
                'options_kk' => $optsKk,
                'correct_answer' => $correct,
                'order' => $order++,
            ];
            if (!empty($q['id'])) {
                Question::where('id', $q['id'])->update($payload);
            } else {
                $quiz->questions()->create($payload);
            }
        }

        return redirect()->route('teacher.sections.show', $section)->with('status', 'Квиз сохранён.');
    }
}
