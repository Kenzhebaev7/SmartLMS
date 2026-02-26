<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LessonController extends Controller
{
    public function create(Section $section): View
    {
        return view('teacher.lessons.create', ['section' => $section]);
    }

    public function store(Request $request, Section $section): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'title_kk' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'content_kk' => ['nullable', 'string'],
            'video_url' => ['nullable', 'string', 'max:500'],
            'video_id' => ['nullable', 'string', 'max:32'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        $path = null;
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('lessons', 'public');
        }

        $videoId = $data['video_id'] ?? null;
        $videoUrl = $data['video_url'] ?? null;
        if (empty($videoId) && !empty($videoUrl) && preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $videoUrl, $m)) {
            $videoId = $m[1];
        }

        $section->lessons()->create([
            'title' => $data['title'],
            'title_kk' => $data['title_kk'] ?? null,
            'content' => $data['content'] ?? '',
            'content_kk' => $data['content_kk'] ?? null,
            'video_url' => $videoUrl,
            'video_id' => $videoId,
            'file_path' => $path,
            'order' => $data['order'] ?? ($section->lessons()->max('order') + 1),
        ]);

        return redirect()->route('teacher.sections.show', $section)->with('status', __('messages.lesson_added'));
    }

    public function edit(Lesson $lesson): View
    {
        $lesson->load('section');
        return view('teacher.lessons.edit', ['lesson' => $lesson]);
    }

    public function update(Request $request, Lesson $lesson): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'title_kk' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'content_kk' => ['nullable', 'string'],
            'video_url' => ['nullable', 'string', 'max:500'],
            'video_id' => ['nullable', 'string', 'max:32'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        $path = $lesson->file_path;
        if ($request->hasFile('file')) {
            if ($path) Storage::disk('public')->delete($path);
            $path = $request->file('file')->store('lessons', 'public');
        }

        $videoId = $data['video_id'] ?? null;
        $videoUrl = $data['video_url'] ?? null;
        if (empty($videoId) && !empty($videoUrl) && preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $videoUrl, $m)) {
            $videoId = $m[1];
        }

        $lesson->update([
            'title' => $data['title'],
            'title_kk' => $data['title_kk'] ?? null,
            'content' => $data['content'] ?? '',
            'content_kk' => $data['content_kk'] ?? null,
            'video_url' => $videoUrl,
            'video_id' => $videoId,
            'file_path' => $path,
            'order' => $data['order'] ?? $lesson->order,
        ]);

        return redirect()->route('teacher.sections.show', $lesson->section)->with('status', __('messages.lesson_updated'));
    }

    public function destroy(Lesson $lesson): RedirectResponse
    {
        $section = $lesson->section;
        if ($lesson->file_path) {
            Storage::disk('public')->delete($lesson->file_path);
        }
        $lesson->delete();
        return redirect()->route('teacher.sections.show', $section)->with('status', 'Урок удалён.');
    }
}
