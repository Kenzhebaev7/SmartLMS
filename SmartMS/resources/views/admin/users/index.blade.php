<x-app-layout>
    <x-slot name="header">{{ __('messages.admin_users_index') }}</x-slot>

    @if(session('status'))
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 shadow-sm">
            {{ session('status') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-700 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="space-y-8">
        <section class="rounded-[30px] border border-slate-200 bg-gradient-to-br from-white via-sky-50/40 to-amber-50/40 p-6 shadow-sm">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
                <div class="max-w-3xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.admin_manage_users') }}</p>
                    <h2 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">{{ __('messages.admin_users_overview_title') }}</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-600">{{ __('messages.admin_users_overview_desc') }}</p>
                </div>
                <a href="{{ route('admin.users.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                    {{ __('messages.admin_add_user') }}
                </a>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-2 2xl:grid-cols-4">
                <div class="rounded-2xl border border-white bg-white/90 p-4 shadow-sm text-fit">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.admin_users_stat_all') }}</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $stats['all'] ?? 0 }}</p>
                </div>
                <div class="rounded-2xl border border-white bg-white/90 p-4 shadow-sm text-fit">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.admin_role_student') }}</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $stats['students'] ?? 0 }}</p>
                </div>
                <div class="rounded-2xl border border-white bg-white/90 p-4 shadow-sm text-fit">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.admin_role_teacher') }}</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $stats['teachers'] ?? 0 }}</p>
                </div>
                <div class="rounded-2xl border border-white bg-white/90 p-4 shadow-sm text-fit">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.admin_role_admin') }}</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $stats['admins'] ?? 0 }}</p>
                </div>
            </div>
        </section>

        <section class="rounded-[30px] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.admin_role') }}</p>
                    <h3 class="mt-2 text-xl font-bold text-slate-900">{{ __('messages.admin_users_filter_title') }}</h3>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.users.index') }}" class="rounded-full border px-4 py-2 text-sm font-semibold transition {{ blank($roleFilter) ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">
                        {{ __('messages.admin_users_stat_all') }}
                    </a>
                    @foreach(\App\Models\User::roles() as $role)
                        <a href="{{ route('admin.users.index', ['role' => $role]) }}" class="rounded-full border px-4 py-2 text-sm font-semibold transition {{ $roleFilter === $role ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">
                            {{ __('messages.admin_role_' . $role) }}
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="grid gap-4">
            @forelse($users ?? [] as $user)
                @php
                    $isActive = ($user->lesson_progresses_count ?? 0) > 0 || ($user->results_count ?? 0) > 0 || $user->updated_at?->gt(now()->subDays(30));
                    $placementState = $user->placement_passed === null
                        ? __('messages.admin_users_placement_pending')
                        : ($user->placement_passed ? __('messages.dashboard_level_advanced') : __('messages.dashboard_level_beginner'));
                @endphp
                <article class="rounded-[30px] border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">
                                    #{{ $user->id }}
                                </span>
                                <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600">
                                    {{ __('messages.admin_role_' . $user->role) }}
                                </span>
                                <span class="inline-flex items-center rounded-full {{ $isActive ? 'border-emerald-200 bg-emerald-100 text-emerald-800' : 'border-slate-200 bg-slate-100 text-slate-600' }} px-3 py-1 text-xs font-semibold">
                                    {{ $isActive ? __('messages.admin_users_active') : __('messages.admin_users_inactive') }}
                                </span>
                            </div>

                            <h3 class="mt-4 text-2xl font-bold text-slate-900">{{ $user->name }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ $user->email }}</p>

                            <div class="mt-5 grid gap-3 md:grid-cols-2 2xl:grid-cols-4">
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-fit">
                                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">{{ __('messages.dashboard_grade') }}</p>
                                    <p class="mt-2 text-lg font-bold text-slate-900">{{ $user->grade ? __('messages.auth_grade_' . $user->grade) : '-' }}</p>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-fit">
                                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">{{ __('messages.dashboard_level') }}</p>
                                    <p class="mt-2 text-lg font-bold text-slate-900">{{ $user->isStudent() ? $placementState : __('messages.admin_users_not_applicable') }}</p>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-fit">
                                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">{{ __('messages.teacher_lessons_completed') }}</p>
                                    <p class="mt-2 text-lg font-bold text-slate-900">{{ $user->lesson_progresses_count ?? 0 }}</p>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-fit">
                                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">{{ __('messages.admin_quiz_attempts') }}</p>
                                    <p class="mt-2 text-lg font-bold text-slate-900">{{ $user->results_count ?? 0 }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex shrink-0 flex-col gap-3 xl:w-48">
                            <form action="{{ route('admin.users.updateRole', $user) }}" method="POST" class="rounded-2xl border border-slate-200 bg-slate-50 p-3">
                                @csrf
                                @method('PATCH')
                                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">{{ __('messages.admin_role') }}</label>
                                <select name="role" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm text-slate-700">
                                    @foreach(\App\Models\User::roles() as $role)
                                        <option value="{{ $role }}" {{ $user->role === $role ? 'selected' : '' }}>{{ __('messages.admin_role_' . $role) }}</option>
                                    @endforeach
                                </select>
                            </form>

                            @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('{{ __('messages.admin_delete_confirm') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 transition hover:bg-red-100">
                                        {{ __('messages.admin_delete_user') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-[30px] border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center text-sm text-slate-500">
                    {{ __('messages.admin_users_empty') }}
                </div>
            @endforelse
        </section>

        <div class="mt-2">
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>
