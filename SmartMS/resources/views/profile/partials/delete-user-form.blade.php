<section class="space-y-3">
    <header>
        <h2 class="text-sm font-semibold text-red-700">
            {{ __('messages.profile_delete_account') }}
        </h2>

        <p class="mt-1 text-xs text-gray-500">
            {{ __('messages.profile_delete_warning') }}
        </p>
    </header>

    <x-danger-button
        class="!px-3 !py-1.5 text-xs font-semibold"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('messages.profile_delete_account') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('messages.profile_delete_confirm_title') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('messages.profile_delete_confirm_desc') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('messages.auth_password') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('messages.auth_password') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('messages.profile_cancel') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('messages.profile_delete_account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
