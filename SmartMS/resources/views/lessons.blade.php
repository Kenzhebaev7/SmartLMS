<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold leading-tight text-teal-900">
                {{ __('Библиотека знаний') }}
            </h2>
            <div class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-100 shadow-sm rounded-2xl">
                <span class="text-sm font-medium italic text-gray-500">Ваш уровень:</span>
                <span class="text-sm font-bold tracking-wider text-teal-600 uppercase">
                    {{ auth()->user()->level ?? 'beginner' }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen py-12 bg-slate-50">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-12">

            <section>
                <div class="flex items-center gap-3 mb-6">
                    <div class="flex items-center justify-center w-10 h-10 text-teal-600 bg-teal-100 shadow-sm rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-extrabold tracking-tight text-slate-800">{{ __('Начальный уровень') }}</h3>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    @php $isDone1 = !empty($completedLessons) && in_array('basics-html-css', $completedLessons, true); @endphp
                    <div class="flex flex-col justify-between p-6 transition-all bg-white border shadow-sm rounded-3xl group hover:shadow-md {{ $isDone1 ? 'border-teal-100' : 'border-gray-100' }}">
                        <div>
                            <div class="flex items-start justify-between mb-4">
                                <span class="px-3 py-1 text-xs font-bold tracking-widest text-teal-500 uppercase bg-teal-50 rounded-lg">Web Basics</span>
                                @if($isDone1)
                                    <span class="p-1.5 text-teal-600 bg-teal-50 rounded-full">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                    </span>
                                @endif
                            </div>
                            <h4 class="text-lg font-bold transition-colors text-slate-900 group-hover:text-teal-700">{{ __('Основы HTML и CSS') }}</h4>
                            <p class="mt-2 text-sm leading-relaxed text-slate-500">Разметка страниц, базовые теги, селекторы и работа с простыми макетами.</p>
                        </div>
                        <div class="mt-8">
                            @if($isDone1)
                                <div class="w-full py-3 text-sm font-bold text-center text-teal-700 bg-teal-50 rounded-xl">{{ __('Пройдено') }}</div>
                            @else
                                <form method="POST" action="{{ route('lessons.complete') }}">
                                    @csrf
                                    <input type="hidden" name="lesson_key" value="basics-html-css">
                                    <button type="submit" class="w-full py-3 text-sm font-bold text-white transition-all bg-teal-600 shadow-lg rounded-xl hover:bg-teal-700 shadow-teal-100">
                                        {{ __('Завершить урок') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    @php $isDone2 = !empty($completedLessons) && in_array('intro-php', $completedLessons, true); @endphp
                    <div class="flex flex-col justify-between p-6 transition-all bg-white border shadow-sm rounded-3xl group hover:shadow-md {{ $isDone2 ? 'border-teal-100' : 'border-gray-100' }}">
                        <div>
                            <div class="flex items-start justify-between mb-4">
                                <span class="px-3 py-1 text-xs font-bold tracking-widest text-orange-500 uppercase bg-orange-50 rounded-lg">Backend</span>
                                @if($isDone2)
                                    <span class="p-1.5 text-teal-600 bg-teal-50 rounded-full">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                    </span>
                                @endif
                            </div>
                            <h4 class="text-lg font-bold transition-colors text-slate-900 group-hover:text-orange-600">{{ __('Введение в PHP') }}</h4>
                            <p class="mt-2 text-sm leading-relaxed text-slate-500">Переменные, условия, циклы и серверная логика.</p>
                        </div>
                        <div class="mt-8">
                            @if($isDone2)
                                <div class="w-full py-3 text-sm font-bold text-center text-teal-700 bg-teal-50 rounded-xl">{{ __('Пройдено') }}</div>
                            @else
                                <form method="POST" action="{{ route('lessons.complete') }}">
                                    @csrf
                                    <input type="hidden" name="lesson_key" value="intro-php">
                                    <button type="submit" class="w-full py-3 text-sm font-bold text-white transition-all bg-teal-600 shadow-lg rounded-xl hover:bg-teal-700 shadow-teal-100">
                                        {{ __('Завершить урок') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </section>

            @php $isLocked = (auth()->user()->level ?? 'beginner') === 'beginner'; @endphp
            <section class="relative">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 text-orange-600 bg-orange-100 shadow-sm rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-extrabold tracking-tight text-slate-800">{{ __('Продвинутый уровень') }}</h3>
                    </div>
                    @if($isLocked)
                        <span class="flex items-center gap-1.5 px-4 py-1.5 bg-gray-200 text-gray-600 rounded-full text-xs font-black uppercase shadow-inner">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                            {{ __('Locked') }}
                        </span>
                    @endif
                </div>

                <div class="grid gap-6 sm:grid-cols-2 {{ $isLocked ? 'blur-sm pointer-events-none grayscale opacity-50 select-none' : '' }}">
                    <div class="flex flex-col justify-between p-6 bg-white border border-gray-100 shadow-sm rounded-3xl">
                        <div>
                            <span class="px-3 py-1 text-xs font-bold tracking-widest text-purple-500 uppercase bg-purple-50 rounded-lg">Mastery</span>
                            <h4 class="mt-4 text-lg font-bold leading-tight text-slate-900">{{ __('Работа с API и паттерны') }}</h4>
                            <p class="mt-2 text-sm italic leading-relaxed text-slate-500">Построение REST API, Repository и Service в Laravel.</p>
                        </div>
                        <div class="flex items-center justify-between mt-8 pt-4 text-xs font-bold border-t border-gray-50 text-slate-400">
                            <span>24 мин. видео</span>
                            <span>+400 XP</span>
                        </div>
                    </div>

                    <div class="flex flex-col justify-between p-6 bg-white border border-gray-100 shadow-sm rounded-3xl">
                        <div>
                            <span class="px-3 py-1 text-xs font-bold tracking-widest text-rose-500 uppercase bg-rose-50 rounded-lg">Quality</span>
                            <h4 class="mt-4 text-lg font-bold leading-tight text-slate-900">{{ __('Тестирование (TDD)') }}</h4>
                            <p class="mt-2 text-sm italic leading-relaxed text-slate-500">Покрытие кода тестами Pest/PHPUnit и рефакторинг.</p>
                        </div>
                        <div class="flex items-center justify-between mt-8 pt-4 text-xs font-bold border-t border-gray-50 text-slate-400">
                            <span>35 мин. видео</span>
                            <span>+600 XP</span>
                        </div>
                    </div>
                </div>

                @if($isLocked)
                    <div class="absolute inset-0 z-20 flex items-center justify-center">
                        <div class="max-w-sm px-8 py-6 text-center transition-transform transform border border-white bg-white/90 backdrop-blur-md rounded-3xl shadow-2xl -rotate-1 hover:rotate-0">
                            <p class="mb-4 font-bold text-slate-800">Эти знания пока скрыты</p>
                            <p class="mb-6 text-xs text-slate-500">Пройдите входной тест, чтобы разблокировать доступ к Master-урокам.</p>
                            <a href="/placement-test" class="inline-block px-6 py-3 text-xs font-black tracking-widest text-white uppercase transition-all bg-orange-500 shadow-lg rounded-xl hover:bg-orange-600 shadow-orange-200">
                                Повысить уровень
                            </a>
                        </div>
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
