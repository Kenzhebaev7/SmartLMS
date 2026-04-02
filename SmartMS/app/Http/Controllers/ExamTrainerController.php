<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Result;
use App\Models\QuestionResult;
use App\Services\AchievementService;
use App\Services\TelegramNotifier;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExamTrainerController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        if ($user->role !== \App\Models\User::ROLE_STUDENT || $user->grade === null) {
            return redirect()->route('dashboard')->with('error', __('messages.exam_trainer_students_only'));
        }

        $quizzes = Quiz::examForGrade((int) $user->grade)->withCount('questions')->orderBy('title')->get();

        return view('exam-trainer.index', [
            'quizzes' => $quizzes,
            'grade' => $user->grade,
        ]);
    }

    public function show(Request $request, Quiz $quiz): View|RedirectResponse
    {
        $user = $request->user();
        if ($user->role !== \App\Models\User::ROLE_STUDENT || $user->grade === null || (int) $quiz->grade !== (int) $user->grade || $quiz->section_id !== null) {
            abort(403, __('messages.sections_forbidden_grade'));
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

        $effectiveDeadline = $quiz->deadline_at;
        if ($effectiveDeadline instanceof Carbon && Carbon::now()->greaterThan($effectiveDeadline)) {
            return redirect()->route('exam-trainer.index')
                ->with('failed', true)
                ->with('message', __('messages.quiz_deadline_passed'));
        }

        return view('exam-trainer.show', [
            'quiz' => $quiz,
            'quizLocale' => $quizLocale,
            'timeLimitSeconds' => $timeLimitSeconds,
            'remainingSeconds' => $remainingSeconds,
            'deadlineAt' => $effectiveDeadline,
        ]);
    }

    public function submit(Request $request, Quiz $quiz): RedirectResponse
    {
        $user = $request->user();
        if ($user->role !== \App\Models\User::ROLE_STUDENT || $user->grade === null || (int) $quiz->grade !== (int) $user->grade || $quiz->section_id !== null) {
            abort(403);
        }

        $now = Carbon::now();
        $effectiveDeadline = $quiz->deadline_at;
        if ($effectiveDeadline instanceof Carbon && $now->greaterThan($effectiveDeadline)) {
            return redirect()->route('exam-trainer.show', $quiz)
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
                        'user_id' => $user->id,
                        'quiz_id' => $quiz->id,
                        'score' => 0,
                        'passed' => false,
                        'attempted_at' => $now,
                    ]);
                    $request->session()->forget($sessionKey);

                    return redirect()->route('exam-trainer.show', $quiz)
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
                'user_id' => $user->id,
                'quiz_id' => $quiz->id,
                'question_id' => $q->id,
                'is_correct' => in_array($userAnswer, $correctAnswers),
            ]);
        }

        $score = $total > 0 ? (int) round(($correct / $total) * 100) : 0;
        $passed = $score >= $quiz->passing_percent;

        Result::create([
            'user_id' => $user->id,
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
            if (!$service->has($user, 'exam_quiz_pass')) {
                $service->award($user, 'exam_quiz_pass');
            }
            app(TelegramNotifier::class)->sendMessage(
                sprintf('✅ Экзаменационный квиз пройден: %s, ученик: %s, результат: %d%%', $quiz->title, $user->email, $score)
            );
            return redirect()->route('exam-trainer.index')
                ->with('status', __('messages.quiz_passed_status', ['score' => $score]));
        }

        app(TelegramNotifier::class)->sendMessage(
            sprintf('⚠️ Экзаменационный квиз НЕ пройден: %s, ученик: %s, результат: %d%% (нужно %d%%)', $quiz->title, $user->email, $score, $quiz->passing_percent)
        );

        return redirect()->route('exam-trainer.show', $quiz)
            ->with('failed', true)
            ->with('score', $score)
            ->with('message', __('messages.quiz_failed_status', ['score' => $score, 'percent' => $quiz->passing_percent]));
    }
}
