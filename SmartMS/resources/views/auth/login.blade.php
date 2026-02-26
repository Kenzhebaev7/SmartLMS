<x-guest-layout>
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 md:p-10">
        <div class="mb-8 text-center">
            <h2 class="text-2xl md:text-3xl font-bold text-slate-800 tracking-tight">{{ __('auth.login_title') }}</h2>
            <p class="text-slate-500 mt-1 text-sm">{{ __('auth.login_subtitle') }}</p>
        </div>

        @if (session('status'))
            <div class="mb-4 text-sm font-medium text-green-700 bg-green-50 p-3 rounded-xl border border-green-200">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">{{ __('auth.email') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition placeholder:text-slate-400"
                       placeholder="mail@example.com">
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <label for="password" class="text-sm font-semibold text-slate-700">{{ __('auth.password') }}</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs font-semibold text-accent hover:text-accent-dark">{{ __('auth.forgot_password') }}</a>
                    @endif
                </div>
                <input id="password" type="password" name="password" required
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition placeholder:text-slate-400"
                       placeholder="••••••••">
                @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-2">
                <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary/30">
                <label for="remember_me" class="text-sm text-slate-600">{{ __('auth.remember') }}</label>
            </div>

            <button type="submit" class="w-full py-3.5 bg-primary text-white rounded-xl font-bold text-sm hover:bg-primary-light transition-colors shadow-md">
                {{ __('auth.login_button') }}
            </button>

            <p class="text-center text-sm text-slate-500">
                {{ __('auth.first_time') }} <a href="{{ route('register') }}" class="font-semibold text-primary hover:underline">{{ __('auth.create_account') }}</a>
            </p>
        </form>
    </div>
</x-guest-layout>
