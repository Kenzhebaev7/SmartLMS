<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
        (function(){
            var t = localStorage.getItem('smartlms_theme') || 'light';
            var a = localStorage.getItem('smartlms_a11y') === '1';
            var root = document.documentElement;
            root.classList.remove('light', 'dark', 'a11y');
            root.classList.add(t);
            if(a) root.classList.add('a11y');
        })();
    </script>
    <title>{{ __('messages.welcome_title') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-[#f6fbff] text-slate-900 dark:bg-slate-950 dark:text-slate-100 font-sans antialiased">
@php
    $primaryActionUrl = auth()->check() ? url('/dashboard') : route('register');
    $secondaryActionUrl = auth()->check() ? route('forum.index') : route('login');
@endphp

<div class="relative overflow-hidden">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(14,165,233,0.26),_transparent_34%),radial-gradient(circle_at_top_right,_rgba(16,185,129,0.24),_transparent_30%),radial-gradient(circle_at_65%_40%,_rgba(245,158,11,0.18),_transparent_28%)] pointer-events-none"></div>
    <div class="absolute -top-20 right-[-8rem] h-72 w-72 rounded-full bg-sky-300/30 blur-3xl pointer-events-none"></div>
    <div class="absolute top-[26rem] left-[-6rem] h-64 w-64 rounded-full bg-emerald-300/20 blur-3xl pointer-events-none"></div>

    <header class="relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-5">
            <nav class="flex items-center justify-between gap-4 rounded-[30px] border border-white/70 bg-white/80 px-4 py-3 shadow-[0_18px_60px_-30px_rgba(15,23,42,0.28)] backdrop-blur-xl dark:border-slate-800 dark:bg-slate-900/80">
                <a href="{{ url('/') }}" class="flex items-center gap-3 min-w-0">
                    @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ asset('images/logo.png') }}" alt="SmartLMS" class="w-11 h-11 rounded-2xl object-contain shadow-sm">
                    @else
                        <span class="w-11 h-11 rounded-2xl bg-gradient-to-br from-sky-500 to-emerald-500 flex items-center justify-center text-white font-black text-lg shadow-sm">S</span>
                    @endif
                    <div class="min-w-0">
                        <span class="block text-lg sm:text-xl font-black tracking-tight text-slate-950 dark:text-white">SmartLMS</span>
                        <span class="hidden sm:block text-xs text-slate-500 dark:text-slate-400">{{ __('messages.welcome_nav_caption') }}</span>
                    </div>
                </a>

                <div class="hidden xl:flex items-center gap-2">
                    <span class="rounded-full bg-sky-100 px-3 py-1.5 text-xs font-bold text-sky-800">{{ __('messages.auth_grade_9') }}</span>
                    <span class="rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-bold text-emerald-800">{{ __('messages.auth_grade_10') }}</span>
                    <span class="rounded-full bg-amber-100 px-3 py-1.5 text-xs font-bold text-amber-800">{{ __('messages.auth_grade_11') }}</span>
                </div>

                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="flex items-center gap-1 rounded-xl border border-slate-200 bg-white/90 p-0.5 dark:border-slate-700 dark:bg-slate-800">
                        <a href="{{ route('locale.switch', 'kk') }}" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold {{ app()->getLocale() === 'kk' ? 'bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700' }}">Қаз</a>
                        <a href="{{ route('locale.switch', 'ru') }}" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold {{ app()->getLocale() === 'ru' ? 'bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700' }}">Рус</a>
                    </div>

                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white">
                                {{ __('messages.nav_cabinet') }}
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center px-4 py-2.5 text-sm font-semibold text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">
                                {{ __('messages.nav_login') }}
                            </a>
                            <a href="{{ route('register') }}" class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white">
                                {{ __('messages.nav_register') }}
                            </a>
                        @endauth
                    @endif
                </div>
            </nav>
        </div>
    </header>

    <main class="relative z-10">
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-12 sm:pt-14 sm:pb-16">
            <div class="grid gap-8 xl:grid-cols-[1.08fr_0.92fr] xl:items-center">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-white/90 px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-slate-700 shadow-sm ring-1 ring-slate-200 dark:bg-slate-900/90 dark:text-slate-200 dark:ring-slate-800">
                        <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        {{ __('messages.welcome_badge') }}
                    </div>

                    <h1 class="mt-6 max-w-4xl text-5xl sm:text-6xl xl:text-7xl font-black tracking-tight leading-[0.96] text-slate-950 dark:text-white">
                        {{ __('messages.welcome_heading') }}
                        <span class="block bg-gradient-to-r from-sky-500 via-cyan-500 to-emerald-500 bg-clip-text text-transparent">{{ __('messages.welcome_heading_accent') }}</span>
                    </h1>

                    <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600 dark:text-slate-300">
                        {{ __('messages.welcome_subheading') }}
                    </p>

                    <div class="mt-8 flex flex-col sm:flex-row gap-4">
                        <a href="{{ $primaryActionUrl }}" class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-sky-500 to-emerald-500 px-7 py-4 text-base font-bold text-white shadow-[0_20px_40px_-20px_rgba(14,165,233,0.75)] transition hover:translate-y-[-1px] hover:shadow-[0_24px_50px_-18px_rgba(16,185,129,0.75)]">
                            {{ __('messages.welcome_cta') }}
                        </a>
                        <a href="{{ $secondaryActionUrl }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white/90 px-7 py-4 text-base font-semibold text-slate-700 transition hover:bg-white dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-800">
                            {{ auth()->check() ? __('messages.nav_forum') : __('messages.nav_login') }}
                        </a>
                    </div>

                    <div class="mt-10 grid gap-4 sm:grid-cols-3">
                        <div class="rounded-[26px] bg-white/92 p-5 shadow-sm ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.welcome_stat_levels') }}</p>
                            <p class="mt-3 text-3xl font-black text-slate-950 dark:text-white">2</p>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ __('messages.welcome_stat_levels_desc') }}</p>
                        </div>
                        <div class="rounded-[26px] bg-white/92 p-5 shadow-sm ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.welcome_stat_grades') }}</p>
                            <p class="mt-3 text-3xl font-black text-slate-950 dark:text-white">9-11</p>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ __('messages.welcome_stat_grades_desc') }}</p>
                        </div>
                        <div class="rounded-[26px] bg-white/92 p-5 shadow-sm ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.welcome_stat_path') }}</p>
                            <p class="mt-3 text-3xl font-black text-slate-950 dark:text-white">1</p>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ __('messages.welcome_stat_path_desc') }}</p>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4">
                    <div class="rounded-[34px] bg-slate-950 p-6 text-white shadow-[0_30px_80px_-35px_rgba(15,23,42,0.8)] dark:bg-slate-900">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.18em] text-sky-200">{{ __('messages.welcome_demo_label') }}</p>
                                <h2 class="mt-3 text-3xl font-black leading-tight">{{ __('messages.welcome_demo_title') }}</h2>
                            </div>
                            <span class="rounded-full bg-white/10 px-3 py-1.5 text-xs font-bold">{{ __('messages.welcome_available') }}</span>
                        </div>

                        <div class="mt-6 space-y-4">
                            <div class="rounded-3xl bg-white/8 p-4 ring-1 ring-white/10">
                                <div class="flex items-start gap-4">
                                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-sky-500 text-sm font-black">01</span>
                                    <div>
                                        <p class="font-bold">{{ __('messages.welcome_demo_test') }}</p>
                                        <p class="mt-1 text-sm leading-6 text-slate-300">{{ __('messages.welcome_demo_test_desc') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="rounded-3xl bg-white/8 p-4 ring-1 ring-white/10">
                                <div class="flex items-start gap-4">
                                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-emerald-500 text-sm font-black">02</span>
                                    <div>
                                        <p class="font-bold">{{ __('messages.welcome_demo_route') }}</p>
                                        <p class="mt-1 text-sm leading-6 text-slate-300">{{ __('messages.welcome_demo_route_desc') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="rounded-3xl bg-white/8 p-4 ring-1 ring-white/10">
                                <div class="flex items-start gap-4">
                                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-amber-500 text-sm font-black text-slate-950">03</span>
                                    <div>
                                        <p class="font-bold">{{ __('messages.welcome_demo_progress') }}</p>
                                        <p class="mt-1 text-sm leading-6 text-slate-300">{{ __('messages.welcome_demo_progress_desc') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-[28px] bg-gradient-to-br from-sky-500 to-cyan-500 p-5 text-white shadow-[0_18px_50px_-26px_rgba(14,165,233,0.6)]">
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-sky-100">{{ __('messages.welcome_students_title') }}</p>
                            <h3 class="mt-3 text-xl font-black">{{ __('messages.welcome_students_panel_title') }}</h3>
                            <p class="mt-2 text-sm leading-6 text-sky-50/90">{{ __('messages.welcome_students_desc') }}</p>
                        </div>
                        <div class="rounded-[28px] bg-gradient-to-br from-emerald-500 to-teal-500 p-5 text-white shadow-[0_18px_50px_-26px_rgba(16,185,129,0.65)]">
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-emerald-100">{{ __('messages.welcome_teachers_title') }}</p>
                            <h3 class="mt-3 text-xl font-black">{{ __('messages.welcome_teachers_panel_title') }}</h3>
                            <p class="mt-2 text-sm leading-6 text-emerald-50/90">{{ __('messages.welcome_teachers_desc') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
            <div class="grid gap-5 lg:grid-cols-3">
                <div class="rounded-[30px] bg-white p-6 shadow-[0_18px_45px_-30px_rgba(15,23,42,0.25)] ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
                    <div class="w-14 h-14 rounded-3xl bg-sky-100 text-sky-700 flex items-center justify-center dark:bg-sky-950/50 dark:text-sky-200">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v12m6-6H6"/></svg>
                    </div>
                    <h3 class="mt-5 text-2xl font-black text-slate-950 dark:text-white">{{ __('messages.welcome_adaptive') }}</h3>
                    <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-300">{{ __('messages.welcome_adaptive_desc') }}</p>
                </div>
                <div class="rounded-[30px] bg-white p-6 shadow-[0_18px_45px_-30px_rgba(15,23,42,0.25)] ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
                    <div class="w-14 h-14 rounded-3xl bg-emerald-100 text-emerald-700 flex items-center justify-center dark:bg-emerald-950/50 dark:text-emerald-200">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m9 12 2 2 4-4m5 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    </div>
                    <h3 class="mt-5 text-2xl font-black text-slate-950 dark:text-white">{{ __('messages.welcome_quizzes') }}</h3>
                    <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-300">{{ __('messages.welcome_quizzes_desc') }}</p>
                </div>
                <div class="rounded-[30px] bg-white p-6 shadow-[0_18px_45px_-30px_rgba(15,23,42,0.25)] ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
                    <div class="w-14 h-14 rounded-3xl bg-amber-100 text-amber-700 flex items-center justify-center dark:bg-amber-950/50 dark:text-amber-200">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h8M8 14h5m-7 6h12a2 2 0 0 0 2-2V8.828a2 2 0 0 0-.586-1.414l-4.828-4.828A2 2 0 0 0 13.172 2H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2Z"/></svg>
                    </div>
                    <h3 class="mt-5 text-2xl font-black text-slate-950 dark:text-white">{{ __('messages.welcome_forum_title') }}</h3>
                    <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-300">{{ __('messages.welcome_forum_desc') }}</p>
                </div>
            </div>
        </section>

        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="rounded-[36px] bg-gradient-to-r from-slate-950 via-sky-950 to-emerald-950 p-6 sm:p-8 text-white shadow-[0_28px_80px_-40px_rgba(15,23,42,0.85)]">
                <div class="max-w-2xl">
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-sky-200">{{ __('messages.welcome_steps_label') }}</p>
                    <h2 class="mt-3 text-3xl sm:text-4xl font-black leading-tight">{{ __('messages.welcome_steps_title') }}</h2>
                    <p class="mt-3 text-base leading-7 text-slate-200">{{ __('messages.welcome_steps_desc') }}</p>
                </div>

                <div class="mt-8 grid gap-4 lg:grid-cols-3">
                    <div class="rounded-[28px] bg-white/8 p-5 ring-1 ring-white/10 backdrop-blur-sm">
                        <span class="inline-flex items-center rounded-full bg-sky-500 px-3 py-1 text-xs font-black">01</span>
                        <h3 class="mt-4 text-xl font-black">{{ __('messages.welcome_step_1_title') }}</h3>
                        <p class="mt-2 text-sm leading-7 text-slate-200">{{ __('messages.welcome_step_1_desc') }}</p>
                    </div>
                    <div class="rounded-[28px] bg-white/8 p-5 ring-1 ring-white/10 backdrop-blur-sm">
                        <span class="inline-flex items-center rounded-full bg-emerald-500 px-3 py-1 text-xs font-black">02</span>
                        <h3 class="mt-4 text-xl font-black">{{ __('messages.welcome_step_2_title') }}</h3>
                        <p class="mt-2 text-sm leading-7 text-slate-200">{{ __('messages.welcome_step_2_desc') }}</p>
                    </div>
                    <div class="rounded-[28px] bg-white/8 p-5 ring-1 ring-white/10 backdrop-blur-sm">
                        <span class="inline-flex items-center rounded-full bg-amber-400 px-3 py-1 text-xs font-black text-slate-950">03</span>
                        <h3 class="mt-4 text-xl font-black">{{ __('messages.welcome_step_3_title') }}</h3>
                        <p class="mt-2 text-sm leading-7 text-slate-200">{{ __('messages.welcome_step_3_desc') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-16">
            <div class="grid gap-5 xl:grid-cols-2">
                <div class="rounded-[34px] bg-white p-6 shadow-[0_18px_45px_-30px_rgba(15,23,42,0.25)] ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.welcome_students_title') }}</p>
                            <h3 class="mt-3 text-3xl font-black text-slate-950 dark:text-white">{{ __('messages.welcome_students_panel_title') }}</h3>
                        </div>
                        <span class="rounded-2xl bg-sky-100 px-4 py-3 text-sm font-black text-sky-800 dark:bg-sky-950/50 dark:text-sky-200">Student</span>
                    </div>
                    <p class="mt-4 text-sm leading-7 text-slate-600 dark:text-slate-300">{{ __('messages.welcome_students_panel_desc') }}</p>
                    <div class="mt-6 space-y-3">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950">
                            <p class="font-bold text-slate-950 dark:text-white">{{ __('messages.welcome_students_item_1_title') }}</p>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('messages.welcome_students_item_1_desc') }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950">
                            <p class="font-bold text-slate-950 dark:text-white">{{ __('messages.welcome_students_item_2_title') }}</p>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('messages.welcome_students_item_2_desc') }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950">
                            <p class="font-bold text-slate-950 dark:text-white">{{ __('messages.welcome_students_item_3_title') }}</p>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('messages.welcome_students_item_3_desc') }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-[34px] bg-white p-6 shadow-[0_18px_45px_-30px_rgba(15,23,42,0.25)] ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.welcome_teachers_title') }}</p>
                            <h3 class="mt-3 text-3xl font-black text-slate-950 dark:text-white">{{ __('messages.welcome_teachers_panel_title') }}</h3>
                        </div>
                        <span class="rounded-2xl bg-emerald-100 px-4 py-3 text-sm font-black text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-200">Teacher</span>
                    </div>
                    <p class="mt-4 text-sm leading-7 text-slate-600 dark:text-slate-300">{{ __('messages.welcome_teachers_panel_desc') }}</p>
                    <div class="mt-6 space-y-3">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950">
                            <p class="font-bold text-slate-950 dark:text-white">{{ __('messages.welcome_teachers_item_1_title') }}</p>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('messages.welcome_teachers_item_1_desc') }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950">
                            <p class="font-bold text-slate-950 dark:text-white">{{ __('messages.welcome_teachers_item_2_title') }}</p>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('messages.welcome_teachers_item_2_desc') }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950">
                            <p class="font-bold text-slate-950 dark:text-white">{{ __('messages.welcome_teachers_item_3_title') }}</p>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('messages.welcome_teachers_item_3_desc') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>
</body>
</html>
