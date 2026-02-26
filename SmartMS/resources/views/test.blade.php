<x-app-layout>
    <div class="py-12 bg-[#FDFDFC] min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-8 text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 border border-amber-100 mb-4">
                    <span class="flex h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span class="text-xs font-bold text-amber-700 uppercase tracking-widest">Адаптация профиля</span>
                </div>
                <h2 class="text-3xl font-extrabold text-teal-900 italic">SmartLMS Test</h2>
                <p class="text-gray-500 mt-2">Ответьте на 3 вопроса, чтобы мы подобрали идеальный темп обучения.</p>
            </div>

            <div class="bg-white overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.05)] rounded-[2.5rem] border border-gray-100">
                <div class="p-8 md:p-12">

                    <form method="POST" action="{{ route('test.process') }}" class="space-y-10">
                        @csrf

                        <div class="relative pl-8 before:absolute before:left-0 before:top-0 before:bottom-0 before:w-1 before:bg-teal-100 before:rounded-full">
                            <h3 class="text-lg font-bold text-slate-800 mb-4">
                                1. {{ __('PHP — это серверный язык программирования?') }}
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <label class="relative flex items-center p-4 border border-gray-100 rounded-2xl cursor-pointer hover:bg-teal-50 transition-all group">
                                    <input type="radio" name="answers[1]" value="yes" class="w-5 h-5 text-teal-600 border-gray-300 focus:ring-teal-500">
                                    <span class="ml-3 font-semibold text-slate-600 group-hover:text-teal-700">Да, верно</span>
                                </label>
                                <label class="relative flex items-center p-4 border border-gray-100 rounded-2xl cursor-pointer hover:bg-red-50 transition-all group">
                                    <input type="radio" name="answers[1]" value="no" class="w-5 h-5 text-teal-600 border-gray-300 focus:ring-teal-500">
                                    <span class="ml-3 font-semibold text-slate-600 group-hover:text-red-700">Нет</span>
                                </label>
                            </div>
                        </div>

                        <div class="relative pl-8 before:absolute before:left-0 before:top-0 before:bottom-0 before:w-1 before:bg-teal-100 before:rounded-full">
                            <h3 class="text-lg font-bold text-slate-800 mb-4">
                                2. {{ __('SQL необходим для работы с базами данных?') }}
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <label class="relative flex items-center p-4 border border-gray-100 rounded-2xl cursor-pointer hover:bg-teal-50 transition-all group">
                                    <input type="radio" name="answers[2]" value="yes" class="w-5 h-5 text-teal-600 border-gray-300 focus:ring-teal-500">
                                    <span class="ml-3 font-semibold text-slate-600 group-hover:text-teal-700">Безусловно</span>
                                </label>
                                <label class="relative flex items-center p-4 border border-gray-100 rounded-2xl cursor-pointer hover:bg-red-50 transition-all group">
                                    <input type="radio" name="answers[2]" value="no" class="w-5 h-5 text-teal-600 border-gray-300 focus:ring-teal-500">
                                    <span class="ml-3 font-semibold text-slate-600 group-hover:text-red-700">Ни в коем случае</span>
                                </label>
                            </div>
                        </div>

                        <div class="relative pl-8 before:absolute before:left-0 before:top-0 before:bottom-0 before:w-1 before:bg-teal-100 before:rounded-full">
                            <h3 class="text-lg font-bold text-slate-800 mb-4">
                                3. {{ __('Laravel является PHP-фреймворком?') }}
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <label class="relative flex items-center p-4 border border-gray-100 rounded-2xl cursor-pointer hover:bg-teal-50 transition-all group">
                                    <input type="radio" name="answers[3]" value="yes" class="w-5 h-5 text-teal-600 border-gray-300 focus:ring-teal-500">
                                    <span class="ml-3 font-semibold text-slate-600 group-hover:text-teal-700">Именно так</span>
                                </label>
                                <label class="relative flex items-center p-4 border border-gray-100 rounded-2xl cursor-pointer hover:bg-red-50 transition-all group">
                                    <input type="radio" name="answers[3]" value="no" class="w-5 h-5 text-teal-600 border-gray-300 focus:ring-teal-500">
                                    <span class="ml-3 font-semibold text-slate-600 group-hover:text-red-700">Это не фреймворк</span>
                                </label>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-50 flex justify-center">
                            <button type="submit" class="w-full md:w-auto px-12 py-4 bg-teal-600 text-white rounded-2xl font-bold text-lg shadow-xl shadow-teal-100 hover:bg-teal-700 hover:-translate-y-1 transition-all active:scale-95">
                                {{ __('Завершить и войти в кабинет') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <p class="text-center mt-8 text-sm text-gray-400">
                Ваши ответы помогут нам скрыть слишком простые уроки.
            </p>
        </div>
    </div>
</x-app-layout>
