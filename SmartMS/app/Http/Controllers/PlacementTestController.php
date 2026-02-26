<?php

namespace App\Http\Controllers;

use App\Services\AchievementService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlacementTestController extends Controller
{
    private const CORRECT_ANSWERS = [
        1 => 'yes', 2 => 'yes', 3 => 'yes', 4 => 'yes', 5 => 'no',
        6 => 'yes', 7 => 'yes', 8 => 'yes', 9 => 'no', 10 => 'yes',
    ];

    public function show(): View
    {
        $questions = [];
        foreach (array_keys(self::CORRECT_ANSWERS) as $id) {
            $questions[$id] = [
                'q' => __('placement.questions.' . $id),
                'correct' => self::CORRECT_ANSWERS[$id],
            ];
        }
        return view('placement-test', ['questions' => $questions]);
    }

    public function process(Request $request): RedirectResponse
    {
        $request->validate([
            'answers' => ['required', 'array'],
        ]);

        $answers = $request->input('answers', []);
        $total = count(self::CORRECT_ANSWERS);
        $correct = 0;

        foreach (self::CORRECT_ANSWERS as $id => $correctValue) {
            if (($answers[$id] ?? null) === $correctValue) {
                $correct++;
            }
        }

        $percent = $total > 0 ? round(($correct / $total) * 100) : 0;
        $threshold = config('smartlms.placement_threshold_percent', 60);
        $level = $percent >= $threshold ? 'advanced' : 'beginner';

        $request->user()->update(['level' => $level]);

        app(AchievementService::class)->award($request->user(), 'placement_done');

        $levelLabel = __('placement.level_' . $level);
        return redirect()->route('dashboard')->with('status', __('placement.status_passed', ['level' => $levelLabel, 'percent' => $percent]));
    }
}
