<?php

namespace Database\Seeders;

use App\Models\Section;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->section1();
        $this->section2();
    }

    private function section1(): void
    {
        $section = Section::firstOrCreate(
            ['title' => 'Основы Информатики'],
            [
                'description' => 'Вводный раздел: информация, данные, разделы компьютерных наук.',
                'order' => 1,
                'level' => 'beginner',
            ]
        );

        Lesson::updateOrCreate(
            ['section_id' => $section->id, 'title' => 'Информация и данные'],
            [
                'content' => "Информация — это сведения об объектах и явлениях, которые мы получаем и обрабатываем. Данные — информация, представленная в формальном виде (числа, символы, коды), пригодном для хранения и обработки на компьютере.\n\nЧеловек воспринимает информацию через органы чувств; компьютер работает только с данными. Единицы измерения: бит, байт, килобайт, мегабайт. Кодирование превращает информацию в данные.",
                'video_id' => 't-JRMxluNz8',
                'video_url' => 'https://www.youtube.com/watch?v=t-JRMxluNz8',
                'is_advanced' => false,
                'order' => 1,
            ]
        );

        Lesson::updateOrCreate(
            ['section_id' => $section->id, 'title' => 'Разделы компьютерных наук'],
            [
                'content' => "Информатика — наука о методах сбора, хранения, обработки и передачи информации. IT (информационные технологии) — применение техники и программ в жизни и бизнесе. Программирование — создание программ, одна из областей информатики.\n\nРазделы компьютерных наук: алгоритмы, программирование, базы данных, сети, безопасность, искусственный интеллект. Все они опираются на основы информатики и логику.",
                'video_id' => '1OVVvHVrF0s',
                'video_url' => 'https://www.youtube.com/watch?v=1OVVvHVrF0s',
                'is_advanced' => false,
                'order' => 2,
            ]
        );

        $quiz = $section->quiz()->firstOrCreate(
            [],
            ['title' => 'Квиз: Основы Информатики', 'passing_percent' => 70]
        );
        if ($quiz->questions()->count() === 0) {
            $quiz->questions()->create([
                'text' => 'Информация — это:',
                'type' => 'single',
                'options' => ['A' => 'Только числа', 'B' => 'Сведения об объектах и явлениях', 'C' => 'Только текст'],
                'correct_answer' => ['B'],
                'order' => 0,
            ]);
        }
    }

    private function section2(): void
    {
        $section = Section::firstOrCreate(
            ['title' => 'Основы программирования на C++'],
            [
                'description' => 'Введение в C++. Настройка среды разработки.',
                'order' => 2,
                'level' => null,
            ]
        );

        Lesson::updateOrCreate(
            ['section_id' => $section->id, 'title' => 'Введение в C++. Настройка среды'],
            [
                'content' => "C++ — язык программирования общего назначения: от системного ПО до игр и приложений. Для работы нужны компилятор (GCC, Clang, MSVC) и среда разработки (IDE): Visual Studio, Code::Blocks, Qt Creator или VS Code с расширениями.\n\nУстановка: скачать компилятор и IDE, установить, создать проект «Консольное приложение», написать первую программу с выводом «Hello, World!». Компиляция превращает исходный код в исполняемый файл.",
                'video_id' => 'P5Lah3YlkpQ',
                'video_url' => 'https://www.youtube.com/watch?v=P5Lah3YlkpQ',
                'is_advanced' => false,
                'order' => 1,
            ]
        );

        $section->quiz()->firstOrCreate(
            [],
            ['title' => 'Квиз: C++ среда', 'passing_percent' => 70]
        );
    }
}
