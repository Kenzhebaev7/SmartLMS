<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-2xl p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-teal-600 text-white flex items-center justify-center text-lg font-bold">
                        {{ mb_substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="text-base font-semibold text-slate-900">
                            {{ auth()->user()->name }}
                        </p>
                        <p class="text-sm text-slate-500">
                            {{ auth()->user()->email }}
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="#password-section" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-semibold bg-slate-900 text-white hover:bg-slate-800 transition-colors">
                        {{ __('messages.profile_update_login_password') }}
                    </a>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-5">
                <div class="lg:col-span-3 space-y-6">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div id="profile-info" class="p-4 sm:p-8 bg-white shadow sm:rounded-lg flex flex-col h-full">
                            <div class="max-w-xl">
                                @include('profile.partials.update-profile-information-form')
                            </div>
                        </div>

                        <div id="password-section" class="p-4 sm:p-8 bg-white shadow sm:rounded-lg flex flex-col h-full">
                            <div class="max-w-xl">
                                @include('profile.partials.update-password-form')
                            </div>
                        </div>
                    </div>
                </div>

                @if(auth()->user()?->role === \App\Models\User::ROLE_STUDENT)
                    <div class="space-y-4">
                        @if(isset($certificates) && count($certificates) > 0)
                            <div>
                                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">
                                    {{ __('messages.profile_certificates_title') }}
                                </h3>
                                <p class="text-sm text-slate-600 dark:text-slate-400">
                                    {{ __('messages.profile_certificates_desc') }}
                                </p>
                            </div>

                            <div class="space-y-3 max-h-[260px] overflow-y-auto pr-1">
                                @foreach($certificates as $certificate)
                                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-4">
                                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $certificate->title }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                            @if($certificate->awarded_at)
                                                {{ $certificate->awarded_at->format('d.m.Y') }}
                                            @endif
                                            @if($certificate->teacher)
                                                · {{ __('messages.profile_certificate_teacher', ['teacher' => $certificate->teacher->name]) }}
                                            @endif
                                        </p>
                                        @if($certificate->description)
                                            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ $certificate->description }}</p>
                                        @endif
                                        <a href="{{ asset('storage/' . $certificate->file_path) }}" target="_blank" rel="noopener" class="mt-3 inline-flex items-center px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition-colors">
                                            {{ __('messages.profile_open_certificate') }}
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if(isset($sections) && isset($progressBySection))
                        <div>
                            <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">
                                {{ __('messages.profile_progress_title') }}
                            </h3>
                            <p class="text-sm text-slate-600 dark:text-slate-400">
                                {{ __('messages.profile_progress_desc') }}
                            </p>
                        </div>

                        <div class="space-y-3 max-h-[420px] overflow-y-auto pr-1">
                            @foreach($sections as $section)
                                @php
                                    $prog = $progressBySection[$section->id] ?? ['percent' => 0, 'completed' => 0, 'total' => 0];
                                    $percent = $prog['percent'];
                                @endphp
                                <a href="{{ route('sections.show', $section) }}"
                                   class="block p-3 rounded-xl border transition-colors {{ ($section->is_featured ?? false) ? 'border-teal-400 dark:border-teal-600 ring-1 ring-teal-200/70 dark:ring-teal-800 bg-gradient-to-r from-teal-50/80 to-white dark:from-teal-950/30 dark:to-slate-800 hover:border-teal-500' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-primary hover:bg-primary-pale dark:hover:bg-slate-700' }}">
                                    @if($section->is_featured ?? false)
                                        <span class="inline-block text-[10px] font-bold uppercase tracking-wide text-teal-700 dark:text-teal-300 mb-1">{{ __('messages.section_featured_badge') }}</span>
                                    @endif
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                                        {{ $section->getTitleForLocale(app()->getLocale()) }}
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                        {{ $prog['completed'] }} / {{ $prog['total'] }} {{ __('messages.dashboard_lessons_done') }}
                                    </p>
                                    <div class="mt-2 h-1.5 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                                        <div class="h-1.5 rounded-full bg-emerald-500" style="width: {{ $percent }}%"></div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        @endif

                        <div class="mt-4 p-4 rounded-xl border border-red-100 bg-red-50/70">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
