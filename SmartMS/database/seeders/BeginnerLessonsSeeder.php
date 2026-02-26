<?php

namespace Database\Seeders;

use App\Models\Section;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class BeginnerLessonsSeeder extends Seeder
{
    public function run(): void
    {
        $section = Section::firstOrCreate(
            ['title' => 'Основы Информатики'],
            [
                'description' => 'Вводный раздел: информация, данные и основы работы с ними.',
                'order' => 0,
                'level' => 'beginner',
            ]
        );

        Lesson::updateOrCreate(
            [
                'section_id' => $section->id,
                'title' => '1.1 Информация и данные',
            ],
            [
                'content' => "В этом уроке разбираем фундаментальные понятия: что такое информация, чем она отличается от данных и как компьютер обрабатывает эти знания.\n\nИнформация — сведения об окружающем мире. Данные — информация в формальном виде (числа, символы, коды). Единицы измерения информации: бит (0 или 1), байт (8 бит), килобайт, мегабайт. Это база, необходимая перед началом изучения программирования и алгоритмов.",
                'video_url' => 'https://www.youtube.com/watch?v=t-JRMxluNz8',
                'video_id' => 't-JRMxluNz8',
                'file_path' => null,
                'is_advanced' => false,
                'order' => 1,
            ]
        );
    }
}
