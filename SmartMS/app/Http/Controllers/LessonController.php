<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Thread;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class LessonController extends Controller
{
    public function show(Request $request, Section $section, Lesson $lesson): Response
    {
        $user = $request->user();
        $unlocked = SectionController::unlockedSectionIds($user);
        if (!in_array($section->id, $unlocked)) {
            abort(403, __('sections.forbidden_level'));
        }
        if ($lesson->section_id !== $section->id) {
            abort(404);
        }
        $lesson->refresh(); // всегда актуальные данные после правок учителя
        // Студент уровня beginner не имеет доступа к урокам для продвинутых
        if ($user && $user->role === \App\Models\User::ROLE_STUDENT && $user->level === 'beginner' && $lesson->is_advanced) {
            abort(403, __('sections.forbidden_level'));
        }

        $questionThreads = Thread::where('lesson_id', $lesson->id)->with('user')->latest()->get();

        $lessonsOrdered = $section->lessons()->orderBy('order')->get();
        $currentIndex = $lessonsOrdered->search(fn ($l) => $l->id === $lesson->id);
        $prevLesson = $currentIndex > 0 ? $lessonsOrdered->get($currentIndex - 1) : null;
        $nextLesson = $currentIndex !== false && $currentIndex < $lessonsOrdered->count() - 1
            ? $lessonsOrdered->get($currentIndex + 1) : null;

        $lessonCompleted = $user && LessonProgress::where('user_id', $user->id)
            ->where(function ($q) use ($lesson) {
                $q->where('lesson_id', $lesson->id)->orWhere('lesson_key', (string) $lesson->id);
            })->exists();

        return response()
            ->view('sections.lesson', [
                'section' => $section,
                'lesson' => $lesson,
                'questionThreads' => $questionThreads,
                'prevLesson' => $prevLesson,
                'nextLesson' => $nextLesson,
                'lessonCompleted' => $lessonCompleted,
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    public function storeQuestion(Request $request, Section $section, Lesson $lesson): RedirectResponse
    {
        $unlocked = SectionController::unlockedSectionIds($request->user());
        if (!in_array($section->id, $unlocked) || $lesson->section_id !== $section->id) {
            abort(403);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        $request->user()->threads()->create([
            'title' => $data['title'],
            'body' => $data['body'],
            'lesson_id' => $lesson->id,
        ]);

        return redirect()->route('lessons.show', [$section, $lesson])
            ->with('status', __('messages.topic_created'));
    }

    public function complete(Request $request, Section $section, Lesson $lesson): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        $unlocked = SectionController::unlockedSectionIds($user);
        if (!in_array($section->id, $unlocked) || $lesson->section_id !== $section->id) {
            abort(403);
        }
        if ($user->role === \App\Models\User::ROLE_STUDENT && $user->level === 'beginner' && $lesson->is_advanced) {
            abort(403);
        }

        $existing = LessonProgress::where('user_id', $user->id)
            ->where(function ($q) use ($lesson) {
                $q->where('lesson_id', $lesson->id)->orWhere('lesson_key', (string) $lesson->id);
            })->first();

        if ($existing) {
            $existing->update(['completed_at' => now(), 'lesson_id' => $lesson->id]);
        } else {
            LessonProgress::create([
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
                'lesson_key' => (string) $lesson->id,
                'completed_at' => now(),
            ]);
        }
        $existed = (bool) $existing;

        if (!$existed) {
            $user->increment('xp', 10);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'completed' => true,
                'xp_awarded' => !$existed ? 10 : 0,
                'user_xp' => $user->fresh()->xp,
            ]);
        }

        return redirect()->route('lessons.show', [$section, $lesson])
            ->with('status', __('lessons.completed'));
    }
}
