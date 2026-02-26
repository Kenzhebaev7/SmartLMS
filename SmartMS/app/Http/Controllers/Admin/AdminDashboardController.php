<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Result;
use App\Models\Section;
use App\Models\Thread;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'users' => User::count(),
            'students' => User::where('role', User::ROLE_STUDENT)->count(),
            'teachers' => User::where('role', User::ROLE_TEACHER)->count(),
            'sections' => Section::count(),
            'threads' => Thread::count(),
            'quiz_attempts' => Result::count(),
        ];
        return view('admin.dashboard', ['stats' => $stats]);
    }
}
