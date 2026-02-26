<!DOCTYPE html>
<html lang="kk">
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
    <title>{{ __('welcome.title') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 font-sans antialiased">

<nav class="flex items-center justify-between px-4 sm:px-8 py-4 md:py-6 bg-white border-b border-slate-200 shadow-sm">
    <a href="{{ url('/') }}" class="flex items-center gap-2 shrink-0">
        <span class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-white font-bold text-lg">S</span>
        <span class="text-xl md:text-2xl font-extrabold text-primary tracking-tight italic">SmartLMS</span>
    </a>
    <div class="flex items-center gap-3 md:gap-4">
        <div class="flex items-center gap-1 rounded-lg border border-slate-200 p-0.5 bg-slate-50">
            <a href="{{ route('locale.switch', 'kk') }}" class="px-2.5 py-1.5 rounded-md text-xs font-semibold {{ app()->getLocale() === 'kk' ? 'bg-primary text-white' : 'text-slate-600 hover:bg-slate-100' }}">Қаз</a>
            <a href="{{ route('locale.switch', 'ru') }}" class="px-2.5 py-1.5 rounded-md text-xs font-semibold {{ app()->getLocale() === 'ru' ? 'bg-primary text-white' : 'text-slate-600 hover:bg-slate-100' }}">Рус</a>
        </div>
        @if (Route::has('login'))
            @auth
                <a href="{{ url('/dashboard') }}" class="px-4 py-2.5 text-primary font-semibold border border-primary rounded-xl hover:bg-primary-pale transition-colors text-sm">{{ __('nav.cabinet') }}</a>
            @else
                <a href="{{ route('login') }}" class="px-4 py-2.5 text-slate-600 font-semibold hover:text-primary transition-colors text-sm">{{ __('nav.login') }}</a>
                <a href="{{ route('register') }}" class="px-4 py-2.5 bg-primary text-white rounded-xl font-semibold text-sm shadow-md hover:bg-primary-light transition-colors">{{ __('nav.register') }}</a>
            @endauth
        @endif
    </div>
</nav>

<main class="max-w-6xl mx-auto px-4 sm:px-6 py-12 md:py-20 text-center">
    <div class="inline-block px-4 py-1.5 mb-6 text-xs font-semibold tracking-wide text-accent-dark uppercase bg-accent-pale border border-accent-border rounded-full">
        {{ __('welcome.badge') }}
    </div>

    <h1 class="max-w-4xl mx-auto text-4xl md:text-6xl font-extrabold leading-tight mb-6 md:mb-8">
        {{ __('welcome.heading') }} <br><span class="text-primary">{{ __('welcome.heading_accent') }}</span>
    </h1>

    <p class="max-w-2xl mx-auto text-base md:text-lg text-slate-600 mb-8 md:mb-10 leading-relaxed">
        {{ __('welcome.subheading') }}
    </p>

    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
        <a href="{{ auth()->check() ? url('/dashboard') : route('register') }}" class="w-full sm:w-auto px-8 py-4 bg-primary text-white text-base font-bold rounded-xl shadow-lg shadow-primary/20 hover:bg-primary-light transition-all">
            {{ __('welcome.cta') }}
        </a>
        <span class="flex items-center gap-2 px-6 py-3 bg-white border border-slate-200 rounded-xl text-slate-600 font-medium text-sm">
            <span class="flex h-2.5 w-2.5 rounded-full bg-primary animate-pulse"></span>
            {{ __('welcome.available') }}
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8 mt-20 md:mt-24 text-left">
        <div class="p-6 md:p-8 bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-2xl md:text-3xl mb-4">🎯</div>
            <h3 class="text-lg md:text-xl font-bold text-slate-800 mb-2">{{ __('welcome.adaptive') }}</h3>
            <p class="text-slate-500 text-sm md:text-base">{{ __('welcome.adaptive_desc') }}</p>
        </div>
        <div class="p-6 md:p-8 bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-2xl md:text-3xl mb-4">📝</div>
            <h3 class="text-lg md:text-xl font-bold text-slate-800 mb-2">{{ __('welcome.quizzes') }}</h3>
            <p class="text-slate-500 text-sm md:text-base">{{ __('welcome.quizzes_desc') }}</p>
        </div>
        <div class="p-6 md:p-8 bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-2xl md:text-3xl mb-4">💬</div>
            <h3 class="text-lg md:text-xl font-bold text-slate-800 mb-2">{{ __('welcome.forum_title') }}</h3>
            <p class="text-slate-500 text-sm md:text-base">{{ __('welcome.forum_desc') }}</p>
        </div>
    </div>
</main>

</body>
</html>
