<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTeacherOrAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user || (!$user->isTeacher() && !$user->isAdmin())) {
            abort(403, __('messages.forbidden_teacher'));
        }

        return $next($request);
    }
}
