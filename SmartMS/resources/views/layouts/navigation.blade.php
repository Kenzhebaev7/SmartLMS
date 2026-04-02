<nav x-data="{ open: false }" class="bg-white/80 backdrop-blur-md border-b border-slate-100 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20"> <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="group flex items-center gap-3">
                        <div class="w-10 h-10 bg-teal-600 rounded-xl flex items-center justify-center shadow-lg group-hover:rotate-6 transition-transform">
                            <span class="text-white font-black text-xl">S</span>
                        </div>
                        <span class="hidden md:block text-xl font-black text-teal-900 tracking-tighter italic">SmartLMS</span>
                    </a>
                </div>

                <div class="hidden space-x-4 sm:-my-px sm:ms-10 sm:flex items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                                class="px-4 py-2 rounded-xl border-none text-sm font-bold transition-all hover:bg-teal-50">
                        {{ __('messages.nav_progress') }}
                    </x-nav-link>

                    <x-nav-link :href="route('sections.index')" :active="request()->routeIs('sections.*')"
                                class="px-4 py-2 rounded-xl border-none text-sm font-bold transition-all hover:bg-teal-50">
                        {{ __('messages.nav_lessons') }}
                    </x-nav-link>

                    @auth
                        <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')"
                                    class="px-4 py-2 rounded-xl border-none text-sm font-bold transition-all hover:bg-teal-50">
                            {{ __('messages.nav_profile') }}
                        </x-nav-link>
                    @endauth

                    <form action="{{ route('search.index') }}" method="GET" class="hidden lg:flex items-center ms-4">
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="{{ __('messages.search_placeholder_short') }}"
                            class="w-40 xl:w-56 rounded-full border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-xs px-3 py-1.5 text-slate-700 dark:text-slate-100 placeholder:text-slate-400"
                        >
                    </form>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @if(Auth::user()->role === 'student' && Auth::user()->grade)
                <div class="me-4 px-3 py-1 bg-slate-100 border border-slate-200 rounded-full">
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-600">{{ __('messages.auth_grade_' . Auth::user()->grade) }}</span>
                </div>
                @endif

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2 bg-slate-50 border border-transparent text-sm leading-4 font-bold rounded-xl text-slate-700 hover:bg-slate-100 focus:outline-none transition duration-150">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-teal-100 text-teal-700 rounded-full flex items-center justify-center text-[10px]">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                {{ Auth::user()->name }}
                            </div>

                            <div class="ms-2">
                                <svg class="fill-current h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Аккаунт</p>
                            <p class="text-sm font-medium text-gray-700 truncate">{{ Auth::user()->email }}</p>
                        </div>

                        <x-dropdown-link :href="route('profile.edit')" class="font-bold text-sm text-slate-600">
                            {{ __('messages.nav_profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                             class="font-bold text-sm text-rose-500"
                                             onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('messages.nav_logout') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 ms-4">
                <a href="{{ route('trainer.errors') }}" class="text-xs font-semibold text-slate-500 hover:text-primary">
                    {{ __('messages.trainer_errors_nav') }}
                </a>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-xl text-slate-400 hover:text-teal-600 hover:bg-teal-50 transition duration-150">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-t border-gray-100">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="font-bold text-teal-600">
                {{ __('messages.nav_progress') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('sections.index')" :active="request()->routeIs('sections.*')" class="font-bold">
                {{ __('messages.nav_lessons') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200 bg-slate-50">
            <div class="px-4 flex items-center justify-between">
                <div>
                    <div class="font-bold text-base text-slate-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-xs text-slate-500 tracking-tight">{{ Auth::user()->email }}</div>
                </div>
                @if(Auth::user()->grade)
                <div class="px-3 py-1 bg-teal-600 rounded-full">
                    <span class="text-[10px] font-black text-white uppercase">{{ __('messages.auth_grade_' . Auth::user()->grade) }}</span>
                </div>
                @endif
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="font-bold text-sm">
                    {{ __('messages.nav_profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                                           class="font-bold text-sm text-rose-500"
                                           onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('messages.nav_logout') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
