<x-app-layout>
    <x-slot name="header">{{ $section->getTitleForLocale(app()->getLocale()) }} — SmartLMS</x-slot>

    @php
        $student = auth()->user();
        $levelKey = $student?->placementLevelKey();
        $trackLabel = $section->is_revision ? __('messages.dashboard_level_beginner') : __('messages.dashboard_level_advanced');
        $sectionGradeLabel = $section->grade ? __('messages.auth_grade_' . $section->grade) : __('messages.teacher_grade_all');
    @endphp

    @if(session('status'))
        <div class="mb-6 rounded-xl bg-primary-50 border border-primary-200 px-4 py-3 text-primary-light">{{ session('status') }}</div>
    @endif

    <div class="mb-6 flex flex-wrap items-center gap-2">
        <span class="inline-flex items-center px-3 py-1.5 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $sectionGradeLabel }}</span>
        <span class="inline-flex items-center px-3 py-1.5 rounded-full {{ $section->is_revision ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200 border border-amber-200 dark:border-amber-700' : 'bg-violet-100 dark:bg-violet-900/30 text-violet-800 dark:text-violet-200 border border-violet-200 dark:border-violet-700' }} text-sm font-semibold">{{ $trackLabel }}</span>
        @if($levelKey)
            <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('messages.sections_student_track', ['level' => __('messages.dashboard_level_' . $levelKey)]) }}</span>
        @endif
    </div>

    @if(!empty($sectionPassed))
        <div class="mb-6 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 border-2 border-emerald-200 dark:border-emerald-700 p-6">
            <p class="text-lg font-bold text-emerald-800 dark:text-emerald-200 mb-1">{{ __('messages.sections_section_complete') }}</p>
            <p class="text-sm text-emerald-700 dark:text-emerald-300 mb-4">{{ __('messages.sections_what_next') }}</p>
            @if($nextSection ?? null)
                <a href="{{ route('sections.show', $nextSection) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 text-white rounded-xl font-semibold hover:bg-emerald-700 transition-colors shadow-lg">
                    {{ __('messages.sections_go_next') }}: {{ $nextSection->getTitleForLocale(app()->getLocale()) }}
                    <span aria-hidden="true">→</span>
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 text-white rounded-xl font-semibold hover:bg-emerald-700 transition-colors shadow-lg">
                    {{ __('messages.sections_back_to_cabinet') }}
                </a>
                <p class="text-sm text-emerald-600 dark:text-emerald-400 mt-3">{{ __('messages.sections_all_done_here') }}</p>
            @endif
        </div>
    @endif

    @if($isMaster)
        <div class="mb-6 rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-amber-800 font-medium">
            {{ __('messages.sections_master_badge') }}
        </div>
    @endif

    @if($section->is_featured ?? false)
        <div class="mb-6 rounded-2xl border-2 border-teal-400/70 dark:border-teal-600 bg-gradient-to-r from-teal-50 to-sky-50 dark:from-teal-950/40 dark:to-slate-800 px-5 py-4 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-widest text-teal-700 dark:text-teal-300 mb-1">{{ __('messages.section_featured_badge') }}</p>
            <p class="text-sm text-slate-700 dark:text-slate-200">{{ $section->getDescriptionForLocale(app()->getLocale()) }}</p>
        </div>
    @else
        <p class="text-gray-600 dark:text-slate-400 mb-8">{{ $section->getDescriptionForLocale(app()->getLocale()) }}</p>
    @endif

    <h3 class="text-lg font-bold text-slate-800 mb-4">{{ __('messages.sections_lessons') }}</h3>
    <div class="space-y-3">
        @foreach($section->lessons ?? [] as $lesson)
            <a href="{{ route('lessons.show', [$section, $lesson]) }}" class="block p-4 rounded-xl border border-slate-200 bg-white hover:border-primary hover:bg-primary-pale transition-colors">
                <span class="font-semibold text-slate-800 dark:text-slate-100">{{ $lesson->getTitleForLocale(app()->getLocale()) }}</span>
            </a>
        @endforeach
    </div>

    @if($section->quiz)
        <div class="mt-8 pt-8 border-t border-slate-200 dark:border-slate-600">
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">{{ __('messages.sections_quiz_unlock_hint') }}</p>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">{{ __('messages.sections_quiz_requirement_notice') }}</p>
            <a href="{{ route('quiz.show', $section) }}" class="inline-flex items-center px-6 py-3 bg-accent text-white rounded-xl font-semibold hover:bg-accent-dark transition-colors">
                {{ __('messages.sections_to_quiz') }}
            </a>
        </div>
    @endif
</x-app-layout>
