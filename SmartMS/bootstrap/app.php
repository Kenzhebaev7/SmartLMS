<?php

use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsurePlacementCompleted;
use App\Http\Middleware\EnsureTeacher;
use App\Http\Middleware\EnsureTeacherOrAdmin;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [SetLocale::class]);
        $middleware->alias([
            'admin' => EnsureAdmin::class,
            'ensure.placement.completed' => EnsurePlacementCompleted::class,
            'teacher' => EnsureTeacher::class,
            'teacher.or.admin' => EnsureTeacherOrAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
