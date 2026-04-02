<?php

namespace App\Http\Controllers;

use App\Models\QuestionResult;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ErrorTrainerController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $hardQuestions = QuestionResult::query()
            ->with(['question.quiz.section'])
            ->where('user_id', $user->id)
            ->selectRaw('question_id, SUM(is_correct = 0) as wrong_count, COUNT(*) as total_count')
            ->groupBy('question_id')
            ->havingRaw('SUM(is_correct = 0) > 0')
            ->orderByDesc('wrong_count')
            ->limit(50)
            ->get();

        return view('trainer.errors', [
            'hardQuestions' => $hardQuestions,
        ]);
    }
}

