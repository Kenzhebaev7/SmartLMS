<?php

namespace App\Http\Controllers;

use App\Models\LessonProgress;
use App\Models\Lesson;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class SectionController extends Controller
{
    public static function completedLessonIdsForStudent(?User $user): array
    {
        if (!$user || $user->role !== User::ROLE_STUDENT) {
            return [];
        }

        return LessonProgress::where('user_id', $user->id)
            ->get()
            ->flatMap(function ($progress) {
                return array_filter([
                    $progress->lesson_id ? (int) $progress->lesson_id : null,
                    is_numeric($progress->lesson_key) ? (int) $progress->lesson_key : null,
                ]);
            })
            ->unique()
            ->values()
            ->all();
    }

    public static function isLessonCompletedByStudent(?User $user, Lesson $lesson, ?array $completedLessonIds = null): bool
    {
        if (!$user || $user->role !== User::ROLE_STUDENT) {
            return false;
        }

        $completedLessonIds ??= self::completedLessonIdsForStudent($user);

        return in_array((int) $lesson->id, $completedLessonIds, true);
    }

    public static function isLessonUnlockedForStudent(?User $user, Lesson $lesson, ?array $completedLessonIds = null): bool
    {
        if (!$user || $user->role !== User::ROLE_STUDENT) {
            return true;
        }

        if (!$lesson->unlock_after_lesson_id) {
            return true;
        }

        $completedLessonIds ??= self::completedLessonIdsForStudent($user);

        return in_array((int) $lesson->unlock_after_lesson_id, $completedLessonIds, true);
    }

    public static function lessonStatusesForStudent(?User $user, Section $section): array
    {
        $lessons = Lesson::dedupeForDisplay($section->lessons ?? collect());
        $completedLessonIds = self::completedLessonIdsForStudent($user);
        $statuses = [];

        foreach ($lessons as $lesson) {
            $statuses[$lesson->id] = [
                'completed' => self::isLessonCompletedByStudent($user, $lesson, $completedLessonIds),
                'unlocked' => self::isLessonUnlockedForStudent($user, $lesson, $completedLessonIds),
                'unlock_after_lesson' => $lesson->unlockAfterLesson,
            ];
        }

        return $statuses;
    }

    public static function lessonProgressMetaForStudent(?User $user, Collection $sections): array
    {
        if (!$user || $user->role !== User::ROLE_STUDENT) {
            return [];
        }

        $completedLessonIds = self::completedLessonIdsForStudent($user);

        $progressBySection = [];
        foreach ($sections as $section) {
            $dedupedLessons = Lesson::dedupeForDisplay($section->lessons ?? collect());
            $total = $dedupedLessons->count();
            $completed = 0;

            foreach ($dedupedLessons as $lesson) {
                $sameLessons = ($section->lessons ?? collect())->filter(fn (Lesson $candidate) => $candidate->dedupeKey() === $lesson->dedupeKey());
                $lessonIds = $sameLessons->pluck('id')->all();
                $hasCompleted = collect($lessonIds)->contains(fn ($lessonId) => in_array((int) $lessonId, $completedLessonIds, true));

                if ($hasCompleted) {
                    $completed++;
                }
            }

            $progressBySection[$section->id] = [
                'percent' => $total > 0 ? round((min($completed, $total) / $total) * 100) : 0,
                'completed' => $completed,
                'total' => $total,
            ];
        }

        return $progressBySection;
    }

    public static function isSectionCompletedByStudent(?User $user, Section $section, ?array $progressMeta = null): bool
    {
        if (!$user || $user->role !== User::ROLE_STUDENT) {
            return false;
        }

        if ($section->quiz) {
            return $user->results()
                ->where('quiz_id', $section->quiz->id)
                ->where('passed', true)
                ->exists();
        }

        if ($progressMeta === null) {
            $progressMeta = self::lessonProgressMetaForStudent($user, collect([$section]))[$section->id] ?? null;
        }

        if (!$progressMeta) {
            return false;
        }

        $total = (int) ($progressMeta['total'] ?? 0);
        $completed = (int) ($progressMeta['completed'] ?? 0);

        return $total > 0 && $completed >= $total;
    }

    public function dashboard(Request $request): View
    {
        $user = $request->user();
        $sections = self::sectionsForUser($user);
        $unlocked = self::unlockedSectionIds($user);

        $progressBySection = [];
        $sectionsPassedCount = 0;
        $recommendedSectionId = null;

        if ($user && $user->role === User::ROLE_STUDENT) {
            $sections->load('lessons', 'quiz');
            $sections->each(fn (Section $section) => $section->setRelation('lessons', Lesson::dedupeForDisplay($section->lessons)));
            $progressBySection = self::lessonProgressMetaForStudent($user, $sections);

            foreach ($sections as $section) {
                if (!in_array($section->id, $unlocked, true)) {
                    continue;
                }

                $isCompleted = self::isSectionCompletedByStudent($user, $section, $progressBySection[$section->id] ?? null);

                if ($isCompleted) {
                    $sectionsPassedCount++;
                } elseif ($recommendedSectionId === null) {
                    $recommendedSectionId = $section->id;
                }
            }

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
        if ($user && $user->role === User::ROLE_STUDENT) {
            $sections->load('lessons', 'quiz');
            $sections->each(fn (Section $section) => $section->setRelation('lessons', Lesson::dedupeForDisplay($section->lessons)));
            $progressBySection = self::lessonProgressMetaForStudent($user, $sections);
        }

        return view('sections.index', [
            'sections' => $sections,
            'unlockedSectionIds' => $unlocked,
            'progressBySection' => $progressBySection,
        ]);
    }

    public static function sectionsForUser($user): Collection
    {
        $query = Section::with('quiz')->orderedForDisplay();

        if ($user && $user->role === User::ROLE_STUDENT) {
            $query->forGrade($user->effectiveGradeForStudent());

            if ($user->placement_passed === false) {
                $query->revisionOnly();
            } else {
                $query->mainOnly();
            }
        }

        return self::dedupeSectionsForDisplay($query->get());
    }

    public static function dedupeSectionsForDisplay(Collection $sections): Collection
    {
        $deduped = $sections
            ->sortBy([
                fn (Section $section) => $section->is_featured ? 0 : 1,
                fn (Section $section) => $section->order,
                fn (Section $section) => $section->id,
            ])
            ->values()
            ->unique(fn (Section $section) => $section->dedupeKey())
            ->values();

        return Section::withProjectActivityFirst($deduped);
    }

    public static function dedupeSectionsForForum(Collection $sections): Collection
    {
        return self::dedupeSectionsForDisplay($sections)
            ->unique(fn (Section $section) => Section::normalizeTitleString((string) ($section->title ?? '')))
            ->values();
    }

    public function show(Request $request, Section $section): Response
    {
        $user = $request->user();

        if ($user && $user->role === User::ROLE_STUDENT) {
            $grade = $user->effectiveGradeForStudent();
            if ($section->grade !== null && (int) $section->grade !== $grade) {
                abort(403, __('messages.sections_forbidden_grade'));
            }
        }

        $unlocked = self::unlockedSectionIds($user);
        if (!in_array($section->id, $unlocked, true)) {
            abort(403, __('messages.sections_forbidden_quiz'));
        }

        $section->load('quiz');
        $section->setRelation('lessons', Lesson::dedupeForDisplay($section->lessons()->with('unlockAfterLesson')->orderBy('order')->get()));
        $isMaster = $request->user()->sectionMasters()->where('section_id', $section->id)->exists();
        $lessonStatuses = self::lessonStatusesForStudent($user, $section);

        $sectionPassed = false;
        $nextSection = null;

        if ($user && $user->role === User::ROLE_STUDENT) {
            $progressMeta = self::lessonProgressMetaForStudent($user, collect([$section]));
            $sectionPassed = self::isSectionCompletedByStudent($user, $section, $progressMeta[$section->id] ?? null);

            if ($sectionPassed) {
                $ordered = self::sectionsForUser($user);
                $index = $ordered->search(fn (Section $orderedSection) => (int) $orderedSection->id === (int) $section->id);

                if ($index !== false && $ordered->has($index + 1)) {
                    $nextSection = $ordered->get($index + 1);
                }
            }
        }

        return response()
            ->view('sections.show', [
                'section' => $section,
                'isMaster' => $isMaster,
                'lessonStatuses' => $lessonStatuses,
                'sectionPassed' => $sectionPassed,
                'nextSection' => $nextSection,
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    public static function unlockedSectionIds($user): array
    {
        if (!$user || $user->role !== User::ROLE_STUDENT) {
            return Section::pluck('id')->all();
        }

        $query = Section::query()->with(['quiz', 'lessons'])->orderedForDisplay();
        $query->forGrade($user->effectiveGradeForStudent());

        if ($user->placement_passed === false) {
            $query->revisionOnly();
        } else {
            $query->mainOnly();
        }

        $sections = self::dedupeSectionsForDisplay($query->get());
        $progressBySection = self::lessonProgressMetaForStudent($user, $sections);
        $unlocked = [];

        foreach ($sections as $section) {
            if ($unlocked === []) {
                $unlocked[] = $section->id;
                continue;
            }

            $prevSection = $sections->where('id', end($unlocked))->first();
            if (!$prevSection) {
                continue;
            }

            $prevCompleted = self::isSectionCompletedByStudent($user, $prevSection, $progressBySection[$prevSection->id] ?? null);
            if ($prevCompleted) {
                $unlocked[] = $section->id;
            }
        }

        return $unlocked;
    }
}
