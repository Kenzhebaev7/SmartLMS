<x-app-layout>
    <x-slot name="header">{{ __('messages.profile_certificates_title') }}</x-slot>

    <div class="max-w-4xl">
        <div class="mb-6 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-900">
            {{ __('messages.profile_certificates_desc') }}
        </div>

        <div class="space-y-4">
            @forelse($certificates ?? [] as $certificate)
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-slate-800">{{ $certificate->title }}</h2>
                            <p class="text-sm text-slate-500 mt-1">
                                @if($certificate->awarded_at)
                                    {{ $certificate->awarded_at->format('d.m.Y') }}
                                @endif
                                @if($certificate->teacher)
                                    · {{ __('messages.profile_certificate_teacher', ['teacher' => $certificate->teacher->name]) }}
                                @endif
                            </p>
                            @if($certificate->description)
                                <p class="mt-3 text-sm text-slate-600 whitespace-pre-wrap">{{ $certificate->description }}</p>
                            @endif
                        </div>

                        <a href="{{ asset('storage/' . $certificate->file_path) }}" target="_blank" rel="noopener" class="inline-flex items-center px-5 py-3 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700 transition-colors">
                            {{ __('messages.profile_open_certificate') }}
                        </a>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-slate-200 bg-white p-6 text-slate-500">
                    {{ __('messages.profile_no_certificates') }}
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
