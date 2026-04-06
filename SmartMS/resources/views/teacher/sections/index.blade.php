<x-app-layout>
    <x-slot name="header">{{ __('messages.teacher_sections_index') }}</x-slot>

    @if(session('status'))
        <div class="mb-6 rounded-xl bg-primary-50 border border-primary-200 px-4 py-3 text-primary-light">{{ session('status') }}</div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-slate-800">{{ __('messages.nav_sections') }}</h2>
        <a href="{{ route('teacher.sections.create') }}" class="px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary-light transition-colors">{{ __('messages.teacher_add_section') }}</a>
    </div>

    <div class="space-y-3">
        @foreach($sections ?? [] as $section)
            <div class="flex items-center justify-between p-4 rounded-xl border border-gray-200 bg-white">
                <div>
                    <span class="font-semibold text-slate-800 dark:text-slate-100">{{ $section->getTitleForLocale(app()->getLocale()) }}</span>
                    <span class="text-sm text-gray-500 ml-2">{{ __('messages.teacher_order_short') }}: {{ $section->order }}</span>
                    <p class="mt-1 text-sm text-slate-500">
                        {{ __('messages.teacher_section_target_summary', ['grade' => $section->grade ? __('messages.auth_grade_' . $section->grade) : __('messages.teacher_grade_all'), 'level' => $section->is_revision ? __('messages.dashboard_level_beginner') : __('messages.dashboard_level_advanced')]) }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('teacher.sections.show', $section) }}" class="px-4 py-2 text-primary font-medium hover:bg-primary-pale rounded-lg">{{ __('messages.teacher_open') }}</a>
                    <a href="{{ route('teacher.sections.edit', $section) }}" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">{{ __('messages.teacher_edit') }}</a>
                    <form action="{{ route('teacher.sections.destroy', $section) }}" method="POST" onsubmit="return confirm('{{ __('messages.teacher_delete_section_confirm') }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg">{{ __('messages.teacher_delete') }}</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>
