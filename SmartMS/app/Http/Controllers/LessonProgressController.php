<?php

namespace App\Http\Controllers;

use App\Models\LessonProgress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LessonProgressController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'lesson_key' => ['required', 'string'],
        ]);

        $lessonKey = $request->input('lesson_key');

        $user = $request->user();

        LessonProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'lesson_key' => $lessonKey,
            ],
            [
                'completed_at' => now(),
            ]
        );

        return back()->with('status', __('Урок отмечен как пройден.'));
    }
}

