<x-app-layout>
    <x-slot name="header">{{ __('forum.title_projects') }}</x-slot>

    @if(session('status'))
        <div class="mb-6 rounded-xl bg-primary-50 dark:bg-primary/20 border border-primary-200 dark:border-primary/30 px-4 py-3 text-primary-700 dark:text-primary-200">{{ session('status') }}</div>
    @endif

    <div class="mb-4 p-4 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-sm text-slate-700 dark:text-slate-300">
        {{ __('forum.projects_description') }}
    </div>

    <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
        <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">{{ __('forum.topics') }}</h2>
        <div class="flex flex-wrap items-center gap-3">
            <form action="{{ route('forum.index') }}" method="GET" class="inline-flex items-center gap-2">
                <label for="section_id" class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ __('forum.by_section') }}</label>
                <select name="section_id" id="section_id" onchange="this.form.submit()" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-3 py-2 text-sm">
                    <option value="">{{ __('forum.all_projects') }}</option>
                    @foreach($sections ?? [] as $s)
                        <option value="{{ $s->id }}" {{ request('section_id') == $s->id ? 'selected' : '' }}>{{ $s->title }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('forum.create') }}" class="px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary-light transition-colors">{{ __('forum.new_topic') }}</a>
        </div>
    </div>

    <div class="space-y-3">
        @foreach($threads ?? [] as $thread)
            <a href="{{ route('forum.show', $thread) }}" class="block p-4 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 hover:border-primary dark:hover:border-primary/50 hover:bg-primary-pale dark:hover:bg-slate-700 transition-colors">
                <h3 class="font-bold text-slate-800 dark:text-slate-100">{{ $thread->title }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $thread->user->name }} · {{ $thread->created_at->format('d.m.Y H:i') }}@if($thread->section) · {{ $thread->section->title }}@endif</p>
            </a>
        @endforeach
    </div>

    @if(isset($threads) && method_exists($threads, 'links'))
        {{ $threads->links() }}
    @endif
</x-app-layout>
