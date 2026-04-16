<x-app-layout>
    <x-slot name="header">{{ __('messages.teacher_certificates_title') }}</x-slot>

    @php
        $certificatesCollection = collect($certificates ?? []);
    @endphp

    @if(session('status'))
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 shadow-sm">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]" x-data="{ gradeFilter: 'all', levelFilter: 'all' }">
        <div class="space-y-6">
            <form action="{{ route('teacher.certificates.store') }}" method="POST" enctype="multipart/form-data" class="rounded-[30px] border border-slate-200 bg-white p-6 shadow-sm">
                @csrf

                <div class="rounded-2xl border border-sky-200 bg-sky-50 px-4 py-4 text-sm text-sky-900">
                    {{ __('messages.teacher_certificates_hint') }}
                </div>

                <div class="mt-6 grid gap-5">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_student') }}</label>
                        <select name="user_id" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-800">
                            <option value="">{{ __('messages.teacher_select_student') }}</option>
                            @foreach($students ?? [] as $student)
                                <option value="{{ $student->id }}" {{ old('user_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->name }} - {{ __('messages.auth_grade_' . $student->grade) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_certificate_title_label') }}</label>
                        <input type="text" name="title" value="{{ old('title') }}" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-800">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_certificate_date') }}</label>
                        <input type="date" name="awarded_at" value="{{ old('awarded_at', now()->format('Y-m-d')) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-800">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_description') }}</label>
                        <textarea name="description" rows="4" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-800">{{ old('description') }}</textarea>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('messages.teacher_certificate_file') }}</label>
                        <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.webp" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-800">
                        <p class="mt-1 text-xs text-slate-500">{{ __('messages.teacher_certificate_file_hint') }}</p>
                    </div>
                </div>

                <button type="submit" class="mt-6 inline-flex items-center justify-center rounded-2xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                    {{ __('messages.teacher_upload_certificate') }}
                </button>
            </form>
        </div>

        <div class="space-y-6">
            <section class="rounded-[30px] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 2xl:flex-row 2xl:items-end 2xl:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.teacher_uploaded_certificates') }}</p>
                        <h2 class="mt-2 text-2xl font-bold text-slate-900">{{ __('messages.teacher_certificates_overview') }}</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ __('messages.teacher_certificates_count', ['count' => count($certificates ?? [])]) }}</p>
                    </div>

                    <div class="grid gap-3 lg:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">{{ __('messages.dashboard_grade') }}</label>
                            <select x-model="gradeFilter" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700">
                                <option value="all">{{ __('messages.teacher_grade_all') }}</option>
                                <option value="9">{{ __('messages.auth_grade_9') }}</option>
                                <option value="10">{{ __('messages.auth_grade_10') }}</option>
                                <option value="11">{{ __('messages.auth_grade_11') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">{{ __('messages.dashboard_level') }}</label>
                            <select x-model="levelFilter" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700">
                                <option value="all">{{ __('messages.teacher_all_levels') }}</option>
                                <option value="beginner">{{ __('messages.dashboard_level_beginner') }}</option>
                                <option value="advanced">{{ __('messages.dashboard_level_advanced') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </section>

            <section class="grid gap-4">
                @forelse($certificatesCollection as $certificate)
                    @php
                        $studentLevel = $certificate->student?->placementLevelKey();
                    @endphp
                    <article
                        x-show="(gradeFilter === 'all' || gradeFilter === '{{ (string) ($certificate->student->grade ?? 'all') }}')
                            && (levelFilter === 'all' || levelFilter === '{{ $studentLevel ?? 'pending' }}')"
                        x-transition.opacity.duration.200ms
                        class="rounded-[30px] border border-slate-200 bg-white p-5 shadow-sm"
                        style="display: none;"
                    >
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">
                                        {{ $certificate->student->grade ? __('messages.auth_grade_' . $certificate->student->grade) : __('messages.teacher_grade_all') }}
                                    </span>
                                    @if($studentLevel)
                                        <span class="inline-flex items-center rounded-full {{ $studentLevel === 'beginner' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-sky-100 text-sky-800 border border-sky-200' }} px-3 py-1 text-xs font-semibold">
                                            {{ __('messages.dashboard_level_' . $studentLevel) }}
                                        </span>
                                    @endif
                                </div>

                                <h3 class="mt-3 text-xl font-bold text-slate-900">{{ $certificate->title }}</h3>
                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $certificate->student->name }}
                                    @if($certificate->awarded_at)
                                        / {{ $certificate->awarded_at->format('d.m.Y') }}
                                    @endif
                                </p>
                                @if($certificate->description)
                                    <p class="mt-3 text-sm leading-7 text-slate-600 whitespace-pre-wrap">{{ $certificate->description }}</p>
                                @endif
                                <p class="mt-3 text-xs text-slate-400">{{ __('messages.teacher_certificate_uploaded_by', ['teacher' => $certificate->teacher->name]) }}</p>
                            </div>

                            <div class="flex w-full flex-col gap-3 lg:w-40">
                                <a href="{{ asset('storage/' . $certificate->file_path) }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                                    {{ __('messages.teacher_open_certificate') }}
                                </a>
                                <form action="{{ route('teacher.certificates.destroy', $certificate) }}" method="POST" onsubmit="return confirm('{{ __('messages.teacher_delete_certificate_confirm') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 transition hover:bg-red-100">
                                        {{ __('messages.teacher_delete') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-[30px] border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center text-sm text-slate-500">
                        {{ __('messages.teacher_no_certificates') }}
                    </div>
                @endforelse
            </section>
        </div>
    </div>
</x-app-layout>
