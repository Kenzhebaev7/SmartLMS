<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class SectionController extends Controller
{
    public function dashboard(Request $request): View
    {
        $user = $request->user();
        $sections = self::sectionsForUser($user);
        $unlocked = self::unlockedSectionIds($user);

        $progressBySection = [];
        $sectionsPassedCount = 0;
        $recommendedSectionId = null;
        if ($user && $user->role === \App\Models\User::ROLE_STUDENT) {
            $sections->load('lessons');
            $lessonIdsBySection = $sections->keyBy('id')->map(fn ($s) => $s->lessons->pluck('id')->all());
            $userProgress = \App\Models\LessonProgress::where('user_id', $user->id)->get();
            foreach ($sections as $section) {
                $ids = $lessonIdsBySection->get($section->id, []);
                $total = count($ids);
                $completed = $userProgress->filter(function ($p) use ($ids) {
                    return in_array($p->lesson_id, $ids) || in_array((int) $p->lesson_key, $ids);
                })->count();
                $percent = $total > 0 ? round((min($completed, $total) / $total) * 100) : 0;
                $progressBySection[$section->id] = ['percent' => $percent, 'completed' => $completed, 'total' => $total];
                if (in_array($section->id, $unlocked) && $section->quiz) {
                    $passed = $user->results()->where('quiz_id', $section->quiz->id)->where('passed', true)->exists();
                    if ($passed) {
                        $sectionsPassedCount++;
                    } elseif ($recommendedSectionId === null) {
                        $recommendedSectionId = $section->id;
                    }
                } elseif ($recommendedSectionId === null && in_array($section->id, $unlocked)) {
                    $recommendedSectionId = $section->id;
                }
            }
            // Рекомендуем только если есть что проходить (не все разделы сданы)
            if ($sections->isNotEmpty() && $sectionsPassedCount >= $sections->count()) {
                $recommendedSectionId = null;
            }
        }

        return view('dashboard', [
            'sections' => $sections,
            'unlockedSectionIds' => $unlocked,
            'progressBySection' => $progressBySection,
            'sectionsTotal' => $sections->count(),
            'sectionsPassedCount' => $sectionsPassedCount,
            'recommendedSectionId' => $recommendedSectionId,
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
            $lessonIdsBySection = $sections->keyBy('id')->map(fn ($s) => $s->lessons->pluck('id')->all());
            $userProgress = \App\Models\LessonProgress::where('user_id', $user->id)->get();
            foreach ($sections as $section) {
                $ids = $lessonIdsBySection->get($section->id, []);
                $total = count($ids);
                $completed = $userProgress->filter(fn ($p) => in_array($p->lesson_id, $ids) || in_array((int) $p->lesson_key, $ids))->count();
                $percent = $total > 0 ? round((min($completed, $total) / $total) * 100) : 0;
                $progressBySection[$section->id] = ['percent' => $percent, 'completed' => $completed, 'total' => $total];
            }
        }

        return view('sections.index', [
            'sections' => $sections,
            'unlockedSectionIds' => $unlocked,
            'progressBySection' => $progressBySection,
        ]);
    }

    public static function sectionsForUser($user)
    {
        $query = Section::with('quiz')->orderedForDisplay();
        if ($user && $user->role === \App\Models\User::ROLE_STUDENT) {
            $query->forGrade($user->effectiveGradeForStudent());
            if ($user->placement_passed === false) {
                $query->revisionOnly();
            } else {
                $query->mainOnly();
            }
        }
        return self::dedupeSectionsForDisplay($query->get());
    }

    /**
     * Один раздел на пару (title + grade), чтобы после повторных сидов не дублировались карточки.
     */
    public static function dedupeSectionsForDisplay(Collection $sections): Collection
    {
        $deduped = $sections
            ->sortBy([
                fn (Section $s) => $s->is_featured ? 0 : 1,
                fn (Section $s) => $s->order,
                fn (Section $s) => $s->id,
            ])
            ->values()
            ->unique(fn (Section $s) => $s->dedupeKey())
            ->values();

        return Section::withProjectActivityFirst($deduped);
    }

    public function show(Request $request, Section $section): Response
    {
        $user = $request->user();
        if ($user && $user->role === \App\Models\User::ROLE_STUDENT) {
            $ug = $user->effectiveGradeForStudent();
            if ($section->grade !== null && (int) $section->grade !== $ug) {
                abort(403, __('messages.sections_forbidden_grade'));
            }
        }
        $unlocked = self::unlockedSectionIds($user);
        if (!in_array($section->id, $unlocked)) {
            abort(403, __('messages.sections_forbidden_quiz'));
        }

        $section->load('quiz');
        $section->setRelation('lessons', $section->lessons()->orderBy('order')->get());
        $isMaster = $request->user()->sectionMasters()->where('section_id', $section->id)->exists();

        $sectionPassed = false;
        $nextSection = null;
        if ($user && $user->role === \App\Models\User::ROLE_STUDENT && $section->quiz) {
            $sectionPassed = $user->results()->where('quiz_id', $section->quiz->id)->where('passed', true)->exists();
            if ($sectionPassed) {
                $ordered = self::sectionsForUser($user);
                $idx = $ordered->search(fn ($s) => (int) $s->id === (int) $section->id);
                if ($idx !== false && $ordered->has($idx + 1)) {
                    $nextSection = $ordered->get($idx + 1);
                }
            }
        }

        return response()
            ->view('sections.show', [
                'section' => $section,
                'isMaster' => $isMaster,
                'sectionPassed' => $sectionPassed,
                'nextSection' => $nextSection,
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    /**
     * Список ID разделов, доступных ученику: поэтапная разблокировка.
     * Первый раздел по порядку всегда открыт; следующий открывается только после
     * успешной сдачи квиза предыдущего раздела.
     */
    public static function unlockedSectionIds($user): array
    {
        if (!$user || $user->role !== \App\Models\User::ROLE_STUDENT) {
            return Section::pluck('id')->all();
        }

        $query = Section::query()->with('quiz')->orderedForDisplay();
        $query->forGrade($user->effectiveGradeForStudent());
        if ($user->placement_passed === false) {
            $query->revisionOnly();
        } else {
            $query->mainOnly();
        }
        $sections = self::dedupeSectionsForDisplay($query->get());
        $unlocked = [];
        foreach ($sections as $section) {
            if (count($unlocked) === 0) {
                // Первый раздел по очереди всегда открыт
                $unlocked[] = $section->id;
                continue;
            }
            // Предыдущий по порядку раздел (последний уже открытый)
            $prevSection = $sections->where('id', end($unlocked))->first();
            $prevQuiz = $prevSection?->quiz;
            if (!$prevQuiz) {
                // У предыдущего раздела нет квиза — следующий открыт (например, только уроки)
                $unlocked[] = $section->id;
                continue;
            }
            $passed = $user->results()->where('quiz_id', $prevQuiz->id)->where('passed', true)->exists();
            if ($passed) {
                $unlocked[] = $section->id;
            }
        }
        return $unlocked;
    }
}
