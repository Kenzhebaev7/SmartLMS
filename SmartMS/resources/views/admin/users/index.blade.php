<x-app-layout>
    <x-slot name="header">{{ __('messages.admin_users_index') }}</x-slot>

    @if(session('status'))
        <div class="mb-6 rounded-xl bg-primary-50 dark:bg-primary/20 border border-primary-200 dark:border-primary/30 px-4 py-3 text-primary-light dark:text-primary-200">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-6 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 px-4 py-3 text-red-700 dark:text-red-300">{{ session('error') }}</div>
    @endif

    <div class="mb-4">
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-xl font-semibold hover:bg-primary-light transition-colors">{{ __('messages.admin_add_user') }}</a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-200 rounded-xl overflow-hidden">
            <thead class="bg-slate-100">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">{{ __('messages.admin_id') }}</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">{{ __('messages.admin_name') }}</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">{{ __('messages.admin_email') }}</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">{{ __('messages.admin_role') }}</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">{{ __('messages.dashboard_grade') }}</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">{{ __('messages.admin_actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($users ?? [] as $user)
                    <tr>
                        <td class="px-4 py-3 text-slate-600">{{ $user->id }}</td>
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $user->email }}</td>
                        <td class="px-4 py-3">{{ __('messages.admin_role_' . $user->role) }}</td>
                        <td class="px-4 py-3">{{ $user->grade ? __('messages.auth_grade_' . $user->grade) : '—' }}</td>
                        <td class="px-4 py-3">
                            <form action="{{ route('admin.users.updateRole', $user) }}" method="POST" class="inline-flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="role" onchange="this.form.submit()" class="rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 px-2 py-1 text-sm">
                                    @foreach(\App\Models\User::roles() as $role)
                                        <option value="{{ $role }}" {{ $user->role === $role ? 'selected' : '' }}>{{ __('messages.admin_role_' . $role) }}</option>
                                    @endforeach
                                </select>
                            </form>
                            @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline ml-2" onsubmit="return confirm('{{ __('messages.admin_delete_confirm') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:underline text-sm">{{ __('messages.admin_delete_user') }}</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $users->links() }}
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.dashboard') }}" class="text-primary font-semibold hover:underline">← {{ __('messages.admin_back_admin') }}</a>
    </div>
</x-app-layout>
