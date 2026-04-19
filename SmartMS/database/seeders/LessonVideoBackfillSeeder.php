<?php

namespace Database\Seeders;

use App\Models\Lesson;
use Illuminate\Database\Seeder;

class LessonVideoBackfillSeeder extends Seeder
{
    /**
     * Stable public YouTube video IDs reused across lessons so every lesson
     * has an embeddable fallback instead of an empty or broken player.
     */
    private array $videoPool = [
        't-JRMxluNz8',
        '2VBw9dX3L8E',
        '8WEtxJ4-sh4',
        '4_4RnpL6f_A',
        'J5bQ8c4ofx8',
        '1OVVvHVrF0s',
        'W8F9_2OHq2A',
        'HXV3zeQKqGY',
        '1uqci6BXLM8',
        '3VnrAJnQp2c',
        'PlxWf493en4',
    ];

    public function run(): void
    {
        $lessons = Lesson::with('section')->orderBy('id')->get();

        foreach ($lessons as $lesson) {
            $grade = (int) ($lesson->section->grade ?? $lesson->grade ?? 9);
            $revisionBias = $lesson->section?->is_revision ? 0 : 5;
            $index = ($grade * 3 + (int) $lesson->order + $revisionBias) % count($this->videoPool);
            $videoId = $this->videoPool[$index];
            $videoUrl = 'https://www.youtube.com/watch?v='.$videoId;

            $lesson->update([
                'video_id' => $videoId,
                'video_url' => $videoUrl,
                'video_id_kk' => $lesson->video_id_kk ?: $videoId,
                'video_url_kk' => $lesson->video_url_kk ?: $videoUrl,
            ]);
        }
    }
}
