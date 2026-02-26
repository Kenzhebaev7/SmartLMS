<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <title>@yield('title', config('app.name', 'SmartLMS'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-slate-900 dark:text-slate-100 antialiased bg-white dark:bg-slate-900 min-h-screen flex flex-col">
    <nav class="flex items-center justify-between px-4 sm:px-8 py-4 bg-white border-b border-slate-200 shadow-sm">
        <a href="{{ url('/') }}" class="flex items-center gap-2">
            <span class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-white font-bold text-lg">S</span>
            <span class="text-xl font-extrabold text-primary tracking-tight italic">SmartLMS</span>
        </a>
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-1 rounded-lg border border-slate-200 p-0.5 bg-slate-50">
                <a href="{{ route('locale.switch', 'kk') }}" class="px-2.5 py-1.5 rounded-md text-xs font-semibold {{ app()->getLocale() === 'kk' ? 'bg-primary text-white' : 'text-slate-600 hover:bg-slate-100' }}">Қаз</a>
                <a href="{{ route('locale.switch', 'ru') }}" class="px-2.5 py-1.5 rounded-md text-xs font-semibold {{ app()->getLocale() === 'ru' ? 'bg-primary text-white' : 'text-slate-600 hover:bg-slate-100' }}">Рус</a>
            </div>
            <a href="{{ url('/') }}" class="text-sm font-semibold text-slate-600 hover:text-primary">{{ __('nav.home') }}</a>
        </div>
    </nav>

    <div class="flex-1 flex items-center justify-center p-4 py-12">
        <div class="w-full sm:max-w-md">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
