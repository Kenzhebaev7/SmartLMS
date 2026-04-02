<?php

namespace App\Http\Controllers;

use App\Services\AchievementService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlacementTestController extends Controller
{
    private const QUESTIONS_PER_GRADE = 15;

    /** Правильные ответы для каждого класса (1..15 => yes/no). Все текущие вопросы — «Да». */
    private const CORRECT_BY_GRADE = [
        9  => [1 => 'yes', 2 => 'yes', 3 => 'yes', 4 => 'yes', 5 => 'yes', 6 => 'yes', 7 => 'no', 8 => 'yes', 9 => 'yes', 10 => 'yes', 11 => 'yes', 12 => 'yes', 13 => 'yes', 14 => 'yes', 15 => 'yes'],
        10 => [1 => 'yes', 2 => 'yes', 3 => 'yes', 4 => 'yes', 5 => 'yes', 6 => 'yes', 7 => 'yes', 8 => 'yes', 9 => 'yes', 10 => 'yes', 11 => 'yes', 12 => 'yes', 13 => 'yes', 14 => 'yes', 15 => 'yes'],
        11 => [1 => 'yes', 2 => 'yes', 3 => 'yes', 4 => 'yes', 5 => 'yes', 6 => 'yes', 7 => 'yes', 8 => 'yes', 9 => 'yes', 10 => 'yes', 11 => 'yes', 12 => 'yes', 13 => 'yes', 14 => 'yes', 15 => 'yes'],
    ];

    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $grade = $user->grade;

        if ($user->role === \App\Models\User::ROLE_STUDENT && $grade === null) {
            return redirect()->route('dashboard')->with('error', __('messages.placement_need_grade'));
        }

        $grade = $grade ?? 9;
        if (!isset(self::CORRECT_BY_GRADE[$grade])) {
            $grade = 9;
        }

        $correctAnswers = self::CORRECT_BY_GRADE[$grade];
        $questions = [];
        foreach (array_keys($correctAnswers) as $id) {
            $questions[$id] = [
                'q' => __('placement.questions_' . $grade . '.' . $id),
                'correct' => $correctAnswers[$id],
            ];
        }

        return view('placement-test', [
            'questions' => $questions,
            'grade' => $grade,
        ]);
    }

    public function process(Request $request): RedirectResponse
    {
        $user = $request->user();
        $grade = $user->grade ?? 9;
        if (!isset(self::CORRECT_BY_GRADE[$grade])) {
            $grade = 9;
        }

        $correctAnswers = self::CORRECT_BY_GRADE[$grade];
        $request->validate([
            'answers' => ['required', 'array'],
        ]);

        $answers = $request->input('answers', []);
        $total = count($correctAnswers);
        $correct = 0;

        foreach ($correctAnswers as $id => $correctValue) {
            if (($answers[$id] ?? null) === $correctValue) {
                $correct++;
            }
        }

        $percent = $total > 0 ? round(($correct / $total) * 100) : 0;
        $threshold = (int) config('smartlms.placement_threshold_percent', 50);
        $passed = $percent >= $threshold;

        $user->update(['placement_passed' => $passed]);

        app(AchievementService::class)->award($user, 'placement_done');

        if ($passed) {
            return redirect()->route('dashboard')->with('status', __('messages.placement_status_passed_full', ['percent' => $percent]));
        }
        return redirect()->route('dashboard')->with('status', __('messages.placement_status_revision', ['percent' => $percent]));
    }
}
