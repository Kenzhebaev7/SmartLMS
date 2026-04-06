<?php

namespace App\Providers;

use App\Http\Controllers\SectionController;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view): void {
            $user = Auth::user();

            if (!$user instanceof User) {
                $view->with('navPanel', null);

                return;
            }

            $panel = [
                'role' => $user->role,
                'searchQuery' => request('q', ''),
                'canUseTeacherMode' => $user->isTeacher() || $user->isAdmin(),
                'notificationsCount' => 0,
                'notificationsRoute' => route('dashboard'),
            ];

            if ($user->isStudent()) {
                $sections = SectionController::sectionsForUser($user);
                $passed = 0;

                foreach ($sections as $section) {
                    if ($section->quiz && $user->results()->where('quiz_id', $section->quiz->id)->where('passed', true)->exists()) {
                        $passed++;
                    }
                }

                $certificatesCount = Certificate::where('user_id', $user->id)->count();

                $panel = array_merge($panel, [
                    'gradeLabel' => __('messages.auth_grade_' . $user->effectiveGradeForStudent()),
                    'levelLabel' => $user->placementLevelKey() ? __('messages.dashboard_level_' . $user->placementLevelKey()) : __('messages.teacher_level_pending'),
                    'progressLabel' => __('messages.nav_progress_summary', ['passed' => $passed, 'total' => $sections->count()]),
                    'notificationsCount' => $certificatesCount,
                    'notificationsRoute' => route('certificates.index'),
                ]);
            } else {
                $teacherCertificates = Certificate::where('teacher_id', $user->id)->count();

                $panel = array_merge($panel, [
                    'gradeLabel' => $user->isAdmin() ? __('messages.admin_role_admin') : __('messages.nav_teacher_cabinet'),
                    'levelLabel' => $user->isAdmin() ? __('messages.admin_manage_content') : __('messages.teacher_progress'),
                    'progressLabel' => __('messages.nav_teacher_summary', ['count' => $teacherCertificates]),
                    'notificationsCount' => $teacherCertificates,
                    'notificationsRoute' => route('teacher.certificates.index'),
                ]);
            }

            $view->with('navPanel', $panel);
        });
    }
}
