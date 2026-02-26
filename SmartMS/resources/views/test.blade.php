<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Входной тест SmartLMS') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">
                    <p class="text-sm text-gray-600">
                        {{ __('Ответьте на три коротких вопроса, чтобы SmartLMS смог адаптировать содержимое под ваш уровень.') }}
                    </p>

                    <form method="POST" action="{{ route('test.process') }}" class="space-y-6">
                        @csrf

                        <div>
                            <h3 class="font-medium text-gray-900">
                                1. {{ __('PHP - это серверный язык?') }}
                            </h3>
                            <div class="mt-2 space-y-2">
                                <label class="flex items-center space-x-2">
                                    <input type="radio" name="answers[1]" value="yes" class="text-teal-600 border-gray-300">
                                    <span>{{ __('Да') }}</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="radio" name="answers[1]" value="no" class="text-teal-600 border-gray-300">
                                    <span>{{ __('Нет') }}</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <h3 class="font-medium text-gray-900">
                                2. {{ __('SQL нужен для работы с базами данных?') }}
                            </h3>
                            <div class="mt-2 space-y-2">
                                <label class="flex items-center space-x-2">
                                    <input type="radio" name="answers[2]" value="yes" class="text-teal-600 border-gray-300">
                                    <span>{{ __('Да') }}</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="radio" name="answers[2]" value="no" class="text-teal-600 border-gray-300">
                                    <span>{{ __('Нет') }}</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <h3 class="font-medium text-gray-900">
                                3. {{ __('Laravel - это PHP фреймворк?') }}
                            </h3>
                            <div class="mt-2 space-y-2">
                                <label class="flex items-center space-x-2">
                                    <input type="radio" name="answers[3]" value="yes" class="text-teal-600 border-gray-300">
                                    <span>{{ __('Да') }}</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="radio" name="answers[3]" value="no" class="text-teal-600 border-gray-300">
                                    <span>{{ __('Нет') }}</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <x-primary-button>
                                {{ __('Завершить тест и перейти в кабинет') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

