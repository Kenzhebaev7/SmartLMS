<x-app-layout>
    <x-slot name="header">{{ $section->getTitleForLocale(app()->getLocale()) }} {{ __('messages.teacher_section_header') }}</x-slot>

    @if(session('status'))
        <div class="mb-6 rounded-xl bg-primary-50 border border-primary-200 px-4 py-3 text-primary-light">{{ session('status') }}</div>
    @endif

    <div class="flex gap-4 mb-8">
        <a href="{{ route('teacher.sections.lessons.create', $section) }}" class="px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary-light transition-colors">{{ __('messages.teacher_add_lesson') }}</a>
        <a href="{{ route('teacher.sections.quiz.edit', $section) }}" class="px-6 py-3 bg-accent text-white rounded-xl font-semibold hover:bg-accent-dark transition-colors">{{ __('messages.teacher_section_quiz') }}</a>
    </div>

    <h3 class="text-lg font-bold text-slate-800 mb-4">{{ __('messages.teacher_lessons') }}</h3>
    <div class="space-y-3">
        @foreach($section->lessons ?? [] as $lesson)
            <div class="flex items-center justify-between p-4 rounded-xl border border-gray-200 bg-white">
                <span class="font-semibold text-slate-800 dark:text-slate-100">{{ $lesson->getTitleForLocale(app()->getLocale()) }}</span>
                <div class="flex gap-2">
                    <a href="{{ route('teacher.lessons.edit', $lesson) }}" class="px-4 py-2 text-primary font-medium hover:bg-primary-pale rounded-lg">{{ __('messages.teacher_edit_lesson') }}</a>
                    <form action="{{ route('teacher.lessons.destroy', $lesson) }}" method="POST" onsubmit="return confirm('{{ __('messages.teacher_delete_confirm') }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg">{{ __('messages.teacher_delete') }}</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>
