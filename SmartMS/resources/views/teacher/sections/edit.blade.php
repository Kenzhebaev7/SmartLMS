<x-app-layout>
    <x-slot name="header">{{ __('messages.teacher_edit_section_header') }}</x-slot>

    <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <form action="{{ route('teacher.sections.update', $section) }}" method="POST" class="space-y-6 rounded-[30px] border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')

            <div class="rounded-2xl border border-sky-200 bg-sky-50 px-4 py-4 text-sm text-sky-900">
                <p class="font-semibold">{{ __('messages.teacher_edit_targeting_hint') }}</p>
                <p class="mt-1">{{ __('messages.teacher_section_grade_helper') }}</p>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_title') }}</label>
                    <input type="text" name="title" value="{{ old('title', $section->title) }}" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-800">
                    @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_title') }} (KK)</label>
                    <input type="text" name="title_kk" value="{{ old('title_kk', $section->title_kk) }}" placeholder="{{ __('messages.teacher_placeholder_title_kk') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-800">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_order') }}</label>
                    <input type="number" name="order" value="{{ old('order', $section->order) }}" min="0" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-800">
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_description') }} (RU)</label>
                    <textarea name="description" rows="4" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-800">{{ old('description', $section->description) }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_description') }} (KK)</label>
                    <textarea name="description_kk" rows="3" placeholder="{{ __('messages.teacher_placeholder_description_kk') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-800">{{ old('description_kk', $section->description_kk) }}</textarea>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                    <div class="max-w-xl">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.teacher_section_target') }}</p>
                        <h3 class="mt-2 text-xl font-bold text-slate-900">{{ __('messages.teacher_section_target_title') }}</h3>
                        <p class="mt-2 text-sm text-slate-600">{{ __('messages.teacher_section_target_desc') }}</p>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2 lg:w-[26rem]">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.dashboard_grade') }}</label>
                            <select name="grade" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-800">
                                <option value="" @selected(old('grade', $section->grade) === null)>{{ __('messages.teacher_grade_all') }}</option>
                                <option value="9" @selected((string) old('grade', $section->grade) === '9')>{{ __('messages.auth_grade_9') }}</option>
                                <option value="10" @selected((string) old('grade', $section->grade) === '10')>{{ __('messages.auth_grade_10') }}</option>
                                <option value="11" @selected((string) old('grade', $section->grade) === '11')>{{ __('messages.auth_grade_11') }}</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <label class="flex w-full items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3">
                                <input type="checkbox" name="is_revision" value="1" @checked(old('is_revision', $section->is_revision)) class="rounded border-slate-300 text-primary focus:ring-primary">
                                <span>
                                    <span class="block text-sm font-semibold text-slate-800">{{ __('messages.teacher_is_revision') }}</span>
                                    <span class="block text-xs text-slate-500">{{ __('messages.teacher_revision_hint') }}</span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                    {{ __('messages.teacher_save') }}
                </button>
                <a href="{{ route('teacher.sections.show', $section) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    {{ __('messages.teacher_cancel') }}
                </a>
            </div>
        </form>

        <aside class="rounded-[30px] border border-slate-200 bg-gradient-to-br from-white to-sky-50/50 p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.teacher_section_target') }}</p>
            <h2 class="mt-2 text-2xl font-bold text-slate-900">{{ __('messages.teacher_section_edit_snapshot') }}</h2>
            <p class="mt-2 text-sm leading-7 text-slate-600">{{ __('messages.teacher_section_edit_snapshot_desc') }}</p>

            <div class="mt-6 grid gap-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.dashboard_grade') }}</p>
                    <p class="mt-2 text-lg font-bold text-slate-900">
                        {{ $section->grade ? __('messages.auth_grade_' . $section->grade) : __('messages.teacher_grade_all') }}
                    </p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.dashboard_level') }}</p>
                    <p class="mt-2 text-lg font-bold text-slate-900">
                        {{ $section->is_revision ? __('messages.dashboard_level_beginner') : __('messages.dashboard_level_advanced') }}
                    </p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.teacher_order') }}</p>
                    <p class="mt-2 text-lg font-bold text-slate-900">{{ $section->order }}</p>
                </div>
            </div>
        </aside>
    </div>
</x-app-layout>
