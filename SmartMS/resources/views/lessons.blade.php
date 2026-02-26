<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Lessons') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Beginner lessons -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">
                        {{ __('Начальные уроки') }}
                    </h3>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="border border-gray-100 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
                            <h4 class="font-semibold text-gray-900 mb-1">
                                {{ __('Основы HTML и CSS') }}
                            </h4>
                            <p class="text-sm text-gray-600">
                                {{ __('Разметка страниц, базовые теги, селекторы и работа с простыми макетами.') }}
                            </p>

                            <div class="mt-4 flex items-center justify-between">
                                @if(!empty($completedLessons) && in_array('basics-html-css', $completedLessons, true))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-700">
                                        {{ __('Урок пройден') }}
                                    </span>
                                @else
                                    <form method="POST" action="{{ route('lessons.complete') }}">
                                        @csrf
                                        <input type="hidden" name="lesson_key" value="basics-html-css">
                                        <x-primary-button>
                                            {{ __('Завершить урок') }}
                                        </x-primary-button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <div class="border border-gray-100 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
                            <h4 class="font-semibold text-gray-900 mb-1">
                                {{ __('Введение в PHP') }}
                            </h4>
                            <p class="text-sm text-gray-600">
                                {{ __('Переменные, условия, циклы и базовая работа с серверной логикой.') }}
                            </p>

                            <div class="mt-4 flex items-center justify-between">
                                @if(!empty($completedLessons) && in_array('intro-php', $completedLessons, true))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-700">
                                        {{ __('Урок пройден') }}
                                    </span>
                                @else
                                    <form method="POST" action="{{ route('lessons.complete') }}">
                                        @csrf
                                        <input type="hidden" name="lesson_key" value="intro-php">
                                        <x-primary-button>
                                            {{ __('Завершить урок') }}
                                        </x-primary-button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Advanced lessons -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg @if((auth()->user()->level ?? 'beginner') === 'beginner') opacity-60 blur-[1px] @endif">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">
                            {{ __('Продвинутые уроки') }}
                        </h3>

                        @if((auth()->user()->level ?? 'beginner') === 'beginner')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                <svg class="w-4 h-4 mr-1 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v2H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-1V6a4 4 0 00-4-4zm2 6V6a2 2 0 10-4 0v2h4zm-5 3a1 1 0 011-1h4a1 1 0 110 2h-4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ __('Заблокировано для уровня beginner') }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                {{ __('Доступно для уровня advanced') }}
                            </span>
                        @endif
                    </div>

                    @if((auth()->user()->level ?? 'beginner') === 'beginner')
                        <p class="text-sm text-gray-600">
                            {{ __('Пройдите входной тест с более высоким результатом, чтобы открыть продвинутые уроки.') }}
                        </p>
                    @else
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="border border-gray-100 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
                                <h4 class="font-semibold text-gray-900 mb-1">
                                    {{ __('Работа с API и паттерны Laravel') }}
                                </h4>
                                <p class="text-sm text-gray-600">
                                    {{ __('Построение REST API, работа с HTTP-клиентом и использование ключевых паттернов Laravel.') }}
                                </p>

                                <div class="mt-4 flex items-center justify-between">
                                    @if(!empty($completedLessons) && in_array('api-and-laravel-patterns', $completedLessons, true))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-700">
                                            {{ __('Урок пройден') }}
                                        </span>
                                    @else
                                        <form method="POST" action="{{ route('lessons.complete') }}">
                                            @csrf
                                            <input type="hidden" name="lesson_key" value="api-and-laravel-patterns">
                                            <x-primary-button>
                                                {{ __('Завершить урок') }}
                                            </x-primary-button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            <div class="border border-gray-100 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
                                <h4 class="font-semibold text-gray-900 mb-1">
                                    {{ __('Тестирование и рефакторинг в Laravel') }}
                                </h4>
                                <p class="text-sm text-gray-600">
                                    {{ __('Покрытие кода тестами, поддержка качества и постепенное улучшение архитектуры.') }}
                                </p>

                                <div class="mt-4 flex items-center justify-between">
                                    @if(!empty($completedLessons) && in_array('testing-and-refactoring', $completedLessons, true))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-700">
                                            {{ __('Урок пройден') }}
                                        </span>
                                    @else
                                        <form method="POST" action="{{ route('lessons.complete') }}">
                                            @csrf
                                            <input type="hidden" name="lesson_key" value="testing-and-refactoring">
                                            <x-primary-button>
                                                {{ __('Завершить урок') }}
                                            </x-primary-button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

