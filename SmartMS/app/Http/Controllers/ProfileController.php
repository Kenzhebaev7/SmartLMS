<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Controllers\SectionController;
use App\Models\Certificate;
use App\Models\LessonProgress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        $sections = SectionController::sectionsForUser($user);
        $progressBySection = [];

        if ($user && $user->role === \App\Models\User::ROLE_STUDENT) {
            $sections->load('lessons');
            $lessonIdsBySection = $sections->keyBy('id')->map(fn ($s) => $s->lessons->pluck('id')->all());
            $userProgress = LessonProgress::where('user_id', $user->id)->get();
            foreach ($sections as $section) {
                $ids = $lessonIdsBySection->get($section->id, []);
                $total = count($ids);
                $completed = $userProgress->filter(function ($p) use ($ids) {
                    return in_array($p->lesson_id, $ids) || in_array((int) $p->lesson_key, $ids);
                })->count();
                $percent = $total > 0 ? round((min($completed, $total) / $total) * 100) : 0;
                $progressBySection[$section->id] = ['percent' => $percent, 'completed' => $completed, 'total' => $total];
            }
        }

        return view('profile.edit', [
            'user' => $user,
            'sections' => $sections,
            'progressBySection' => $progressBySection,
            'certificates' => $user && $user->role === \App\Models\User::ROLE_STUDENT
                ? Certificate::where('user_id', $user->id)->with('teacher')->latest('awarded_at')->latest()->get()
                : collect(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
