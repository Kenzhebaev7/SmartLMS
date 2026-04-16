<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()->withCount(['lessonProgresses', 'results'])->latest();

        if ($request->filled('role') && in_array($request->string('role')->toString(), User::roles(), true)) {
            $query->where('role', $request->string('role')->toString());
        }

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'roleFilter' => $request->string('role')->toString(),
            'stats' => [
                'all' => User::count(),
                'students' => User::where('role', User::ROLE_STUDENT)->count(),
                'teachers' => User::where('role', User::ROLE_TEACHER)->count(),
                'admins' => User::where('role', User::ROLE_ADMIN)->count(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'in:'.implode(',', User::roles())],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'grade' => in_array($data['role'], [User::ROLE_TEACHER, User::ROLE_ADMIN], true) ? null : 9,
            'placement_passed' => null,
        ]);

        return redirect()->route('admin.users.index')->with('status', __('messages.admin_user_created'));
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $request->validate(['role' => ['required', 'in:'.implode(',', User::roles())]]);
        $user->update(['role' => $request->input('role')]);
        return redirect()->route('admin.users.index')->with('status', __('messages.role_updated'));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return redirect()->route('admin.users.index')->with('error', __('admin.cannot_delete_self'));
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('status', __('messages.admin_user_deleted'));
    }
}
