<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $levels = ['beginner', 'advanced'];
        return view('admin.settings', ['levels' => $levels]);
    }
}
