<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PlacementController;
use App\Http\Controllers\LessonsController;
use App\Http\Controllers\LessonProgressController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    $completedLessonsCount = $user?->lessonProgresses()->count() ?? 0;
    $totalLessons = 4;
    $progress = $totalLessons > 0 ? (int) round(($completedLessonsCount / $totalLessons) * 100) : 0;

    return view('dashboard', [
        'progress' => $progress,
        'completedLessonsCount' => $completedLessonsCount,
        'totalLessons' => $totalLessons,
    ]);
})->middleware(['auth', 'verified', 'ensure.placement.completed'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/test', [PlacementController::class, 'showTest'])->name('test.show');
    Route::post('/test', [PlacementController::class, 'processTest'])->name('test.process');

    Route::get('/lessons', [LessonsController::class, 'index'])->name('lessons.index');
    Route::post('/lessons/complete', [LessonProgressController::class, 'store'])->name('lessons.complete');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
