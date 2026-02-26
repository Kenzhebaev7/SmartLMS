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
    public function index(): View
    {
        $users = User::latest()->paginate(20);
        return view('admin.users.index', ['users' => $users]);
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
            'level' => in_array($data['role'], [User::ROLE_TEACHER, User::ROLE_ADMIN], true) ? null : 'none',
        ]);

        return redirect()->route('admin.users.index')->with('status', __('admin.user_created'));
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
        return redirect()->route('admin.users.index')->with('status', __('admin.user_deleted'));
    }
}
