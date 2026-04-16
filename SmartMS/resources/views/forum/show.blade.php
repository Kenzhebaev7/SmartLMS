<x-app-layout>
    <x-slot name="header">{{ __('messages.forum_title_projects') }}</x-slot>

    @if(session('status'))
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 shadow-sm">
            {{ session('status') }}
        </div>
    @endif

    @php
        $visibleComments = collect($thread->comments ?? []);
        $recentReplies = $visibleComments->flatMap(function ($comment) {
            return collect([$comment])->merge($comment->replies ?? []);
        })->filter(fn ($comment) => $comment->created_at?->gte(now()->subDays(7)))->count();
    @endphp

    <div class="space-y-8">
        <section class="rounded-[30px] border border-slate-200 bg-gradient-to-br from-white via-sky-50/50 to-amber-50/40 p-6 shadow-sm">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        @if($thread->is_pinned)
                            <span class="inline-flex items-center rounded-full border border-amber-200 bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">
                                {{ __('messages.forum_pinned_label') }}
                            </span>
                        @endif
                        @if($thread->tag)
                            <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600">
                                {{ __('messages.forum_tag_' . $thread->tag) }}
                            </span>
                        @endif
                        @if($thread->section)
                            <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600">
                                {{ $thread->section->getTitleForLocale(app()->getLocale()) }}
                            </span>
                        @endif
                    </div>

                    <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-900">{{ $thread->title }}</h2>
                    <p class="mt-3 text-sm text-slate-500">{{ $thread->user->name }} · {{ $thread->created_at->format('d.m.Y H:i') }}</p>
                    <div class="mt-5 whitespace-pre-wrap text-sm leading-7 text-slate-700">{{ $thread->body }}</div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 xl:w-60">
                    <div class="rounded-2xl border border-white bg-white/90 p-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">{{ __('messages.forum_replies_label') }}</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">{{ $visibleComments->count() }}</p>
                    </div>
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-emerald-700">{{ __('messages.forum_new_replies_label') }}</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">{{ $recentReplies }}</p>
                    </div>
                </div>
            </div>

            @auth
                @if(in_array(auth()->user()->role, [\App\Models\User::ROLE_TEACHER, \App\Models\User::ROLE_ADMIN]))
                    <div class="mt-5 flex flex-wrap gap-3">
                        <form action="{{ route('forum.threads.pin', $thread) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="inline-flex items-center justify-center rounded-2xl border border-amber-200 bg-white px-4 py-3 text-sm font-semibold text-amber-800 transition hover:bg-amber-50">
                                {{ $thread->is_pinned ? __('messages.forum_unpin_topic') : __('messages.forum_pin_topic') }}
                            </button>
                        </form>
                        <form action="{{ route('forum.threads.destroy', $thread) }}" method="POST" onsubmit="return confirm('{{ __('messages.forum_confirm_hide_topic') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center justify-center rounded-2xl border border-red-200 bg-white px-4 py-3 text-sm font-semibold text-red-700 transition hover:bg-red-50">
                                {{ __('messages.forum_hide_topic') }}
                            </button>
                        </form>
                    </div>
                @endif
            @endauth
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
            <div class="rounded-[30px] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.forum_comments') }}</p>
                        <h3 class="mt-2 text-xl font-bold text-slate-900">{{ __('messages.forum_discussion_title') }}</h3>
                    </div>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse($thread->comments ?? [] as $comment)
                        @include('forum._comment', ['comment' => $comment])
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-5 py-6 text-sm text-slate-500">
                            {{ __('messages.forum_empty_comments') }}
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="space-y-6">
                <form action="{{ route('forum.comments.store', $thread) }}" method="POST" class="rounded-[30px] border border-slate-200 bg-white p-6 shadow-sm">
                    @csrf
                    <input type="hidden" name="parent_id" value="">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.forum_your_comment') }}</p>
                    <h3 class="mt-2 text-xl font-bold text-slate-900">{{ __('messages.forum_reply_box_title') }}</h3>
                    <label class="mt-4 block text-sm font-medium text-slate-700">{{ __('messages.forum_create_body') }}</label>
                    <textarea name="body" rows="5" required class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-800"></textarea>
                    @error('body')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    <button type="submit" class="mt-4 inline-flex items-center justify-center rounded-2xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                        {{ __('messages.forum_send') }}
                    </button>
                </form>

                <div class="rounded-[30px] border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.forum_topics') }}</p>
                    <h3 class="mt-2 text-xl font-bold text-slate-900">{{ __('messages.forum_reply_guidelines_title') }}</h3>
                    <p class="mt-2 text-sm leading-7 text-slate-600">{{ __('messages.forum_reply_guidelines_desc') }}</p>
                    <a href="{{ route('forum.index') }}" class="mt-5 inline-flex items-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        {{ __('messages.forum_back_list') }}
                    </a>
                </div>
            </div>
        </section>
    </div>

    <script>
        document.querySelectorAll('.reply-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var form = this.closest('.rounded-xl')?.querySelector('.reply-form');
                if (form) {
                    form.classList.toggle('hidden');
                    if (!form.classList.contains('hidden')) {
                        form.querySelector('textarea')?.focus();
                    }
                }
            });
        });
    </script>
</x-app-layout>
