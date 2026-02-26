<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\Section;
use App\Services\AchievementService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function show(Request $request, Section $section): View|RedirectResponse
    {
        $unlocked = SectionController::unlockedSectionIds($request->user());
        if (!in_array($section->id, $unlocked)) {
            abort(403, __('sections.forbidden_quiz'));
        }

        $quiz = $section->quiz;
        if (!$quiz) {
            return redirect()->route('sections.show', $section)
                ->with('info', __('quiz.not_created'));
        }

        $quiz->load('questions');

        $quizLocale = $request->get('quiz_locale', session('quiz_locale', app()->getLocale()));
        if (!in_array($quizLocale, ['ru', 'kk'], true)) {
            $quizLocale = 'ru';
        }
        session(['quiz_locale' => $quizLocale]);

        return view('quiz.show', [
            'section' => $section,
            'quiz' => $quiz,
            'quizLocale' => $quizLocale,
        ]);
    }

    public function submit(Request $request, Section $section): RedirectResponse
    {
        $unlocked = SectionController::unlockedSectionIds($request->user());
        if (!in_array($section->id, $unlocked)) {
            abort(403);
        }

        $quiz = $section->quiz;
        if (!$quiz) {
            abort(404);
        }

        $questions = $quiz->questions;
        $total = $questions->count();
        $correct = 0;

        foreach ($questions as $q) {
            $key = 'q_' . $q->id;
            $userAnswer = $request->input($key);
            $correctAnswers = (array) $q->correct_answer;
            if (in_array($userAnswer, $correctAnswers)) {
                $correct++;
            }
        }

        $score = $total > 0 ? (int) round(($correct / $total) * 100) : 0;
        $passed = $score >= $quiz->passing_percent;

        Result::create([
            'user_id' => $request->user()->id,
            'quiz_id' => $quiz->id,
            'score' => $score,
            'passed' => $passed,
            'attempted_at' => now(),
        ]);

        if ($passed) {
            $service = app(AchievementService::class);
            if (!$service->has($request->user(), 'first_quiz_pass')) {
                $service->award($request->user(), 'first_quiz_pass');
            }
            return redirect()->route('sections.show', $section)
                ->with('status', __('quiz.passed_status', ['score' => $score]));
        }

        return redirect()->route('quiz.show', $section)
            ->with('failed', true)
            ->with('score', $score)
            ->with('message', __('quiz.failed_status', ['score' => $score, 'percent' => $quiz->passing_percent]));
    }
}
