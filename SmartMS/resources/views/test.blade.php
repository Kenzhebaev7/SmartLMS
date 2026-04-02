<x-app-layout>
    <div class="py-12 bg-[#FDFDFC] min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-8 text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 border border-amber-100 mb-4">
                    <span class="flex h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span class="text-xs font-bold text-amber-700 uppercase tracking-widest">{{ __('messages.test_badge') }}</span>
                </div>
                <h2 class="text-3xl font-extrabold text-teal-900 italic">{{ __('messages.test_title') }}</h2>
                <p class="text-gray-500 mt-2">{{ __('messages.test_subtitle') }}</p>
            </div>

            <div class="bg-white overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.05)] rounded-[2.5rem] border border-gray-100">
                <div class="p-8 md:p-12">

                    <form method="POST" action="{{ route('test.process') }}" class="space-y-10">
                        @csrf

                        <div class="relative pl-8 before:absolute before:left-0 before:top-0 before:bottom-0 before:w-1 before:bg-teal-100 before:rounded-full">
                            <h3 class="text-lg font-bold text-slate-800 mb-4">
                                1. {{ __('messages.test_q1') }}
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <label class="relative flex items-center p-4 border border-gray-100 rounded-2xl cursor-pointer hover:bg-teal-50 transition-all group">
                                    <input type="radio" name="answers[1]" value="yes" class="w-5 h-5 text-teal-600 border-gray-300 focus:ring-teal-500">
                                    <span class="ml-3 font-semibold text-slate-600 group-hover:text-teal-700">{{ __('messages.test_yes_1') }}</span>
                                </label>
                                <label class="relative flex items-center p-4 border border-gray-100 rounded-2xl cursor-pointer hover:bg-red-50 transition-all group">
                                    <input type="radio" name="answers[1]" value="no" class="w-5 h-5 text-teal-600 border-gray-300 focus:ring-teal-500">
                                    <span class="ml-3 font-semibold text-slate-600 group-hover:text-red-700">{{ __('messages.test_no_1') }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="relative pl-8 before:absolute before:left-0 before:top-0 before:bottom-0 before:w-1 before:bg-teal-100 before:rounded-full">
                            <h3 class="text-lg font-bold text-slate-800 mb-4">
                                2. {{ __('messages.test_q2') }}
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <label class="relative flex items-center p-4 border border-gray-100 rounded-2xl cursor-pointer hover:bg-teal-50 transition-all group">
                                    <input type="radio" name="answers[2]" value="yes" class="w-5 h-5 text-teal-600 border-gray-300 focus:ring-teal-500">
                                    <span class="ml-3 font-semibold text-slate-600 group-hover:text-teal-700">{{ __('messages.test_yes_2') }}</span>
                                </label>
                                <label class="relative flex items-center p-4 border border-gray-100 rounded-2xl cursor-pointer hover:bg-red-50 transition-all group">
                                    <input type="radio" name="answers[2]" value="no" class="w-5 h-5 text-teal-600 border-gray-300 focus:ring-teal-500">
                                    <span class="ml-3 font-semibold text-slate-600 group-hover:text-red-700">{{ __('messages.test_no_2') }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="relative pl-8 before:absolute before:left-0 before:top-0 before:bottom-0 before:w-1 before:bg-teal-100 before:rounded-full">
                            <h3 class="text-lg font-bold text-slate-800 mb-4">
                                3. {{ __('messages.test_q3') }}
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <label class="relative flex items-center p-4 border border-gray-100 rounded-2xl cursor-pointer hover:bg-teal-50 transition-all group">
                                    <input type="radio" name="answers[3]" value="yes" class="w-5 h-5 text-teal-600 border-gray-300 focus:ring-teal-500">
                                    <span class="ml-3 font-semibold text-slate-600 group-hover:text-teal-700">{{ __('messages.test_yes_3') }}</span>
                                </label>
                                <label class="relative flex items-center p-4 border border-gray-100 rounded-2xl cursor-pointer hover:bg-red-50 transition-all group">
                                    <input type="radio" name="answers[3]" value="no" class="w-5 h-5 text-teal-600 border-gray-300 focus:ring-teal-500">
                                    <span class="ml-3 font-semibold text-slate-600 group-hover:text-red-700">{{ __('messages.test_no_3') }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-50 flex justify-center">
                            <button type="submit" class="w-full md:w-auto px-12 py-4 bg-teal-600 text-white rounded-2xl font-bold text-lg shadow-xl shadow-teal-100 hover:bg-teal-700 hover:-translate-y-1 transition-all active:scale-95">
                                {{ __('messages.placement_finish') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <p class="text-center mt-8 text-sm text-gray-400">
                {{ __('messages.test_footer') }}
            </p>
        </div>
    </div>
</x-app-layout>
