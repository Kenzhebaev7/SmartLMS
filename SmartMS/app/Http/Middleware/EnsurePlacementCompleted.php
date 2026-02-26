<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePlacementCompleted
{
    /**
     * Handle an incoming request.
     *
     * If the user's level is not set, redirect them to the placement test.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->level === null) {
            return redirect()->route('test.show');
        }

        return $next($request);
    }
}

