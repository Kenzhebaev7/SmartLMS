<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Thread;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LessonController extends Controller
{
    public function show(Request $request, Section $section, Lesson $lesson): Response
    {
        $user = $request->user();
        $unlocked = SectionController::unlockedSectionIds($user);
        if (!in_array($section->id, $unlocked)) {
            abort(403, __('messages.sections_forbidden_level'));
        }
        if ($lesson->section_id !== $section->id) {
            abort(404);
        }
        $lesson->refresh();
        if ($user && $user->role === \App\Models\User::ROLE_STUDENT && $section->grade !== null && (int) $section->grade !== $user->effectiveGradeForStudent()) {
            abort(403, __('sections.forbidden_grade'));
        }

        $questionThreads = Thread::where('lesson_id', $lesson->id)->with('user')->latest()->get();

        $section->loadMissing('quiz');
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
        if ($user->role === \App\Models\User::ROLE_STUDENT && $section->grade !== null && (int) $section->grade !== $user->effectiveGradeForStudent()) {
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

        if ($request->wantsJson()) {
            return response()->json([
                'completed' => true,
            ]);
        }

        return redirect()->route('lessons.show', [$section, $lesson])
            ->with('status', __('messages.lessons_completed'));
    }

    public function pdfHandout(Request $request, Section $section, Lesson $lesson): StreamedResponse|RedirectResponse
    {
        $user = $request->user();
        $unlocked = SectionController::unlockedSectionIds($user);
        if (!in_array($section->id, $unlocked) || $lesson->section_id !== $section->id) {
            abort(403);
        }

        $title = $lesson->getTitleForLocale(app()->getLocale());
        $content = $lesson->getContentForLocale(app()->getLocale()) ?? '';
        $body = Str::markdown($content);
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><style>body{font-family:DejaVu Sans,sans-serif;padding:20px;line-height:1.6;color:#333;} h1{font-size:18px;border-bottom:1px solid #ccc;padding-bottom:8px;} .content p{margin:0.5em 0;} .content ul,.content ol{margin:0.5em 0 0.5em 1.2em;} .content h2{font-size:15px;margin:1em 0 0.4em;} .content h3{font-size:13px;margin:0.8em 0 0.3em;}</style></head><body><h1>' . htmlspecialchars($title) . '</h1><div class="content">' . $body . '</div></body></html>';

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = Str::slug($title) . '-конспект.pdf';

        return response()->streamDownload(function () use ($dompdf) {
            echo $dompdf->output();
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
