<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        (function(){
            var THEME_KEY = 'smartlms_theme', A11Y_KEY = 'smartlms_a11y';
            var t = localStorage.getItem(THEME_KEY) || 'light';
            var a = localStorage.getItem(A11Y_KEY) === '1';
            var root = document.documentElement;
            root.classList.remove('light', 'dark', 'a11y');
            root.classList.add(t);
            if(a) root.classList.add('a11y');

            function applyTheme() {
                var theme = localStorage.getItem(THEME_KEY) || 'light';
                var a11y = localStorage.getItem(A11Y_KEY) === '1';
                root.classList.remove('light', 'dark', 'a11y');
                root.classList.add(theme);
                if(a11y) root.classList.add('a11y');
            }
            window.smartLmsTheme = {
                toggleTheme: function() {
                    var theme = localStorage.getItem(THEME_KEY) || 'light';
                    localStorage.setItem(THEME_KEY, theme === 'dark' ? 'light' : 'dark');
                    applyTheme();
                },
                toggleA11y: function() {
                    var on = localStorage.getItem(A11Y_KEY) === '1';
                    localStorage.setItem(A11Y_KEY, on ? '0' : '1');
                    applyTheme();
                }
            };
        })();
    </script>

    <title>@yield('title', config('app.name', 'SmartLMS'))</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-white dark:bg-slate-900 antialiased text-slate-900 dark:text-slate-100 font-sans min-h-screen flex flex-col">
<div class="min-h-screen flex flex-col bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 flex-1">

    <nav class="sticky top-0 z-50 border-b border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between gap-3 h-16">
                <div class="flex items-center gap-3 min-w-0">
                    <a href="{{ url('/') }}" class="flex items-center gap-3 shrink-0">
                        @if(file_exists(public_path('images/logo.png')))
                            <img src="{{ asset('images/logo.png') }}" alt="SmartLMS" class="w-10 h-10 rounded-2xl object-contain shadow-sm">
                        @else
                            <span class="w-10 h-10 rounded-2xl bg-primary flex items-center justify-center text-white font-black text-lg shadow-sm">S</span>
                        @endif
                        <div class="min-w-0">
                            <span class="block text-lg md:text-xl font-bold text-primary tracking-tight leading-none">SmartLMS</span>
                            @auth
                                @if(auth()->user()->isStudent())
                                    <p class="hidden md:block text-xs text-slate-500 mt-1 text-fit">
                                        {{ $navPanel['gradeLabel'] ?? '' }}
                                        @if(!empty($navPanel['levelLabel'])) / {{ $navPanel['levelLabel'] }} @endif
                                    </p>
                                @elseif(auth()->user()->isTeacher())
                                    <p class="hidden md:block text-xs text-slate-500 mt-1 text-fit">{{ __('messages.nav_teacher_home') }}</p>
                                @elseif(auth()->user()->isAdmin())
                                    <p class="hidden md:block text-xs text-slate-500 mt-1 text-fit">{{ __('messages.nav_admin_home') }}</p>
                                @endif
                            @endauth
                        </div>
                    </a>
                </div>

                <div class="flex items-center gap-2 md:gap-2.5 shrink-0">
                    @auth
                        <form action="{{ route('search.index') }}" method="GET" class="hidden xl:flex items-center relative">
                            <svg class="absolute left-3 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M16 10.5A5.5 5.5 0 1 1 5 10.5a5.5 5.5 0 0 1 11 0Z"/></svg>
                            <input
                                type="text"
                                name="q"
                                value="{{ $navPanel['searchQuery'] ?? request('q') }}"
                                placeholder="{{ __('messages.nav_search_placeholder') }}"
                                class="w-52 rounded-lg border border-slate-200 bg-white pl-9 pr-3 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-primary focus:ring-primary/20"
                            >
                        </form>
                    @endauth
                    <button type="button" onclick="window.smartLmsTheme.toggleTheme()" class="p-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors" title="{{ __('messages.nav_theme_light') }} / {{ __('messages.nav_theme_dark') }}" aria-label="{{ __('messages.nav_theme_dark') }}">
                        <span class="theme-icon-light hidden dark:inline"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg></span>
                        <span class="theme-icon-dark inline dark:hidden"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg></span>
                    </button>
                    <button type="button" onclick="window.smartLmsTheme.toggleA11y()" class="p-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors a11y-toggle" title="{{ __('messages.nav_a11y') }}" aria-label="{{ __('messages.nav_a11y') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                    <div class="flex items-center gap-1 rounded-lg border border-slate-200 dark:border-slate-700 px-1 py-0.5 bg-white dark:bg-slate-800">
                        <a href="{{ route('lang.switch', 'kk') }}" class="px-2.5 py-1 rounded-md text-xs font-semibold {{ app()->getLocale() === 'kk' ? 'bg-slate-100 dark:bg-slate-700 text-slate-900 dark:text-white' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700' }}">KZ</a>
                        <a href="{{ route('lang.switch', 'ru') }}" class="px-2.5 py-1 rounded-md text-xs font-semibold {{ app()->getLocale() === 'ru' ? 'bg-slate-100 dark:bg-slate-700 text-slate-900 dark:text-white' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700' }}">RU</a>
                    </div>
                    @auth
                    <a href="{{ $navPanel['notificationsRoute'] ?? route('dashboard') }}" class="relative hidden sm:inline-flex items-center justify-center w-10 h-10 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors" title="{{ __('messages.nav_notifications') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2c0 .53-.21 1.04-.59 1.41L4 17h5m6 0a3 3 0 1 1-6 0m6 0H9"/></svg>
                        @if(($navPanel['notificationsCount'] ?? 0) > 0)
                            <span class="absolute -top-1 -right-1 min-w-5 h-5 px-1 rounded-full bg-rose-500 text-white text-[10px] font-bold flex items-center justify-center">{{ min(($navPanel['notificationsCount'] ?? 0), 99) }}</span>
                        @endif
                    </a>

                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('teacher.dashboard') }}" class="hidden lg:inline-flex items-center gap-2 px-3 py-2 rounded-full bg-amber-100 text-amber-900 font-semibold border border-amber-200 hover:bg-amber-200 transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14 3 9l9-5 9 5-9 5Zm0 0v6m0-6 7.5-4.167M12 14 4.5 9.833"/></svg>
                            {{ __('messages.nav_teacher_mode') }}
                        </a>
                    @endif

                    <div class="hidden sm:flex items-center">
                        <x-dropdown align="right" width="56">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center gap-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-2.5 py-1.5 text-sm font-medium text-slate-800 dark:text-slate-100 max-w-[220px]">
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-100 flex items-center justify-center text-xs font-bold">
                                        {{ mb_substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                    <span class="truncate">{{ auth()->user()->name }}</span>
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="px-4 py-3 border-b border-slate-100">
                                    <p class="text-sm font-semibold text-slate-900 dark:text-slate-50 truncate">
                                        {{ auth()->user()->name }}
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-300 truncate">
                                        {{ auth()->user()->email }}
                                    </p>
                                    @if(auth()->user()->role === 'student' && auth()->user()->grade)
                                        @php $g = (int) auth()->user()->grade; @endphp
                                        <p class="mt-2 text-xs font-medium text-slate-500 dark:text-slate-300">
                                            {{ __('messages.auth_grade_' . $g) }}
                                        </p>
                                    @endif
                                </div>

                                <x-dropdown-link :href="route('profile.edit')" class="text-sm font-medium text-slate-700 dark:text-slate-200">
                                    {{ __('messages.nav_profile') }}
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('certificates.index')" class="text-sm font-medium text-slate-700 dark:text-slate-200">
                                    {{ __('messages.nav_certificates') }}
                                </x-dropdown-link>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                                     class="text-sm font-semibold text-red-600 dark:text-red-400"
                                                     onclick="event.preventDefault(); this.closest('form').submit();">
                                        {{ __('messages.nav_logout') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                    @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-primary hover:text-primary-light transition-colors">{{ __('messages.nav_login') }}</a>
                    @if(Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-micro px-4 py-2.5 bg-emerald-500 text-white rounded-xl font-semibold text-sm hover:bg-emerald-600 transition-all duration-200 hover:scale-105 shadow-sky-100">{{ __('messages.nav_register') }}</a>
                    @endif
                    @endauth

                    @auth
                    <button type="button" id="nav-mobile-toggle" class="lg:hidden p-2 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700" aria-expanded="false" aria-controls="nav-mobile-menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    @endauth
                </div>
            </div>

            @auth
            <div class="hidden lg:flex items-center justify-between gap-4 border-t border-slate-100 dark:border-slate-800 py-3">
                <div class="flex items-center gap-2 min-w-0">
                    @if(auth()->user()->isStudent())
                        <span class="text-sm text-slate-500 truncate">
                            {{ $navPanel['gradeLabel'] ?? '' }}
                            @if(!empty($navPanel['levelLabel'])) / {{ $navPanel['levelLabel'] }} @endif
                            @if(!empty($navPanel['progressLabel'])) / {{ $navPanel['progressLabel'] }} @endif
                        </span>
                    @elseif(auth()->user()->isTeacher())
                        <span class="text-sm text-slate-500 truncate">{{ $navPanel['progressLabel'] ?? __('messages.teacher_progress') }}</span>
                    @elseif(auth()->user()->isAdmin())
                        <span class="text-sm text-slate-500 truncate">{{ $navPanel['progressLabel'] ?? __('messages.nav_admin_home') }}</span>
                    @endif
                </div>

                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar min-w-0">
                    @if(auth()->user()->isStudent())
                        <a href="{{ route('dashboard') }}" class="nav-main-link {{ request()->routeIs('dashboard') ? 'nav-main-link-active' : '' }}">{{ __('messages.nav_home') }}</a>
                        <a href="{{ route('sections.index') }}" class="nav-main-link {{ request()->routeIs('sections.*') ? 'nav-main-link-active' : '' }}">{{ __('messages.nav_sections') }}</a>
                        <a href="{{ route('certificates.index') }}" class="nav-main-link {{ request()->routeIs('certificates.*') ? 'nav-main-link-active-emerald' : '' }}">{{ __('messages.nav_certificates') }}</a>
                        <a href="{{ route('forum.index') }}" class="nav-main-link {{ request()->routeIs('forum.*') ? 'nav-main-link-active' : '' }}">{{ __('messages.nav_forum') }}</a>
                    @elseif(auth()->user()->isTeacher())
                        <a href="{{ route('teacher.dashboard') }}" class="nav-main-link {{ request()->routeIs('teacher.dashboard') ? 'nav-main-link-active-amber' : '' }}">{{ __('messages.nav_teacher_home') }}</a>
                        <a href="{{ route('teacher.sections.index') }}" class="nav-main-link {{ request()->routeIs('teacher.sections.*') || request()->routeIs('teacher.lessons.*') ? 'nav-main-link-active' : '' }}">{{ __('messages.teacher_sections_lessons') }}</a>
                        <a href="{{ route('teacher.progress.index') }}" class="nav-main-link {{ request()->routeIs('teacher.progress.*') ? 'nav-main-link-active' : '' }}">{{ __('messages.teacher_progress') }}</a>
                        <a href="{{ route('teacher.certificates.index') }}" class="nav-main-link {{ request()->routeIs('teacher.certificates.*') ? 'nav-main-link-active-emerald' : '' }}">{{ __('messages.teacher_certificates_title') }}</a>
                        <a href="{{ route('forum.index') }}" class="nav-main-link {{ request()->routeIs('forum.*') ? 'nav-main-link-active' : '' }}">{{ __('messages.nav_forum') }}</a>
                    @elseif(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="nav-main-link {{ request()->routeIs('admin.dashboard') ? 'nav-main-link-active-slate' : '' }}">{{ __('messages.nav_admin_home') }}</a>
                        <a href="{{ route('admin.users.index') }}" class="nav-main-link {{ request()->routeIs('admin.users.*') ? 'nav-main-link-active' : '' }}">{{ __('messages.admin_manage_users') }}</a>
                        <a href="{{ route('teacher.sections.index') }}" class="nav-main-link {{ request()->routeIs('teacher.sections.*') || request()->routeIs('teacher.lessons.*') || request()->routeIs('teacher.dashboard') ? 'nav-main-link-active' : '' }}">{{ __('messages.admin_manage_content') }}</a>
                        <a href="{{ route('teacher.certificates.index') }}" class="nav-main-link {{ request()->routeIs('teacher.certificates.*') ? 'nav-main-link-active-emerald' : '' }}">{{ __('messages.teacher_certificates_title') }}</a>
                        <a href="{{ route('admin.settings.index') }}" class="nav-main-link {{ request()->routeIs('admin.settings.*') ? 'nav-main-link-active-slate' : '' }}">{{ __('messages.admin_settings') }}</a>
                    @endif
                </div>
            </div>

            <div id="nav-mobile-menu" class="hidden lg:hidden pb-4">
                @if(isset($navPanel))
                    <div class="pt-3">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-left">
                            <div class="nav-status-card-mobile">
                                <span class="nav-status-label">{{ __('messages.dashboard_grade') }}</span>
                                <span class="nav-status-value">{{ $navPanel['gradeLabel'] ?? '-' }}</span>
                            </div>
                            <div class="nav-status-card-mobile">
                                <span class="nav-status-label">{{ __('messages.dashboard_level') }}</span>
                                <span class="nav-status-value">{{ $navPanel['levelLabel'] ?? '-' }}</span>
                            </div>
                            <div class="nav-status-card-mobile">
                                <span class="nav-status-label">{{ __('messages.nav_progress') }}</span>
                                <span class="nav-status-value">{{ $navPanel['progressLabel'] ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mt-3 rounded-2xl border border-slate-200 bg-white/95 px-3 py-3 shadow-sm">
                    <div class="flex flex-col gap-1">
                        @if(auth()->user()->isStudent())
                            <a href="{{ route('dashboard') }}" class="px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('dashboard') ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:bg-slate-50' }}">{{ __('messages.nav_home') }}</a>
                            <a href="{{ route('sections.index') }}" class="px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('sections.*') ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:bg-slate-50' }}">{{ __('messages.nav_sections') }}</a>
                            <a href="{{ route('certificates.index') }}" class="px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('certificates.*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50' }}">{{ __('messages.nav_certificates') }}</a>
                            <a href="{{ route('forum.index') }}" class="px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('forum.*') ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:bg-slate-50' }}">{{ __('messages.nav_forum') }}</a>
                        @elseif(auth()->user()->isTeacher())
                            <a href="{{ route('teacher.dashboard') }}" class="px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('teacher.dashboard') ? 'bg-amber-50 text-amber-800' : 'text-slate-600 hover:bg-slate-50' }}">{{ __('messages.nav_teacher_home') }}</a>
                            <a href="{{ route('teacher.sections.index') }}" class="px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('teacher.sections.*') || request()->routeIs('teacher.lessons.*') ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:bg-slate-50' }}">{{ __('messages.teacher_sections_lessons') }}</a>
                            <a href="{{ route('teacher.progress.index') }}" class="px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('teacher.progress.*') ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:bg-slate-50' }}">{{ __('messages.teacher_progress') }}</a>
                            <a href="{{ route('teacher.certificates.index') }}" class="px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('teacher.certificates.*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50' }}">{{ __('messages.teacher_certificates_title') }}</a>
                            <a href="{{ route('forum.index') }}" class="px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('forum.*') ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:bg-slate-50' }}">{{ __('messages.nav_forum') }}</a>
                        @elseif(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.dashboard') ? 'bg-slate-100 text-slate-800' : 'text-slate-600 hover:bg-slate-50' }}">{{ __('messages.nav_admin_home') }}</a>
                            <a href="{{ route('admin.users.index') }}" class="px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.users.*') ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:bg-slate-50' }}">{{ __('messages.admin_manage_users') }}</a>
                            <a href="{{ route('teacher.sections.index') }}" class="px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('teacher.sections.*') || request()->routeIs('teacher.lessons.*') || request()->routeIs('teacher.dashboard') ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:bg-slate-50' }}">{{ __('messages.admin_manage_content') }}</a>
                            <a href="{{ route('teacher.certificates.index') }}" class="px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('teacher.certificates.*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50' }}">{{ __('messages.teacher_certificates_title') }}</a>
                            <a href="{{ route('admin.settings.index') }}" class="px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.settings.*') ? 'bg-slate-100 text-slate-800' : 'text-slate-600 hover:bg-slate-50' }}">{{ __('messages.admin_settings') }}</a>
                        @endif
                        <a href="{{ route('search.index') }}" class="px-4 py-3 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50">{{ __('messages.nav_search') }}</a>
                        <a href="{{ route('profile.edit') }}" class="px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('profile.*') ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:bg-slate-50' }}">{{ __('messages.nav_profile') }}</a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('teacher.dashboard') }}" class="px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('teacher.*') ? 'bg-amber-50 text-amber-800' : 'text-amber-700 hover:bg-amber-50/70' }}">{{ __('messages.nav_teacher_mode') }}</a>
                        @endif
                    </div>
                </div>
            </div>
            @endauth
        </div>
    </nav>

    @isset($header)
        <header class="bg-white dark:bg-slate-800 border-b border-slate-100 dark:border-slate-600 shadow-sm">
            <div class="max-w-7xl mx-auto py-6 md:py-8 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-slate-100 tracking-tight" style="color: inherit;">
                        {{ $header }}
                    </h1>
                    @isset($actions)
                        <div class="flex gap-3">
                            {{ $actions }}
                        </div>
                    @endisset
                </div>
            </div>
        </header>
    @endisset

    {{-- Р“Р»РѕР±Р°Р»СЊРЅС‹Рµ СѓРІРµРґРѕРјР»РµРЅРёСЏ: СѓСЃРїРµС… Рё РѕС€РёР±РєР° - РІРёРґРЅС‹ РЅР° РІСЃРµС… СЃС‚СЂР°РЅРёС†Р°С…, РјРѕР¶РЅРѕ Р·Р°РєСЂС‹С‚СЊ --}}
    @if(session('status') || session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4" x-data="{ open: true }" x-show="open" x-transition>
            @if(session('status'))
                <div class="rounded-xl border px-4 py-3 flex items-center justify-between gap-4 bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 shadow-sm">
                    <p class="text-sm font-medium">{{ session('status') }}</p>
                    <button type="button" @click="open = false" class="shrink-0 p-1 rounded-lg hover:bg-emerald-200/50 dark:hover:bg-emerald-800/50 transition-colors" aria-label="{{ __('messages.nav_close') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif
            @if(session('error'))
                <div class="rounded-xl border px-4 py-3 flex items-center justify-between gap-4 mt-2 bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 shadow-sm">
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                    <button type="button" @click="open = false" class="shrink-0 p-1 rounded-lg hover:bg-red-200/50 dark:hover:bg-red-800/50 transition-colors" aria-label="{{ __('messages.nav_close') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif
        </div>
    @endif

    <main class="py-12 flex-1 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-slate-800 dark:text-slate-100">
            {{ $slot ?? '' }}
        </div>
    </main>

</div>

<script>
        document.getElementById('nav-mobile-toggle')?.addEventListener('click', function() {
            var menu = document.getElementById('nav-mobile-menu');
            if (menu) {
                menu.classList.toggle('hidden');
                this.setAttribute('aria-expanded', menu.classList.contains('hidden') ? 'false' : 'true');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>



