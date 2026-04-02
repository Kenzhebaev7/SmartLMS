<?php

namespace App\Services;

use App\Models\LessonProgress;
use App\Models\QuestionResult;
use App\Models\Result;
use App\Models\Section;
use Illuminate\Support\Facades\DB;

/**
 * Удаление дублей разделов (одинаковый курс по смыслу) и нормализация title.
 * Используется сидером, миграцией и artisan-командой.
 */
class SectionDuplicateCleanup
{
    public function run(): void
    {
        $this->trimSectionTitlesInDatabase();
        $this->removeDuplicateSectionsByTitleGrade();
        $this->fixProjectActivitySectionOrder();
    }

    /** После удаления дублей оставшийся раздел мог иметь большой order — возвращаем в начало курса. */
    public function fixProjectActivitySectionOrder(): void
    {
        foreach ([9, 10, 11] as $grade) {
            $section = Section::where('grade', $grade)
                ->get()
                ->first(fn (Section $s) => Section::looksLikeProjectActivitySection($s));
            if ($section) {
                $section->update([
                    'order' => $grade * 100 + 1,
                    'is_featured' => true,
                ]);
            }
        }
    }

    public function trimSectionTitlesInDatabase(): void
    {
        Section::query()->orderBy('id')->chunkById(100, function ($sections) {
            foreach ($sections as $section) {
                $clean = preg_replace('/\s+/u', ' ', trim((string) $section->title));
                if ($clean !== $section->title) {
                    $section->update(['title' => $clean]);
                }
            }
        });
    }

    public function removeDuplicateSectionsByTitleGrade(): void
    {
        foreach ([9, 10, 11] as $grade) {
            $sections = Section::where('grade', $grade)->orderBy('id')->get();
            $byKey = $sections->groupBy(fn (Section $s) => $s->dedupeKey());
            foreach ($byKey as $group) {
                if ($group->count() <= 1) {
                    continue;
                }
                $keeper = $group->sortBy([
                    fn (Section $s) => $s->is_featured ? 0 : 1,
                    fn (Section $s) => $s->order,
                    fn (Section $s) => $s->id,
                ])->first();
                foreach ($group as $dup) {
                    if ((int) $dup->id !== (int) $keeper->id) {
                        $this->deleteSectionAndRelatedData($dup);
                    }
                }
            }
        }
    }

    private function deleteSectionAndRelatedData(Section $section): void
    {
        DB::transaction(function () use ($section) {
            $section->load(['lessons']);
            $lessonIds = $section->lessons->pluck('id')->filter()->all();
            if ($lessonIds !== []) {
                LessonProgress::whereIn('lesson_id', $lessonIds)->delete();
            }
            if ($quiz = $section->quiz()->first()) {
                $qid = $quiz->id;
                QuestionResult::where('quiz_id', $qid)->delete();
                Result::where('quiz_id', $qid)->delete();
                $quiz->questions()->delete();
                $quiz->delete();
            }
            $section->lessons()->delete();
            $section->masters()->detach();
            $section->delete();
        });
    }
}
