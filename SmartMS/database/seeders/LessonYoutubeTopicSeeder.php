<?php

namespace Database\Seeders;

use App\Models\Lesson;
use Illuminate\Database\Seeder;

class LessonYoutubeTopicSeeder extends Seeder
{
    /**
     * Curated YouTube videos verified via recent search results.
     * They are reused by topic so lessons get relevant and currently available videos.
     */
    private const VIDEO_MAP = [
        'sql' => 'HXV3zeQKqGY',
        'cpp' => 'ZzaPdXTrSb8',
        'algorithms' => '8hly31xKli0',
        'binary' => 'ry1hpm1GXVI',
        'html' => 'qz0aGYrrlhU',
        'internet' => '4DlUO7K7SNU',
        'project' => '2t4h6t4r0jc',
        'generic' => '8hly31xKli0',
    ];

    public function run(): void
    {
        Lesson::query()->with('section:id,title,title_kk')->each(function (Lesson $lesson): void {
            $videoId = $this->resolveVideoId($lesson);
            $videoUrl = 'https://www.youtube.com/watch?v=' . $videoId;

            $lesson->update([
                'video_id' => $videoId,
                'video_url' => $videoUrl,
                'video_id_kk' => $videoId,
                'video_url_kk' => $videoUrl,
            ]);
        });
    }

    private function resolveVideoId(Lesson $lesson): string
    {
        $haystack = mb_strtolower(trim(implode(' ', array_filter([
            $lesson->title,
            $lesson->title_kk,
            $lesson->section?->title,
            $lesson->section?->title_kk,
        ]))), 'UTF-8');

        $topicRules = [
            'sql' => ['sql', 'база данных', 'базы данных', 'деректер қоры', 'деректер қор', 'database', 'таблиц', 'table', 'select', 'insert', 'update', 'delete', 'query', 'сұрау'],
            'cpp' => ['c++', 'функц', 'параметр', 'айнымалы', 'переменн', 'тип', 'programming', 'бағдарламалау', 'цикл', 'услов', 'ветвлен', 'while', 'for', 'if', 'else'],
            'binary' => ['система счисления', 'санау жүйе', 'binary', 'двоич', 'восьмер', 'шестнадцатер', 'кодирован', 'кодтау', 'единицы измерения'],
            'html' => ['html', 'css', 'web', 'веб', 'браузер', 'страниц', 'бет', 'графика', 'визуал', 'медиа', 'цифр'],
            'internet' => ['интернет', 'internet', 'безопас', 'қауіпсіз', 'файл', 'папк', 'folder', 'онлайн'],
            'project' => ['проект', 'жоба', 'presentation', 'презентац', 'sprint', 'спринт', 'тестирован', 'отладк', 'защита', 'этап', 'план', 'практика', 'практикум'],
            'algorithms' => ['алгорит', 'algorithm', 'логик', 'logic', 'блок-схем', 'орындаушы', 'исполнител', 'деректер құрылым', 'структур данных', 'data structures'],
        ];

        foreach ($topicRules as $topic => $needles) {
            foreach ($needles as $needle) {
                if (str_contains($haystack, $needle)) {
                    return self::VIDEO_MAP[$topic];
                }
            }
        }

        return self::VIDEO_MAP['generic'];
    }
}
