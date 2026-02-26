<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Личный кабинет SmartLMS') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold mb-1">
                                {{ __('Ваш текущий уровень') }}
                            </h3>
                            <p class="text-sm text-gray-600">
                                {{ __('Система подбирает контент исходя из вашего уровня владения технологиями.') }}
                            </p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-teal-100 text-teal-800">
                            {{ auth()->user()->level ?? 'beginner' }}
                        </span>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">
                                {{ __('Прогресс по урокам') }}
                            </span>
                            <span class="text-xs text-gray-500">
                                {{ $completedLessonsCount }}/{{ $totalLessons }} ({{ $progress }}%)
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                            <div class="bg-teal-500 h-3 rounded-full transition-all duration-300"
                                 style="width: {{ $progress }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-2">
                        {{ __('Начальные уроки') }}
                    </h3>
                    <p class="text-sm text-gray-600">
                        {{ __('Базовые материалы по основам программирования, синтаксису и ключевым концепциям. Рекомендуется всем пользователям.') }}
                    </p>
                </div>
            </div>

            @if((auth()->user()->level ?? 'beginner') === 'advanced')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-2">
                            {{ __('Продвинутые уроки') }}
                        </h3>
                        <p class="text-sm text-gray-600">
                            {{ __('Углублённые материалы по архитектуре систем, паттернам проектирования и работе с реальными кейсами.') }}
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
