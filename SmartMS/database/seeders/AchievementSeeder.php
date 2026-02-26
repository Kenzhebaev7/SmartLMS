<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['key' => 'placement_done', 'name' => 'Старт', 'description' => 'Пройден вступительный тест', 'xp' => 10],
            ['key' => 'first_quiz_pass', 'name' => 'Первый квиз', 'description' => 'Первый раз прошли квиз на проходной балл', 'xp' => 20],
            ['key' => 'first_thread', 'name' => 'Автор темы', 'description' => 'Создали первую тему на форуме', 'xp' => 15],
            ['key' => 'first_comment', 'name' => 'Первый комментарий', 'description' => 'Оставили первый комментарий', 'xp' => 5],
        ];
        foreach ($items as $item) {
            Achievement::firstOrCreate(['key' => $item['key']], $item);
        }
    }
}
