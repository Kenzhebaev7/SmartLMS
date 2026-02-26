<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlacementController extends Controller
{
    public function showTest(): View
    {
        return view('test');
    }

    public function processTest(Request $request): RedirectResponse
    {
        $request->validate([
            'answers' => ['required', 'array'],
        ]);

        $answers = $request->input('answers', []);

        // Count how many "yes" answers user has given
        $score = collect($answers)
            ->filter(fn ($value) => $value === 'yes')
            ->count();

        if ($score >= 2) {
            $request->user()->update(['level' => 'advanced']);
        } else {
            $request->user()->update(['level' => 'beginner']);
        }

        return redirect()->route('dashboard');
    }

    public function lessons(Request $request): View
    {
        $completedLessons = $request->user()
            ? $request->user()->lessonProgresses()->pluck('lesson_key')->all()
            : [];

        return view('lessons', [
            'completedLessons' => $completedLessons,
        ]);
    }
}

