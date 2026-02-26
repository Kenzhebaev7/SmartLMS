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

    {{-- Верхняя навигация с эффектом стекла (backdrop blur) --}}
    <nav class="sticky top-0 z-50 border-b border-slate-200/60 dark:border-slate-700/80 bg-white/75 dark:bg-slate-900/85 backdrop-blur-xl shadow-glass text-slate-800 dark:text-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 md:h-20 items-center">
                <div class="flex items-center gap-6 md:gap-10">
                    <a href="{{ url('/') }}" class="flex items-center gap-3 group shrink-0">
                        @if(file_exists(public_path('images/logo.png')))
                            <img src="{{ asset('images/logo.png') }}" alt="SmartLMS" class="w-10 h-10 md:w-11 md:h-11 rounded-xl object-contain">
                        @else
                            <span class="w-10 h-10 md:w-11 md:h-11 rounded-xl bg-primary flex items-center justify-center text-white font-black text-lg">S</span>
                        @endif
                        <span class="text-xl md:text-[22px] font-extrabold text-primary tracking-tight italic">SmartLMS</span>
                    </a>

                    @auth
                    <div class="hidden md:flex items-center gap-1">
                        <a href="{{ route('dashboard') }}" class="nav-link px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 hover:scale-105 {{ request()->routeIs('dashboard') ? 'bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/80 hover:text-slate-900 dark:hover:text-slate-100' }}">{{ __('nav.cabinet') }}</a>
                        <a href="{{ route('sections.index') }}" class="nav-link px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 hover:scale-105 {{ request()->routeIs('sections.*') ? 'bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/80 hover:text-slate-900 dark:hover:text-slate-100' }}">{{ __('nav.sections') }}</a>
                        <a href="{{ route('forum.index') }}" class="nav-link px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 hover:scale-105 {{ request()->routeIs('forum.*') ? 'bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/80 hover:text-slate-900 dark:hover:text-slate-100' }}">{{ __('nav.forum') }}</a>
                        @if(auth()->user() && (auth()->user()->isTeacher() || auth()->user()->isAdmin()))
                            <a href="{{ route('teacher.dashboard') }}" class="nav-link px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 hover:scale-105 {{ request()->routeIs('teacher.*') ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/80' }}">{{ __('nav.teacher_cabinet') }}</a>
                        @endif
                        @if(auth()->user() && auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="nav-link px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 hover:scale-105 {{ request()->routeIs('admin.*') ? 'bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-100' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/80' }}">{{ __('nav.admin') }}</a>
                        @endif
                    </div>
                    @endauth
                </div>

                <div class="flex items-center gap-2 md:gap-4">
                    <button type="button" onclick="window.smartLmsTheme.toggleTheme()" class="btn-micro p-2 rounded-lg border border-slate-200 dark:border-slate-600 bg-white/60 dark:bg-slate-700/80 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-600 transition-all duration-200 hover:scale-110" title="{{ __('nav.theme_light') }} / {{ __('nav.theme_dark') }}" aria-label="{{ __('nav.theme_dark') }}">
                        <span class="theme-icon-light hidden dark:inline"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg></span>
                        <span class="theme-icon-dark inline dark:hidden"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg></span>
                    </button>
                    <button type="button" onclick="window.smartLmsTheme.toggleA11y()" class="btn-micro p-2 rounded-lg border border-slate-200 dark:border-slate-600 bg-white/60 dark:bg-slate-700/80 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-600 transition-all duration-200 hover:scale-110 a11y-toggle" title="{{ __('nav.a11y') }}" aria-label="{{ __('nav.a11y') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                    <div class="flex items-center gap-1 rounded-lg border border-slate-200 dark:border-slate-600 p-0.5 bg-white dark:bg-slate-700">
                        <a href="{{ route('locale.switch', 'kk') }}" class="px-2.5 py-1.5 rounded-md text-xs font-semibold {{ app()->getLocale() === 'kk' ? 'bg-primary text-white' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-600' }}">Қаз</a>
                        <a href="{{ route('locale.switch', 'ru') }}" class="px-2.5 py-1.5 rounded-md text-xs font-semibold {{ app()->getLocale() === 'ru' ? 'bg-primary text-white' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-600' }}">Рус</a>
                    </div>
                    @auth
                    <div class="hidden sm:flex items-center gap-2">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300 truncate max-w-[120px]">{{ auth()->user()->name }}</span>
                        <span class="px-2.5 py-1 bg-amber-100 dark:bg-amber-900/40 border border-amber-300 dark:border-amber-600 rounded-lg text-xs font-bold text-amber-800 dark:text-amber-200">{{ auth()->user()->xp ?? 0 }} XP</span>
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider {{ (auth()->user()->level ?? '') === 'advanced' ? 'bg-accent-pale dark:bg-accent/20 text-accent-dark dark:text-amber-400 border border-accent-border' : 'bg-primary-100 dark:bg-primary/20 text-primary border border-primary-200 dark:border-primary/40' }}">{{ auth()->user()->level ?? 'beginner' }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn-micro text-sm font-semibold text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 px-2 py-1 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30 transition-all duration-200 hover:scale-105">{{ __('nav.logout') }}</button>
                    </form>
                    @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-primary hover:text-primary-light transition-colors">{{ __('nav.login') }}</a>
                    @if(Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-micro px-4 py-2.5 bg-emerald-500 text-white rounded-xl font-semibold text-sm hover:bg-emerald-600 transition-all duration-200 hover:scale-105 shadow-sky-100">{{ __('nav.register') }}</a>
                    @endif
                    @endauth

                    @auth
                    <button type="button" id="nav-mobile-toggle" class="md:hidden p-2 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700" aria-expanded="false" aria-controls="nav-mobile-menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    @endauth
                </div>
            </div>

            @auth
            <div id="nav-mobile-menu" class="hidden md:hidden pb-4 border-t border-slate-200/80 bg-white dark:bg-slate-900/95">
                <div class="flex flex-col gap-1 pt-3">
                    <a href="{{ route('dashboard') }}" class="px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('dashboard') ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:bg-slate-50' }}">{{ __('nav.cabinet') }}</a>
                    <a href="{{ route('sections.index') }}" class="px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('sections.*') ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:bg-slate-50' }}">{{ __('nav.sections') }}</a>
                    <a href="{{ route('forum.index') }}" class="px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('forum.*') ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:bg-slate-50' }}">{{ __('nav.forum') }}</a>
                    @if(auth()->user() && (auth()->user()->isTeacher() || auth()->user()->isAdmin()))
                        <a href="{{ route('teacher.dashboard') }}" class="px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('teacher.*') ? 'bg-amber-50 text-amber-800' : 'text-amber-700 hover:bg-amber-50/70' }}">{{ __('nav.teacher_cabinet') }}</a>
                    @endif
                    @if(auth()->user() && auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.*') ? 'bg-slate-100 text-slate-800' : 'text-slate-600 hover:bg-slate-50' }}">{{ __('nav.admin') }}</a>
                    @endif
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
