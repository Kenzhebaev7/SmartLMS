<x-guest-layout>
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 md:p-10">
        <div class="mb-8 text-center">
            <h2 class="text-2xl md:text-3xl font-bold text-slate-800 tracking-tight">{{ __('auth.register_title') }}</h2>
            <p class="text-slate-500 mt-1 text-sm">{{ __('auth.register_subtitle') }}</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700 mb-1.5">{{ __('auth.name') }}</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition placeholder:text-slate-400"
                       placeholder="Атыңыз">
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">{{ __('auth.email') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition placeholder:text-slate-400"
                       placeholder="mail@example.com">
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">{{ __('auth.password') }}</label>
                <input id="password" type="password" name="password" required
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition placeholder:text-slate-400"
                       placeholder="••••••••">
                @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1.5">{{ __('auth.password_confirm') }}</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition placeholder:text-slate-400"
                       placeholder="••••••••">
            </div>

            <button type="submit" class="w-full py-3.5 bg-primary text-white rounded-xl font-bold text-sm hover:bg-primary-light transition-colors shadow-md">
                {{ __('auth.register_button') }}
            </button>

            <p class="text-center text-sm text-slate-500">
                {{ __('auth.have_account') }} <a href="{{ route('login') }}" class="font-semibold text-primary hover:underline">{{ __('auth.login_link') }}</a>
            </p>
        </form>
    </div>
</x-guest-layout>
