<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('messages.profile_information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('messages.profile_information_desc') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('messages.auth_name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('messages.auth_email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('messages.profile_email_unverified') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('messages.profile_verification_resend') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('messages.profile_verification_sent') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        @if($user->role === \App\Models\User::ROLE_STUDENT)
            <div>
                <x-input-label for="grade" :value="__('messages.auth_grade')" />
                <select id="grade" name="grade" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">{{ __('messages.auth_choose_grade') }}</option>
                    <option value="9" @selected(old('grade', $user->grade) == 9)>{{ __('messages.auth_grade_9') }}</option>
                    <option value="10" @selected(old('grade', $user->grade) == 10)>{{ __('messages.auth_grade_10') }}</option>
                    <option value="11" @selected(old('grade', $user->grade) == 11)>{{ __('messages.auth_grade_11') }}</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('grade')" />
            </div>
        @endif

        <div class="flex items-center gap-4 mt-4">
            <x-primary-button>{{ __('messages.profile_save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('messages.profile_saved') }}</p>
            @endif
        </div>
    </form>
</section>
