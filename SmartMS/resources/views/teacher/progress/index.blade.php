<x-app-layout>
    <x-slot name="header">{{ __('teacher.progress_index') }}</x-slot>

    @if(session('status'))
        <div class="mb-6 rounded-xl bg-primary-50 border border-primary-200 px-4 py-3 text-primary-light">{{ session('status') }}</div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-200 rounded-xl overflow-hidden">
            <thead class="bg-gray-50 dark:bg-slate-700">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-800 dark:text-slate-100">{{ __('teacher.student') }}</th>
                    @foreach($sections ?? [] as $s)
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $s->title }}</th>
                    @endforeach
                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-800 dark:text-slate-100">{{ __('sections.master_badge') }}</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-800 dark:text-slate-100">{{ __('teacher.achievements') }}</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-800 dark:text-slate-100">{{ __('teacher.feedback') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-600">
                @foreach($students ?? [] as $student)
                    <tr class="border-t border-gray-200">
                        <td class="px-4 py-3 font-medium text-slate-800 dark:text-slate-100">{{ $student->name }}</td>
                        @foreach($sections ?? [] as $s)
                            @php
                                $quiz = $s->quiz;
                                $passed = $quiz ? $student->results()->where('quiz_id', $quiz->id)->where('passed', true)->exists() : false;
                            @endphp
                            <td class="px-4 py-3">
                                @if($passed)
                                    <span class="text-[#0D9488] font-medium">Пройден</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                        @endforeach
                        <td class="px-4 py-3">
                            @foreach($sections ?? [] as $s)
                                @if(($masters ?? collect())->where('user_id', $student->id)->where('section_id', $s->id)->count())
                                    <span class="inline-flex px-2 py-0.5 rounded bg-amber-100 text-amber-800 text-xs font-semibold mr-1">{{ $s->title }}</span>
                                @else
                                    <form action="{{ route('teacher.progress.master') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $student->id }}">
                                        <input type="hidden" name="section_id" value="{{ $s->id }}">
                                        <button type="submit" class="text-primary text-sm hover:underline">{{ __('teacher.assign_master', ['section' => $s->title]) }}</button>
                                    </form>
                                @endif
                            @endforeach
                        </td>
                        <td class="px-4 py-3">
                            @if(isset($achievements) && $achievements->isNotEmpty())
                                <form action="{{ route('teacher.students.achievements.award', $student) }}" method="POST" class="inline-flex gap-1 flex-wrap">
                                    @csrf
                                    <select name="achievement_key" class="rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-2 py-1 text-sm">
                                        @foreach($achievements as $a)
                                            <option value="{{ $a->key }}">{{ $a->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="shrink-0 px-2 py-1 bg-emerald-600 text-white rounded-lg text-sm hover:bg-emerald-700">{{ __('teacher.award_achievement') }}</button>
                                </form>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <form action="{{ route('teacher.progress.feedback') }}" method="POST" class="flex gap-2">
                                @csrf
                                <input type="hidden" name="student_id" value="{{ $student->id }}">
                                <textarea name="body" rows="2" placeholder="{{ __('teacher.feedback_placeholder') }}" class="flex-1 min-w-[120px] rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-2 py-1 text-sm">{{ isset($feedbacks[$student->id]) ? $feedbacks[$student->id]->body : '' }}</textarea>
                                <button type="submit" class="shrink-0 px-3 py-1 bg-primary text-white rounded-lg text-sm hover:bg-primary-light">{{ __('teacher.save_feedback') }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
