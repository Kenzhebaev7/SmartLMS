<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\User;
use Illuminate\View\View;

class TeacherDashboardController extends Controller
{
    public function index(): View
    {
        $sections = Section::withCount('lessons')->orderBy('grade')->orderByDesc('is_featured')->orderBy('order')->get();

        $students = User::where('role', User::ROLE_STUDENT)
            ->whereNotNull('grade')
            ->orderBy('grade')
            ->orderBy('name')
            ->withCount('lessonProgresses')
            ->get();

        return view('teacher.dashboard', [
            'sections' => $sections,
            'students' => $students,
        ]);
    }
}
