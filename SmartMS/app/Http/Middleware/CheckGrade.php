<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckGrade
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user || $user->role !== \App\Models\User::ROLE_STUDENT) {
            return $next($request);
        }

        $grade = $user->effectiveGradeForStudent();
        if ($grade === null) {
            return $next($request);
        }

        $route = $request->route();
        $section = $route?->parameter('section');
        if ($section instanceof \App\Models\Section && $section->grade !== null && (int) $section->grade !== $grade) {
            abort(403, __('messages.sections_forbidden_grade'));
        }

        $lesson = $route?->parameter('lesson');
        if ($lesson instanceof \App\Models\Lesson) {
            $lessonGrade = $lesson->grade ?? $lesson->section?->grade;
            if ($lessonGrade !== null && (int) $lessonGrade !== $grade) {
                abort(403, __('messages.sections_forbidden_grade'));
            }
        }

        return $next($request);
    }
}
