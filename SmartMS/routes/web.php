<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PlacementTestController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\Teacher\TeacherDashboardController;
use App\Http\Controllers\Teacher\SectionController as TeacherSectionController;
use App\Http\Controllers\Teacher\LessonController as TeacherLessonController;
use App\Http\Controllers\Teacher\QuizController as TeacherQuizController;
use App\Http\Controllers\Teacher\StudentProgressController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch')->where('locale', 'kk|ru');

Route::get('/', function () {
    $locale = session('locale', request()->cookie('locale', config('app.locale')));
    if (!in_array($locale, \App\Http\Middleware\SetLocale::LOCALES, true)) {
        $locale = 'kk';
    }
    \Illuminate\Support\Facades\App::setLocale($locale);
    return view('welcome');
})->middleware('web');

Route::middleware(['auth'])->group(function () {
    Route::get('/placement-test', [PlacementTestController::class, 'show'])->name('placement-test.show');
    Route::post('/placement-test', [PlacementTestController::class, 'process'])->name('placement-test.process');
});

Route::middleware(['auth', 'ensure.placement.completed'])->group(function () {
    Route::get('/dashboard', [SectionController::class, 'dashboard'])->name('dashboard');

    Route::get('/sections', [SectionController::class, 'index'])->name('sections.index');
    Route::get('/sections/{section}', [SectionController::class, 'show'])->name('sections.show');

    Route::get('/sections/{section}/lessons/{lesson}', [LessonController::class, 'show'])->name('lessons.show');
    Route::post('/sections/{section}/lessons/{lesson}/questions', [LessonController::class, 'storeQuestion'])->name('lessons.questions.store');
    Route::post('/sections/{section}/lessons/{lesson}/complete', [LessonController::class, 'complete'])->name('lessons.complete');

    Route::get('/sections/{section}/quiz', [QuizController::class, 'show'])->name('quiz.show');
    Route::post('/sections/{section}/quiz', [QuizController::class, 'submit'])->name('quiz.submit');
});

Route::middleware(['auth', 'teacher.or.admin'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
    Route::resource('sections', TeacherSectionController::class);
    Route::resource('sections.lessons', TeacherLessonController::class)->shallow();
    Route::get('sections/{section}/quiz', [TeacherQuizController::class, 'edit'])->name('sections.quiz.edit');
    Route::put('sections/{section}/quiz', [TeacherQuizController::class, 'update'])->name('sections.quiz.update');
    Route::get('progress', [StudentProgressController::class, 'index'])->name('progress.index');
    Route::post('progress/master', [StudentProgressController::class, 'assignMaster'])->name('progress.master');
    Route::post('progress/feedback', [StudentProgressController::class, 'storeFeedback'])->name('progress.feedback');
    Route::post('students/{student}/achievements', [StudentProgressController::class, 'awardAchievement'])->name('students.achievements.award');
});

Route::middleware(['auth', 'ensure.placement.completed'])->group(function () {
    Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
    Route::get('/forum/create', [ForumController::class, 'create'])->name('forum.create');
    Route::post('/forum', [ForumController::class, 'store'])->name('forum.store');
    Route::get('/forum/{thread}', [ForumController::class, 'show'])->name('forum.show');
    Route::post('/forum/{thread}/comments', [ForumController::class, 'storeComment'])->name('forum.comments.store');
});

Route::middleware('auth')->group(function () {
    Route::delete('/forum/threads/{thread}', [ForumController::class, 'destroyThread'])->name('forum.threads.destroy');
    Route::delete('/forum/comments/{comment}', [ForumController::class, 'destroyComment'])->name('forum.comments.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.updateRole');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
