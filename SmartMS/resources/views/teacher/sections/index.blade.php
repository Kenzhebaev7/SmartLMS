<x-app-layout>
    <x-slot name="header">{{ __('teacher.sections_index') }}</x-slot>

    @if(session('status'))
        <div class="mb-6 rounded-xl bg-primary-50 border border-primary-200 px-4 py-3 text-primary-light">{{ session('status') }}</div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-slate-800">{{ __('nav.sections') }}</h2>
        <a href="{{ route('teacher.sections.create') }}" class="px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary-light transition-colors">{{ __('teacher.add_section') }}</a>
    </div>

    <div class="space-y-3">
        @foreach($sections ?? [] as $section)
            <div class="flex items-center justify-between p-4 rounded-xl border border-gray-200 bg-white">
                <div>
                    <span class="font-semibold text-slate-800">{{ $section->title }}</span>
                    <span class="text-sm text-gray-500 ml-2">{{ __('teacher.order_short') }}: {{ $section->order }}</span>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('teacher.sections.show', $section) }}" class="px-4 py-2 text-primary font-medium hover:bg-primary-pale rounded-lg">{{ __('teacher.open') }}</a>
                    <a href="{{ route('teacher.sections.edit', $section) }}" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">{{ __('teacher.edit') }}</a>
                    <form action="{{ route('teacher.sections.destroy', $section) }}" method="POST" onsubmit="return confirm('{{ __('teacher.delete_section_confirm') }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg">{{ __('teacher.delete') }}</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>
