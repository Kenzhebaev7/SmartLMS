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

        // Старые пользователи без выбранного класса: заставляем выбрать класс
        if ($user->grade === null) {
            // Разрешаем заходить в профиль, чтобы выбрать класс
            if ($request->routeIs('profile.*')) {
                return $next($request);
            }
            return redirect()->route('profile.edit')
                ->with('error', __('messages.placement_need_grade'));
        }

        // Новый пользователь: класс выбран, но вступительный тест ещё не пройден
        if ($user->placement_passed === null && $user->grade !== null) {
            if ($request->routeIs('placement-test.*')) {
                return $next($request);
            }
            return redirect()->route('placement-test.show');
        }

        return $next($request);
    }
}
