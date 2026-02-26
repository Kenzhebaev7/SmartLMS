<x-app-layout>
    <x-slot name="header">{{ __('admin.create_user') }}</x-slot>

    <form action="{{ route('admin.users.store') }}" method="POST" class="max-w-md space-y-4">
        @csrf
        <div>
            <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('auth.name') }}</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-3 py-2">
            @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('auth.email') }}</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-3 py-2">
            @error('email')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('auth.password') }}</label>
            <input type="password" name="password" id="password" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-3 py-2">
            @error('password')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('auth.password_confirm') }}</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-3 py-2">
        </div>
        <div>
            <label for="role" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('admin.role') }}</label>
            <select name="role" id="role" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-3 py-2">
                @foreach(\App\Models\User::roles() as $role)
                    <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>{{ __('admin.role_' . $role) }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2 bg-primary text-white rounded-xl font-semibold hover:bg-primary-light transition-colors">{{ __('teacher.create') }}</button>
            <a href="{{ route('admin.users.index') }}" class="px-6 py-2 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-700 dark:text-slate-300">{{ __('teacher.cancel') }}</a>
        </div>
    </form>

    <div class="mt-6">
        <a href="{{ route('admin.dashboard') }}" class="text-primary font-semibold hover:underline">← {{ __('admin.back_admin') }}</a>
    </div>
</x-app-layout>
