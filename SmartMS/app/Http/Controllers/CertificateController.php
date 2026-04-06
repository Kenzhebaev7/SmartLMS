<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $certificates = Certificate::query()
            ->where('user_id', $user->id)
            ->with('teacher')
            ->latest('awarded_at')
            ->latest()
            ->get();

        return view('certificates.index', [
            'certificates' => $certificates,
        ]);
    }
}
