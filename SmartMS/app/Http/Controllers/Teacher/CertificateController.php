<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function index(): View
    {
        $students = User::query()
            ->where('role', User::ROLE_STUDENT)
            ->whereNotNull('grade')
            ->orderBy('grade')
            ->orderBy('name')
            ->get();

        $certificates = Certificate::query()
            ->with(['student', 'teacher'])
            ->latest('awarded_at')
            ->latest()
            ->get();

        return view('teacher.certificates.index', [
            'students' => $students,
            'certificates' => $certificates,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'awarded_at' => ['nullable', 'date'],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:20480'],
        ]);

        $student = User::findOrFail($data['user_id']);
        if (!$student->isStudent()) {
            abort(403);
        }

        $path = $request->file('file')->store('certificates', 'public');

        Certificate::create([
            'user_id' => $student->id,
            'teacher_id' => $request->user()->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'file_path' => $path,
            'awarded_at' => $data['awarded_at'] ?? now(),
        ]);

        return redirect()->route('teacher.certificates.index')
            ->with('status', __('messages.certificate_uploaded'));
    }

    public function destroy(Certificate $certificate): RedirectResponse
    {
        if ($certificate->file_path) {
            Storage::disk('public')->delete($certificate->file_path);
        }

        $certificate->delete();

        return redirect()->route('teacher.certificates.index')
            ->with('status', __('messages.certificate_deleted'));
    }
}
