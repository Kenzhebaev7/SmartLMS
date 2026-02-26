<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\View\View;

class TeacherDashboardController extends Controller
{
    public function index(): View
    {
        $sections = Section::withCount('lessons')->orderBy('order')->get();

        return view('teacher.dashboard', ['sections' => $sections]);
    }
}
