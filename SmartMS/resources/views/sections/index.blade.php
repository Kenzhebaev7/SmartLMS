<x-app-layout>
    <x-slot name="header">{{ __('sections.title') }}</x-slot>

    <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100 mb-6">{{ __('sections.course_sections') }}</h2>
    <div class="grid gap-4 md:grid-cols-2">
        @foreach($sections ?? [] as $section)
            @php
                $unlocked = in_array($section->id, $unlockedSectionIds ?? []);
                $progress = $progressBySection[$section->id] ?? 0;
            @endphp
            <a href="{{ $unlocked ? route('sections.show', $section) : '#' }}"
               class="block p-6 rounded-2xl border-2 transition-all {{ $unlocked ? 'border-primary dark:border-primary/50 bg-white dark:bg-slate-800 hover:bg-primary-pale dark:hover:bg-slate-700 shadow-sm' : 'border-slate-200 dark:border-slate-600 bg-slate-100 dark:bg-slate-800/50 opacity-75 pointer-events-none grayscale' }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-bold text-slate-800 dark:text-slate-100">{{ $section->getTitleForLocale(app()->getLocale()) }}</h3>
                            @if($section->level)
                                <span class="inline-flex px-2 py-0.5 rounded-lg text-xs font-medium {{ $section->level === 'advanced' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200' : 'bg-primary-100 dark:bg-primary/20 text-primary' }}">{{ $section->level === 'advanced' ? __('dashboard.level_advanced') : __('dashboard.level_beginner') }}</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 rounded-lg text-xs font-medium bg-slate-200 dark:bg-slate-600 text-slate-700 dark:text-slate-200">{{ __('dashboard.section_level_all') }}</span>
                            @endif
                        </div>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $section->getDescriptionForLocale(app()->getLocale()) ? Str::limit($section->getDescriptionForLocale(app()->getLocale()), 100) : '' }}</p>
                        @if(isset($progressBySection) && auth()->user() && auth()->user()->role === 'student')
                            <div class="mt-3">
                                <div class="h-2 rounded-full bg-slate-200 dark:bg-slate-600 overflow-hidden">
                                    <div class="h-full rounded-full bg-primary dark:bg-primary/80 transition-all" style="width: {{ $progress }}%"></div>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $progress }}% {{ __('dashboard.completed') }}</p>
                            </div>
                        @endif
                    </div>
                    @if(!$unlocked)
                        <span class="inline-flex items-center px-2 py-1 rounded-lg bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200 text-xs font-semibold shrink-0">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                            {{ __('dashboard.locked') }}
                        </span>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
</x-app-layout>
