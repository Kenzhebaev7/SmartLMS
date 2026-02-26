<x-app-layout>
    <x-slot name="header">{{ __('dashboard.title') }}</x-slot>

    @if(session('status'))
        <div class="mb-6 rounded-xl bg-emerald-50/90 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 px-4 py-3 text-emerald-800 dark:text-emerald-200 shadow-sky-100">
            {{ session('status') }}
        </div>
    @endif

    @if(auth()->user()->role === 'student')
        <div class="mb-6 rounded-xl bg-sky-50/90 dark:bg-sky-900/20 border border-sky-100 dark:border-sky-800 px-4 py-3 text-slate-700 dark:text-slate-200 text-sm shadow-sky-100">
            {{ __('dashboard.onboarding') }}
        </div>
    @endif

    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4 flex-wrap">
            <div class="flex items-center gap-3">
                <span class="text-slate-600 dark:text-slate-400 text-sm font-medium">{{ __('dashboard.level') }}:</span>
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold {{ (auth()->user()->level ?? '') === 'advanced' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200 border border-amber-200 dark:border-amber-700' : 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-200 border border-emerald-200 dark:border-emerald-700' }}">
                    {{ (auth()->user()->level ?? '') === 'advanced' ? __('dashboard.level_advanced') : __('dashboard.level_beginner') }}
                </span>
            </div>
            <div class="flex items-center gap-2 px-4 py-2 bg-sky-50 dark:bg-sky-900/30 border border-sky-100 dark:border-sky-700 rounded-xl shadow-sky-100">
                <span class="text-sky-800 dark:text-sky-200 font-bold">{{ auth()->user()->xp ?? 0 }}</span>
                <span class="text-sky-600 dark:text-sky-400 text-sm font-medium">XP</span>
            </div>
        </div>
        @if(auth()->user()->achievements && auth()->user()->achievements->count() > 0)
            <div class="w-full mt-2">
                <p class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-2">{{ __('dashboard.achievements') }}</p>
                <div class="flex flex-wrap gap-2">
                    @foreach(auth()->user()->achievements->take(5) as $a)
                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 text-sm text-emerald-800 dark:text-emerald-200 font-medium transition-transform duration-200 hover:scale-105" title="{{ $a->description }}">{{ $a->name }} (+{{ $a->xp }} XP)</span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100 mb-6">{{ __('dashboard.sections') }}</h2>
    <div class="grid gap-5 md:grid-cols-2">
        @foreach($sections ?? [] as $index => $section)
            @php
                $unlocked = in_array($section->id, $unlockedSectionIds ?? []);
                $progress = $progressBySection[$section->id] ?? 0;
            @endphp
            <div class="animate-float-in opacity-0" style="animation-delay: {{ $index * 0.08 }}s;">
                @if($unlocked)
                    <a href="{{ route('sections.show', $section) }}" class="block p-6 rounded-2xl border border-sky-100 dark:border-sky-700 bg-white dark:bg-slate-800 hover:border-sky-200 dark:hover:border-sky-500 hover:shadow-sky-200 dark:hover:shadow-sky-900/30 shadow-sky-100 transition-all duration-300 hover:-translate-y-1 hover:scale-[1.02] group">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-bold text-slate-800 dark:text-slate-100 group-hover:text-sky-700 dark:group-hover:text-sky-300 transition-colors">{{ $section->getTitleForLocale(app()->getLocale()) }}</h3>
                                    @if($section->level)
                                        <span class="inline-flex px-2 py-0.5 rounded-lg text-xs font-medium {{ $section->level === 'advanced' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200' : 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-200' }}">{{ $section->level === 'advanced' ? __('dashboard.level_advanced') : __('dashboard.level_beginner') }}</span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 rounded-lg text-xs font-medium bg-slate-200 dark:bg-slate-600 text-slate-700 dark:text-slate-200">{{ __('dashboard.section_level_all') }}</span>
                                    @endif
                                </div>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $section->getDescriptionForLocale(app()->getLocale()) ? Str::limit($section->getDescriptionForLocale(app()->getLocale()), 80) : __('dashboard.no_description') }}</p>
                                @if(isset($progressBySection) && auth()->user() && auth()->user()->role === 'student')
                                    <div class="mt-3">
                                        <div class="h-2 rounded-full bg-slate-100 dark:bg-slate-600 overflow-hidden">
                                            <div class="h-full rounded-full bg-sky-500 dark:bg-sky-400 transition-all duration-500" style="width: {{ $progress }}%"></div>
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $progress }}% {{ __('dashboard.completed') }}</p>
                                    </div>
                                @endif
                            </div>
                            <span class="shrink-0 text-sky-500 dark:text-sky-400 group-hover:translate-x-0.5 transition-transform">→</span>
                        </div>
                    </a>
                @else
                    <div class="block p-6 rounded-2xl border border-slate-200 dark:border-slate-600 bg-slate-50/80 dark:bg-slate-800/60 opacity-90 cursor-not-allowed select-none">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-bold text-slate-600 dark:text-slate-400">{{ $section->getTitleForLocale(app()->getLocale()) }}</h3>
                                    @if($section->level)
                                        <span class="inline-flex px-2 py-0.5 rounded-lg text-xs font-medium bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-300">{{ $section->level === 'advanced' ? __('dashboard.level_advanced') : __('dashboard.level_beginner') }}</span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 rounded-lg text-xs font-medium bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-300">{{ __('dashboard.section_level_all') }}</span>
                                    @endif
                                </div>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $section->getDescriptionForLocale(app()->getLocale()) ? Str::limit($section->getDescriptionForLocale(app()->getLocale()), 80) : __('dashboard.no_description') }}</p>
                                <div class="mt-3 flex items-center gap-2 text-amber-700 dark:text-amber-400 text-sm font-medium">
                                    <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ __('dashboard.locked') }} — {{ __('sections.forbidden_quiz') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="mt-8">
        <a href="{{ route('sections.index') }}" class="btn-micro inline-flex items-center px-6 py-3 bg-sky-500 text-white rounded-xl font-semibold hover:bg-sky-600 transition-all duration-200 hover:scale-105 shadow-sky-100">
            {{ __('dashboard.all_sections') }} →
        </a>
    </div>
</x-app-layout>
