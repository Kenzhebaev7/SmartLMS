<x-app-layout>
    <div class="py-12 bg-[#FDFDFC] min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8 text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 border border-amber-100 mb-4">
                    <span class="flex h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span class="text-xs font-bold text-amber-700 uppercase tracking-widest">{{ __('placement.title') }}</span>
                </div>
                <h2 class="text-3xl font-extrabold text-primary italic">SmartLMS</h2>
                <p class="text-gray-500 mt-2">{{ __('placement.subtitle') }}</p>
            </div>

            <div class="bg-white overflow-hidden shadow-lg rounded-2xl border border-gray-100">
                <div class="p-8 md:p-12">
                    <form method="POST" action="{{ route('placement-test.process') }}" class="space-y-10">
                        @csrf
                        @foreach($questions ?? [] as $id => $data)
                            <div class="relative pl-8 before:absolute before:left-0 before:top-0 before:bottom-0 before:w-1 before:bg-teal-100 before:rounded-full">
                                <h3 class="text-lg font-bold text-slate-800 mb-4">{{ $id }}. {{ $data['q'] }}</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <label class="relative flex items-center p-4 border border-gray-100 rounded-2xl cursor-pointer hover:bg-primary-pale transition-all group">
                                        <input type="radio" name="answers[{{ $id }}]" value="yes" class="w-5 h-5 text-primary border-gray-300 focus:ring-primary">
                                        <span class="ml-3 font-semibold text-slate-600 group-hover:text-teal-700">{{ __('placement.yes') }}</span>
                                    </label>
                                    <label class="relative flex items-center p-4 border border-gray-100 rounded-2xl cursor-pointer hover:bg-red-50 transition-all group">
                                        <input type="radio" name="answers[{{ $id }}]" value="no" class="w-5 h-5 text-primary border-gray-300 focus:ring-primary">
                                        <span class="ml-3 font-semibold text-slate-600 group-hover:text-red-700">{{ __('placement.no') }}</span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                        <div class="pt-6 border-t border-gray-50 flex justify-center">
                            <button type="submit" class="w-full md:w-auto px-12 py-4 bg-primary text-white rounded-2xl font-bold text-lg shadow-lg hover:bg-primary-light transition-colors transition-all">
                                {{ __('placement.finish') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
