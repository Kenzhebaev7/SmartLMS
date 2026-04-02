<x-app-layout>
    <x-slot name="header">{{ __('messages.sections_title') }}</x-slot>

    <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100 mb-2">{{ __('messages.sections_course_sections') }}</h2>
    <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">{{ __('messages.dashboard_continue_learning') }}</p>
    <div class="grid gap-4 md:grid-cols-2">
        @foreach($sections ?? [] as $section)
            @php
                $unlocked = in_array($section->id, $unlockedSectionIds ?? []);
                $prog = $progressBySection[$section->id] ?? ['percent' => 0, 'completed' => 0, 'total' => 0];
                $progress = is_array($prog) ? ($prog['percent'] ?? 0) : $prog;
                $completed = is_array($prog) ? ($prog['completed'] ?? 0) : 0;
                $total = is_array($prog) ? ($prog['total'] ?? 0) : 0;
                $sg = $section->grade ? (int) $section->grade : 9;
                $isFeatured = $section->is_featured ?? false;
                $btnClass = $isFeatured ? 'bg-teal-600 hover:bg-teal-700' : ($sg === 9 ? 'bg-emerald-500 hover:bg-emerald-600' : ($sg === 10 ? 'bg-sky-500 hover:bg-sky-600' : 'bg-indigo-500 hover:bg-indigo-600'));
            @endphp
            <div class="block p-6 rounded-2xl border {{ $isFeatured ? 'border-teal-400 dark:border-teal-500 ring-2 ring-teal-200/80 dark:ring-teal-700/60 bg-gradient-to-br from-teal-50/90 via-white to-sky-50/40 dark:from-teal-950/30 dark:via-slate-800 dark:to-slate-800 md:col-span-2 shadow-md' : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800' }} {{ $unlocked ? 'hover:shadow-lg transition-all' : 'opacity-75 pointer-events-none' }}">
                @if($isFeatured)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide bg-teal-600 text-white mb-2">{{ __('messages.section_featured_badge') }}</span>
                @endif
                <h3 class="font-bold text-slate-800 dark:text-slate-100">{{ $section->getTitleForLocale(app()->getLocale()) }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ trans_choice('messages.dashboard_lessons_n', $section->lessons->count()) }}</p>
                @if(isset($progressBySection) && auth()->user() && auth()->user()->role === 'student' && $total > 0)
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">{{ $completed }} {{ __('messages.dashboard_of') }} {{ $total }} {{ __('messages.dashboard_lessons_done') }}</p>
                @endif
                @if($unlocked)
                    <a href="{{ route('sections.show', $section) }}" class="mt-4 inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold {{ $btnClass }} text-white transition-colors">{{ __('messages.dashboard_start') }}</a>
                @else
                    <span class="mt-4 inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold {{ $btnClass }} text-white opacity-60 pointer-events-none">{{ __('messages.dashboard_start') }}</span>
                    <p class="mt-2 text-xs text-amber-800 dark:text-amber-200 font-medium">{{ __('messages.dashboard_locked') }} — {{ __('messages.sections_forbidden_quiz') }}</p>
                @endif
            </div>
        @endforeach
    </div>
</x-app-layout>
