<x-app-layout>
    <x-slot name="header">{{ __('messages.dashboard_title') }}</x-slot>

    @if(session('status'))
        <div class="mb-6 rounded-xl bg-emerald-50/90 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 px-4 py-3 text-emerald-800 dark:text-emerald-200 shadow-sky-100">
            {{ session('status') }}
        </div>
    @endif

    @if(auth()->user()->role === 'student')
        <div class="mb-6 rounded-xl bg-sky-50/90 dark:bg-sky-900/20 border border-sky-100 dark:border-sky-800 px-4 py-3 text-slate-700 dark:text-slate-200 text-sm shadow-sky-100">
            {{ __('messages.dashboard_onboarding') }}
        </div>
    @endif
    @if(auth()->user()->role === 'student' && auth()->user()->grade)
        <div class="mb-6 flex items-center gap-2">
            <span class="text-slate-600 dark:text-slate-400 text-sm font-medium">{{ __('messages.dashboard_grade') }}:</span>
            @php $ug = (int) auth()->user()->grade; @endphp
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold {{ $ug === 9 ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-200 border border-emerald-200 dark:border-emerald-700' : ($ug === 10 ? 'bg-sky-100 dark:bg-sky-900/30 text-sky-800 dark:text-sky-200 border border-sky-200 dark:border-sky-700' : 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-200 border border-indigo-200 dark:border-indigo-700') }}">{{ __('messages.auth_grade_' . $ug) }}</span>
        </div>
    @endif
    @if(auth()->user()->achievements && auth()->user()->achievements->count() > 0)
        <div class="mb-6">
            <p class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-2">{{ __('messages.dashboard_achievements') }}</p>
            <div class="flex flex-wrap gap-2">
                @foreach(auth()->user()->achievements->take(5) as $a)
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 text-sm text-emerald-800 dark:text-emerald-200 font-medium" title="{{ $a->description ?? '' }}">{{ $a->name }}</span>
                @endforeach
            </div>
        </div>
    @endif

    <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100 mb-2">{{ __('messages.dashboard_sections') }}</h2>
    @if(auth()->user()->role === 'student' && isset($sectionsTotal) && $sectionsTotal > 0)
        <p class="text-sm text-slate-600 dark:text-slate-400 mb-1">{{ __('messages.dashboard_sections_passed', ['passed' => $sectionsPassedCount ?? 0, 'total' => $sectionsTotal]) }}</p>
    @endif
    <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">{{ __('messages.dashboard_continue_learning') }}</p>
    <div class="grid gap-5 md:grid-cols-2">
        @foreach($sections ?? [] as $index => $section)
            @php
                $unlocked = in_array($section->id, $unlockedSectionIds ?? []);
                $prog = $progressBySection[$section->id] ?? ['percent' => 0, 'completed' => 0, 'total' => 0];
                $progress = is_array($prog) ? ($prog['percent'] ?? 0) : $prog;
                $completed = is_array($prog) ? ($prog['completed'] ?? 0) : 0;
                $total = is_array($prog) ? ($prog['total'] ?? 0) : 0;
                $sg = $section->grade ? (int) $section->grade : 9;
                $isFeatured = $section->is_featured ?? false;
                $btnClass = $isFeatured ? 'bg-teal-600 hover:bg-teal-700' : ($sg === 9 ? 'bg-emerald-500 hover:bg-emerald-600' : ($sg === 10 ? 'bg-sky-500 hover:bg-sky-600' : 'bg-indigo-500 hover:bg-indigo-600'));
                $barClass = $isFeatured ? 'bg-teal-500' : ($sg === 9 ? 'bg-emerald-500' : ($sg === 10 ? 'bg-sky-500' : 'bg-indigo-500'));
                $topicNum = $index + 1;
                $topicTotal = count($sections ?? []);
                $isRecommended = isset($recommendedSectionId) && $recommendedSectionId == $section->id;
                $isFeatured = $section->is_featured ?? false;
            @endphp
            <div class="animate-float-in opacity-0 {{ $isFeatured ? 'md:col-span-2' : '' }}" style="animation-delay: {{ $index * 0.08 }}s;">
                @if($unlocked)
                    <a href="{{ route('sections.show', $section) }}" class="block p-6 rounded-2xl border {{ $isFeatured ? 'border-teal-400 dark:border-teal-500 ring-2 ring-teal-200/80 dark:ring-teal-700/60 bg-gradient-to-br from-teal-50/90 via-white to-sky-50/50 dark:from-teal-950/40 dark:via-slate-800 dark:to-slate-800 shadow-md' : ($isRecommended ? 'border-amber-300 dark:border-amber-600 ring-1 ring-amber-200 dark:ring-amber-700' : 'border-slate-200 dark:border-slate-600') }} {{ !$isFeatured ? 'bg-white dark:bg-slate-800' : '' }} hover:border-slate-300 dark:hover:border-slate-500 hover:shadow-lg transition-all duration-300 hover:-translate-y-0.5 group">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                @if($isFeatured)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide bg-teal-600 text-white mb-2 shadow-sm">{{ __('messages.section_featured_badge') }}</span>
                                @endif
                                @if($topicTotal > 0)
                                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-0.5">{{ __('messages.dashboard_topic_of', ['n' => $topicNum, 'total' => $topicTotal]) }}</p>
                                @endif
                                @if($isRecommended && !$isFeatured)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-200 border border-amber-200 dark:border-amber-700 mb-1">{{ __('messages.dashboard_recommended_next') }}</span>
                                @endif
                                <h3 class="font-bold text-slate-800 dark:text-slate-100 group-hover:text-slate-700 dark:group-hover:text-slate-200 transition-colors">{{ $section->getTitleForLocale(app()->getLocale()) }}</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ trans_choice('messages.dashboard_lessons_n', $section->lessons->count()) }}</p>
                                @if(isset($progressBySection) && auth()->user() && auth()->user()->role === 'student' && $total > 0)
                                    <div class="mt-3">
                                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $completed }} {{ __('messages.dashboard_of') }} {{ $total }} {{ __('messages.dashboard_lessons_done') }}</p>
                                        <div class="h-1.5 rounded-full bg-slate-100 dark:bg-slate-600 overflow-hidden mt-1">
                                            <div class="h-full rounded-full {{ $barClass }} transition-all duration-500" style="width: {{ $progress }}%"></div>
                                        </div>
                                    </div>
                                @endif
                                <div class="mt-4">
                                    <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold {{ $btnClass }} text-white transition-colors">{{ __('messages.dashboard_start') }}</span>
                                </div>
                            </div>
                            <span class="shrink-0 text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300 transition-colors">→</span>
                        </div>
                    </a>
                @else
                    <div class="block p-6 rounded-2xl border {{ $isFeatured ? 'border-teal-300 dark:border-teal-700 ring-1 ring-teal-200/60 bg-teal-50/40 dark:bg-teal-950/20' : 'border-slate-200 dark:border-slate-600' }} bg-slate-50/80 dark:bg-slate-800/60 opacity-90 cursor-not-allowed select-none">
                        @if($isFeatured)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide bg-teal-600/80 text-white mb-2">{{ __('messages.section_featured_badge') }}</span>
                        @endif
                        @if($topicTotal > 0)
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-0.5">{{ __('messages.dashboard_topic_of', ['n' => $topicNum, 'total' => $topicTotal]) }}</p>
                        @endif
                        <h3 class="font-bold text-slate-600 dark:text-slate-400">{{ $section->getTitleForLocale(app()->getLocale()) }}</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ trans_choice('messages.dashboard_lessons_n', $section->lessons->count()) }}</p>
                        <div class="mt-3 flex items-center gap-2 text-amber-700 dark:text-amber-400 text-sm font-medium">
                            <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                            <span>{{ __('messages.dashboard_locked') }} — {{ __('messages.sections_forbidden_quiz') }}</span>
                        </div>
                        <div class="mt-4">
                            <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold {{ $btnClass }} text-white opacity-60 pointer-events-none">{{ __('messages.dashboard_start') }}</span>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="mt-8">
        <a href="{{ route('sections.index') }}" class="btn-micro inline-flex items-center px-6 py-3 bg-sky-500 text-white rounded-xl font-semibold hover:bg-sky-600 transition-all duration-200 hover:scale-105 shadow-sky-100">
            {{ __('messages.dashboard_all_sections') }} →
        </a>
    </div>
</x-app-layout>
