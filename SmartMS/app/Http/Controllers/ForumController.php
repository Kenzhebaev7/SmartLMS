<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Section;
use App\Models\Thread;
use App\Services\AchievementService;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ForumController extends Controller
{
    public function index(Request $request): View
    {
        $query = Thread::visible()
            ->with(['user', 'section'])
            ->withCount([
                'comments as replies_count' => fn ($commentQuery) => $commentQuery->visible(),
                'comments as recent_replies_count' => fn ($commentQuery) => $commentQuery
                    ->visible()
                    ->where('created_at', '>=', Carbon::now()->subDays(7)),
            ])
            ->orderByDesc('is_pinned')
            ->latest();

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }
        if ($request->filled('tag') && in_array($request->string('tag')->toString(), Thread::tags(), true)) {
            $query->where('tag', $request->string('tag')->toString());
        }

        $threads = Thread::dedupeForDisplay($query->get());
        $sections = SectionController::dedupeSectionsForForum(Section::orderedForDisplay()->get());

        return view('forum.index', [
            'threads' => $threads,
            'sections' => $sections,
            'tagOptions' => Thread::tags(),
            'currentTag' => $request->string('tag')->toString(),
        ]);
    }

    public function create(): View
    {
        $sections = SectionController::dedupeSectionsForForum(Section::orderedForDisplay()->get());

        return view('forum.create', [
            'sections' => $sections,
            'tagOptions' => Thread::tags(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'section_id' => ['nullable', 'integer', 'exists:sections,id'],
            'tag' => ['required', 'in:'.implode(',', Thread::tags())],
            'is_pinned' => ['nullable', 'boolean'],
        ]);

        $request->user()->threads()->create([
            'title' => $data['title'],
            'body' => $data['body'],
            'section_id' => $data['section_id'] ?? null,
            'tag' => $data['tag'],
            'is_pinned' => $request->user()->isTeacher() || $request->user()->isAdmin()
                ? !empty($data['is_pinned'])
                : false,
        ]);

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
        $thread->load([
            'user',
            'section',
            'comments' => fn ($q) => $q
                ->visible()
                ->with(['user', 'replies' => fn ($r) => $r->visible()->with('user')])
                ->whereNull('parent_id')
                ->orderBy('created_at'),
        ]);

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
        return redirect()->route('forum.index')->with('status', __('messages.forum_thread_hidden'));
    }

    public function togglePin(Request $request, Thread $thread): RedirectResponse
    {
        if (!$request->user()->isTeacher() && !$request->user()->isAdmin()) {
            abort(403, __('messages.forbidden_teacher'));
        }

        $thread->update(['is_pinned' => !$thread->is_pinned]);

        return redirect()->route('forum.show', $thread)->with(
            'status',
            $thread->is_pinned ? __('messages.forum_thread_pinned') : __('messages.forum_thread_unpinned')
        );
    }

    public function destroyComment(Request $request, Comment $comment): RedirectResponse
    {
        if (!$request->user()->isTeacher() && !$request->user()->isAdmin()) {
            abort(403, __('messages.forbidden_teacher'));
        }
        $comment->update(['hidden_at' => now()]);
        return redirect()->route('forum.show', $comment->thread)->with('status', __('messages.forum_comment_hidden'));
    }
}
