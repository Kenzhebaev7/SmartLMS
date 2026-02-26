<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class LessonsController extends Controller
{
    public function index(Request $request): View
    {
        $completedLessons = $request->user()
            ? $request->user()->lessonProgresses()->pluck('lesson_key')->all()
            : [];

        return view('lessons', [
            'completedLessons' => $completedLessons,
        ]);
    }
}

