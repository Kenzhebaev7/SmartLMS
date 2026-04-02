<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\Section;
use App\Models\QuestionResult;
use App\Services\AchievementService;
use App\Services\TelegramNotifier;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function show(Request $request, Section $section): View|RedirectResponse
    {
        $unlocked = SectionController::unlockedSectionIds($request->user());
        if (!in_array($section->id, $unlocked)) {
            abort(403, __('messages.sections_forbidden_quiz'));
        }

        $quiz = $section->quiz;
        if (!$quiz) {
            return redirect()->route('sections.show', $section)
                ->with('info', __('messages.quiz_not_created'));
        }

        $quiz->load('questions');

        $quizLocale = $request->get('quiz_locale', session('quiz_locale', app()->getLocale()));
        if (!in_array($quizLocale, ['ru', 'kk'], true)) {
            $quizLocale = 'ru';
        }
        session(['quiz_locale' => $quizLocale]);

         $timeLimitSeconds = $quiz->time_limit_seconds;
         $remainingSeconds = null;
         if ($timeLimitSeconds) {
             $sessionKey = 'quiz_start_' . $quiz->id;
             $startedAt = $request->session()->get($sessionKey);
             if (!$startedAt) {
                 $startedAt = Carbon::now();
                 $request->session()->put($sessionKey, $startedAt);
             } else {
                 $startedAt = Carbon::parse($startedAt);
             }
             $elapsed = Carbon::now()->diffInSeconds($startedAt);
             $remainingSeconds = max(0, $timeLimitSeconds - $elapsed);
         }

         $effectiveDeadline = $quiz->deadline_at ?? $section->deadline_at;
         if ($effectiveDeadline instanceof Carbon && Carbon::now()->greaterThan($effectiveDeadline)) {
             return redirect()->route('sections.show', $section)
                 ->with('failed', true)
                 ->with('message', __('messages.quiz_deadline_passed'));
         }

        return view('quiz.show', [
            'section' => $section,
            'quiz' => $quiz,
            'quizLocale' => $quizLocale,
            'timeLimitSeconds' => $timeLimitSeconds,
            'remainingSeconds' => $remainingSeconds,
            'deadlineAt' => $effectiveDeadline,
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

        $now = Carbon::now();
        $effectiveDeadline = $quiz->deadline_at ?? $section->deadline_at;
        if ($effectiveDeadline instanceof Carbon && $now->greaterThan($effectiveDeadline)) {
            return redirect()->route('quiz.show', $section)
                ->with('failed', true)
                ->with('message', __('messages.quiz_deadline_passed'));
        }

        if ($quiz->time_limit_seconds) {
            $sessionKey = 'quiz_start_' . $quiz->id;
            $startedAt = $request->session()->get($sessionKey);
            if ($startedAt) {
                $startedAt = Carbon::parse($startedAt);
                $elapsed = $now->diffInSeconds($startedAt);
                if ($elapsed > $quiz->time_limit_seconds) {
                    Result::create([
                        'user_id' => $request->user()->id,
                        'quiz_id' => $quiz->id,
                        'score' => 0,
                        'passed' => false,
                        'attempted_at' => $now,
                    ]);
                    $request->session()->forget($sessionKey);

                    return redirect()->route('quiz.show', $section)
                        ->with('failed', true)
                        ->with('message', __('messages.quiz_time_over'));
                }
            }
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
            QuestionResult::create([
                'user_id' => $request->user()->id,
                'quiz_id' => $quiz->id,
                'question_id' => $q->id,
                'is_correct' => in_array($userAnswer, $correctAnswers),
            ]);
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

        if ($quiz->time_limit_seconds ?? null) {
            $request->session()->forget('quiz_start_' . $quiz->id);
        }

        if ($passed) {
            $service = app(AchievementService::class);
            if (!$service->has($request->user(), 'first_quiz_pass')) {
                $service->award($request->user(), 'first_quiz_pass');
            }
            app(TelegramNotifier::class)->sendMessage(
                sprintf('✅ Квиз пройден: %s, ученик: %s, результат: %d%%', $section->title, $request->user()->email, $score)
            );
            return redirect()->route('sections.show', $section)
                ->with('status', __('messages.quiz_passed_status', ['score' => $score]));
        }

        app(TelegramNotifier::class)->sendMessage(
            sprintf('⚠️ Квиз НЕ пройден: %s, ученик: %s, результат: %d%% (нужно %d%%)', $section->title, $request->user()->email, $score, $quiz->passing_percent)
        );

        return redirect()->route('quiz.show', $section)
            ->with('failed', true)
            ->with('score', $score)
            ->with('message', __('messages.quiz_failed_status', ['score' => $score, 'percent' => $quiz->passing_percent]));
    }
}
