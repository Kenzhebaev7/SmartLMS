<x-app-layout>
    <x-slot name="header">{{ __('messages.teacher_certificates_title') }}</x-slot>

    @if(session('status'))
        <div class="mb-6 rounded-xl bg-primary-50 border border-primary-200 px-4 py-3 text-primary-light">{{ session('status') }}</div>
    @endif

    <div class="grid gap-6 lg:grid-cols-5">
        <div class="lg:col-span-2">
            <form action="{{ route('teacher.certificates.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 rounded-2xl border border-slate-200 bg-white p-6">
                @csrf
                <div class="rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-900">
                    {{ __('messages.teacher_certificates_hint') }}
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('messages.teacher_student') }}</label>
                    <select name="user_id" required class="w-full rounded-lg border border-gray-300 px-4 py-2">
                        <option value="">{{ __('messages.teacher_select_student') }}</option>
                        @foreach($students ?? [] as $student)
                            <option value="{{ $student->id }}" {{ old('user_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->name }} — {{ __('messages.auth_grade_' . $student->grade) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('messages.teacher_certificate_title_label') }}</label>
                    <input type="text" name="title" value="{{ old('title') }}" required class="w-full rounded-lg border border-gray-300 px-4 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('messages.teacher_certificate_date') }}</label>
                    <input type="date" name="awarded_at" value="{{ old('awarded_at', now()->format('Y-m-d')) }}" class="w-full rounded-lg border border-gray-300 px-4 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('messages.teacher_description') }}</label>
                    <textarea name="description" rows="4" class="w-full rounded-lg border border-gray-300 px-4 py-2">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('messages.teacher_certificate_file') }}</label>
                    <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.webp" required class="w-full rounded-lg border border-gray-300 px-4 py-2">
                    <p class="mt-1 text-xs text-slate-500">{{ __('messages.teacher_certificate_file_hint') }}</p>
                </div>

                <button type="submit" class="px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary-light transition-colors">
                    {{ __('messages.teacher_upload_certificate') }}
                </button>
            </form>
        </div>

        <div class="lg:col-span-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                <div class="flex items-center justify-between gap-4 mb-4">
                    <h2 class="text-xl font-bold text-slate-800">{{ __('messages.teacher_uploaded_certificates') }}</h2>
                    <span class="text-sm text-slate-500">{{ __('messages.teacher_certificates_count', ['count' => count($certificates ?? [])]) }}</span>
                </div>

                <div class="space-y-4">
                    @forelse($certificates ?? [] as $certificate)
                        <div class="rounded-xl border border-slate-200 p-4">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                                <div>
                                    <h3 class="font-semibold text-slate-800">{{ $certificate->title }}</h3>
                                    <p class="text-sm text-slate-500 mt-1">
                                        {{ $certificate->student->name }} · {{ $certificate->student->grade ? __('messages.auth_grade_' . $certificate->student->grade) : '—' }}
                                        @if($certificate->awarded_at)
                                            · {{ $certificate->awarded_at->format('d.m.Y') }}
                                        @endif
                                    </p>
                                    @if($certificate->description)
                                        <p class="mt-2 text-sm text-slate-600 whitespace-pre-wrap">{{ $certificate->description }}</p>
                                    @endif
                                    <p class="mt-2 text-xs text-slate-400">{{ __('messages.teacher_certificate_uploaded_by', ['teacher' => $certificate->teacher->name]) }}</p>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ asset('storage/' . $certificate->file_path) }}" target="_blank" rel="noopener" class="px-4 py-2 rounded-lg bg-sky-600 text-white font-medium hover:bg-sky-700 transition-colors">
                                        {{ __('messages.teacher_open_certificate') }}
                                    </a>
                                    <form action="{{ route('teacher.certificates.destroy', $certificate) }}" method="POST" onsubmit="return confirm('{{ __('messages.teacher_delete_certificate_confirm') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-4 py-2 rounded-lg text-red-600 hover:bg-red-50 transition-colors">
                                            {{ __('messages.teacher_delete') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-500">{{ __('messages.teacher_no_certificates') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
