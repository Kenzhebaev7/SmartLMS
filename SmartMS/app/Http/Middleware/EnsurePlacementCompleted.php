<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePlacementCompleted
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        if ($user->role !== \App\Models\User::ROLE_STUDENT) {
            return $next($request);
        }

        if ($user->level === null || $user->level === 'none') {
            if ($request->routeIs('placement-test.*')) {
                return $next($request);
            }
            return redirect()->route('placement-test.show');
        }

        return $next($request);
    }
}
