<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Section;
use App\Models\Thread;
use App\Services\AchievementService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ForumController extends Controller
{
    public function index(Request $request): View
    {
        $query = Thread::visible()->with(['user', 'section'])->latest();
        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }
        $threads = $query->paginate(15)->withQueryString();
        $sections = Section::orderBy('order')->get();
        return view('forum.index', ['threads' => $threads, 'sections' => $sections]);
    }

    public function create(): View
    {
        $sections = Section::orderBy('order')->get();
        return view('forum.create', ['sections' => $sections]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'section_id' => ['nullable', 'integer', 'exists:sections,id'],
        ]);
        $request->user()->threads()->create($data);
        $service = app(AchievementService::class);
        if (!$service->has($request->user(), 'first_thread')) {
            $service->award($request->user(), 'first_thread');
        }
        return redirect()->route('forum.index')->with('status', __('messages.topic_created'));
    }

    public function show(Thread $thread): View
    {
        if ($thread->hidden_at) {
            abort(404);
        }
        $thread->load(['comments' => fn ($q) => $q->visible()->with(['user', 'replies' => fn ($r) => $r->visible()->with('user')])->whereNull('parent_id')->orderBy('created_at')]);
        return view('forum.show', ['thread' => $thread]);
    }

    public function storeComment(Request $request, Thread $thread): RedirectResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string'],
            'parent_id' => ['nullable', 'integer', 'exists:comments,id'],
        ]);
        $thread->comments()->create([
            'user_id' => $request->user()->id,
            'parent_id' => $data['parent_id'] ?? null,
            'body' => $data['body'],
        ]);
        $service = app(AchievementService::class);
        if (!$service->has($request->user(), 'first_comment')) {
            $service->award($request->user(), 'first_comment');
        }
        return redirect()->route('forum.show', $thread)->with('status', __('messages.comment_added'));
    }

    public function destroyThread(Request $request, Thread $thread): RedirectResponse
    {
        if (!$request->user()->isTeacher() && !$request->user()->isAdmin()) {
            abort(403, __('messages.forbidden_teacher'));
        }
        $thread->update(['hidden_at' => now()]);
        return redirect()->route('forum.index')->with('status', __('forum.thread_hidden'));
    }

    public function destroyComment(Request $request, Comment $comment): RedirectResponse
    {
        if (!$request->user()->isTeacher() && !$request->user()->isAdmin()) {
            abort(403, __('messages.forbidden_teacher'));
        }
        $comment->update(['hidden_at' => now()]);
        return redirect()->route('forum.show', $comment->thread)->with('status', __('forum.comment_hidden'));
    }
}
