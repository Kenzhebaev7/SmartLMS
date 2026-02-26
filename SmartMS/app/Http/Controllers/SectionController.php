<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class SectionController extends Controller
{
    public function dashboard(Request $request): View
    {
        $user = $request->user();
        $sections = self::sectionsForUser($user);
        $unlocked = self::unlockedSectionIds($user);

        $progressBySection = [];
        if ($user && $user->role === \App\Models\User::ROLE_STUDENT) {
            $sections->load('lessons');
            $lessonIdsBySection = $sections->keyBy('id')->map(fn ($s) => $s->lessons->filter(fn ($l) => $user->level !== 'beginner' || !$l->is_advanced)->pluck('id')->all());
            $userProgress = \App\Models\LessonProgress::where('user_id', $user->id)->get();
            foreach ($sections as $section) {
                $ids = $lessonIdsBySection->get($section->id, []);
                $total = count($ids);
                $completed = $userProgress->filter(function ($p) use ($ids) {
                    return in_array($p->lesson_id, $ids) || in_array((int) $p->lesson_key, $ids);
                })->count();
                $progressBySection[$section->id] = $total > 0 ? round((min($completed, $total) / $total) * 100) : 0;
            }
        }

        return view('dashboard', [
            'sections' => $sections,
            'unlockedSectionIds' => $unlocked,
            'progressBySection' => $progressBySection,
        ]);
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $sections = self::sectionsForUser($user);
        $unlocked = self::unlockedSectionIds($user);

        $progressBySection = [];
        if ($user && $user->role === \App\Models\User::ROLE_STUDENT) {
            $sections->load('lessons');
            $lessonIdsBySection = $sections->keyBy('id')->map(fn ($s) => $s->lessons->filter(fn ($l) => $user->level !== 'beginner' || !$l->is_advanced)->pluck('id')->all());
            $userProgress = \App\Models\LessonProgress::where('user_id', $user->id)->get();
            foreach ($sections as $section) {
                $ids = $lessonIdsBySection->get($section->id, []);
                $total = count($ids);
                $completed = $userProgress->filter(fn ($p) => in_array($p->lesson_id, $ids) || in_array((int) $p->lesson_key, $ids))->count();
                $progressBySection[$section->id] = $total > 0 ? round((min($completed, $total) / $total) * 100) : 0;
            }
        }

        return view('sections.index', [
            'sections' => $sections,
            'unlockedSectionIds' => $unlocked,
            'progressBySection' => $progressBySection,
        ]);
    }

    private static function sectionsForUser($user)
    {
        $query = Section::with('quiz')->orderBy('order');
        if ($user && $user->role === \App\Models\User::ROLE_STUDENT && $user->level) {
            $query->forLevel($user->level);
        }
        return $query->get();
    }

    public function show(Request $request, Section $section): Response
    {
        $user = $request->user();
        if ($user && $user->role === \App\Models\User::ROLE_STUDENT && $section->level && $section->level !== $user->level) {
            abort(403, __('sections.forbidden_level'));
        }
        $unlocked = self::unlockedSectionIds($user);
        if (!in_array($section->id, $unlocked)) {
            abort(403, __('sections.forbidden_quiz'));
        }

        $lessonsQuery = $section->lessons()->orderBy('order');
        if ($user && $user->role === \App\Models\User::ROLE_STUDENT && $user->level === 'beginner') {
            $lessonsQuery->where('is_advanced', false);
        }
        $section->setRelation('lessons', $lessonsQuery->get());
        $isMaster = $request->user()->sectionMasters()->where('section_id', $section->id)->exists();

        return response()
            ->view('sections.show', [
                'section' => $section,
                'isMaster' => $isMaster,
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    public static function unlockedSectionIds($user): array
    {
        if (!$user || $user->role !== \App\Models\User::ROLE_STUDENT) {
            return Section::pluck('id')->all();
        }

        $sections = Section::forLevel($user->level)->orderBy('order')->get();
        $unlocked = [];
        foreach ($sections as $section) {
            if (count($unlocked) === 0) {
                $unlocked[] = $section->id;
                continue;
            }
            $prevSection = $sections->where('id', end($unlocked))->first();
            $quiz = $prevSection?->quiz;
            if (!$quiz) {
                $unlocked[] = $section->id;
                continue;
            }
            $passed = $user->results()->where('quiz_id', $quiz->id)->where('passed', true)->exists();
            if ($passed) {
                $unlocked[] = $section->id;
            }
        }
        return $unlocked;
    }
}
