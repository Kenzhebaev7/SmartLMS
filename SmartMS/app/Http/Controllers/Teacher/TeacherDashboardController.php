<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
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

        $studentsByGrade = [
            9 => $students->where('grade', 9)->count(),
            10 => $students->where('grade', 10)->count(),
            11 => $students->where('grade', 11)->count(),
        ];

        return view('teacher.dashboard', [
            'sections' => $sections,
            'students' => $students,
            'certificatesCount' => Certificate::count(),
            'sectionsCount' => $sections->count(),
            'studentsCount' => $students->count(),
            'studentsByGrade' => $studentsByGrade,
        ]);
    }
}
