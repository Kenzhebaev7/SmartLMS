<x-app-layout>
    <x-slot name="header">{{ __('messages.forum_title_projects') }}</x-slot>

    @if(session('status'))
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 shadow-sm">
            {{ session('status') }}
        </div>
    @endif

    @php
        $threadCollection = collect($threads ?? []);
    @endphp

    <div class="space-y-8">
        <section class="rounded-[30px] border border-slate-200 bg-gradient-to-br from-white via-sky-50/50 to-amber-50/40 p-6 shadow-sm">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
                <div class="max-w-3xl">
                    <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 border border-slate-200">
                        {{ __('messages.forum_topics') }}
                    </span>
                    <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-900">{{ __('messages.forum_hub_title') }}</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-600">{{ __('messages.forum_projects_description') }}</p>
                </div>

                <a href="{{ route('forum.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                    {{ __('messages.forum_new_topic') }}
                </a>
            </div>

            <div class="mt-6 grid gap-4 lg:grid-cols-3">
                <div class="rounded-2xl border border-white bg-white/90 p-4 shadow-sm text-fit">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.forum_topics') }}</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $threadCollection->count() }}</p>
                </div>
                <div class="rounded-2xl border border-white bg-white/90 p-4 shadow-sm text-fit">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.forum_pinned_label') }}</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $threadCollection->where('is_pinned', true)->count() }}</p>
                </div>
                <div class="rounded-2xl border border-white bg-white/90 p-4 shadow-sm text-fit">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.forum_new_replies_label') }}</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $threadCollection->sum('recent_replies_count') }}</p>
                </div>
            </div>
        </section>

        <section class="rounded-[30px] border border-slate-200 bg-white p-6 shadow-sm">
            <form action="{{ route('forum.index') }}" method="GET" class="grid gap-4 xl:grid-cols-[1fr_1fr_auto]">
                <div>
                    <label for="section_id" class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">{{ __('messages.forum_by_section') }}</label>
                    <select name="section_id" id="section_id" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700">
                        <option value="">{{ __('messages.forum_all_projects') }}</option>
                        @foreach($sections ?? [] as $s)
                            <option value="{{ $s->id }}" {{ request('section_id') == $s->id ? 'selected' : '' }}>{{ $s->getTitleForLocale(app()->getLocale()) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="tag" class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">{{ __('messages.forum_tag_label') }}</label>
                    <select name="tag" id="tag" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700">
                        <option value="">{{ __('messages.forum_tag_all') }}</option>
                        @foreach($tagOptions ?? [] as $tagOption)
                            <option value="{{ $tagOption }}" {{ ($currentTag ?? '') === $tagOption ? 'selected' : '' }}>
                                {{ __('messages.forum_tag_' . $tagOption) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-3">
                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                        {{ __('messages.teacher_open') }}
                    </button>
                    @if(request()->filled('section_id') || request()->filled('tag'))
                        <a href="{{ route('forum.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            {{ __('messages.teacher_cancel') }}
                        </a>
                    @endif
                </div>
            </form>
        </section>

        @if(($sections ?? collect())->isEmpty())
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-900">
                {{ __('messages.forum_no_sections_hint') }}
            </div>
        @endif

        @if($threadCollection->isEmpty())
            <div class="rounded-[30px] border border-slate-200 bg-white px-6 py-10 text-center">
                <h3 class="text-lg font-bold text-slate-900">{{ __('messages.forum_empty_title') }}</h3>
                <p class="mt-2 text-sm text-slate-500">{{ __('messages.forum_empty_desc') }}</p>
                <a href="{{ route('forum.create') }}" class="mt-5 inline-flex rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                    {{ __('messages.forum_new_topic') }}
                </a>
            </div>
        @else
            <section class="grid gap-4">
                @foreach($threadCollection as $thread)
                    <a href="{{ route('forum.show', $thread) }}" class="block rounded-[30px] border border-slate-200 bg-white p-5 shadow-sm transition hover:border-slate-300 hover:shadow-md">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    @if($thread->is_pinned)
                                        <span class="inline-flex items-center rounded-full border border-amber-200 bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">
                                            {{ __('messages.forum_pinned_label') }}
                                        </span>
                                    @endif
                                    @if($thread->tag)
                                        <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">
                                            {{ __('messages.forum_tag_' . $thread->tag) }}
                                        </span>
                                    @endif
                                    @if($thread->section)
                                        <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600">
                                            {{ $thread->section->getTitleForLocale(app()->getLocale()) }}
                                        </span>
                                    @endif
                                </div>

                                <h3 class="mt-4 text-xl font-bold text-slate-900">{{ $thread->title }}</h3>
                                <p class="mt-2 text-sm leading-7 text-slate-600">{{ \Illuminate\Support\Str::limit($thread->body, 180) }}</p>
                                <p class="mt-3 text-sm text-slate-500">
                                    {{ $thread->user->name }} · {{ $thread->created_at->format('d.m.Y H:i') }}
                                </p>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-2 lg:w-52">
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">{{ __('messages.forum_replies_label') }}</p>
                                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ $thread->replies_count ?? 0 }}</p>
                                </div>
                                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-emerald-700">{{ __('messages.forum_new_replies_label') }}</p>
                                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ $thread->recent_replies_count ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </section>
        @endif

        @if(isset($threads) && method_exists($threads, 'links'))
            {{ $threads->links() }}
        @endif
    </div>
</x-app-layout>
