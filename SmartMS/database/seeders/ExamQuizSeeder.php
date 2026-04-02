<?php

namespace Database\Seeders;

use App\Models\Quiz;
use Illuminate\Database\Seeder;

class ExamQuizSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedExamQuiz(9, 'Итоговый квиз: 9 класс (информация и алгоритмы)', 'Қорытынды квиз: 9 сынып (ақпарат және алгоритмдер)', [
            ['text' => 'Минимальная единица информации в компьютере?', 'text_kk' => 'Компьютерде ақпараттың ең кіші бірлігі?', 'options' => ['A' => 'Байт', 'B' => 'Бит', 'C' => 'Символ'], 'options_kk' => ['A' => 'Байт', 'B' => 'Бит', 'C' => 'Таңба'], 'correct' => 'B'],
            ['text' => 'Алгоритм должен обладать свойством:', 'text_kk' => 'Алгоритмнің қасиеті болуы керек:', 'options' => ['A' => 'Случайности', 'B' => 'Определённости', 'C' => 'Бесконечности'], 'options_kk' => ['A' => 'Кездейсоқтық', 'B' => 'Анықтылық', 'C' => 'Шексіздік'], 'correct' => 'B'],
            ['text' => '1 байт равен скольким битам?', 'text_kk' => '1 байт қанша битке тең?', 'options' => ['A' => '10', 'B' => '8', 'C' => '2'], 'options_kk' => ['A' => '10', 'B' => '8', 'C' => '2'], 'correct' => 'B'],
        ]);
        $this->seedExamQuiz(10, 'Итоговый квиз: 10 класс (системы счисления и C++)', 'Қорытынды квиз: 10 сынып (санау жүйелері және C++)', [
            ['text' => 'Основание двоичной системы счисления?', 'text_kk' => 'Екілік санау жүйесінің негізі?', 'options' => ['A' => '10', 'B' => '2', 'C' => '8'], 'options_kk' => ['A' => '10', 'B' => '2', 'C' => '8'], 'correct' => 'B'],
            ['text' => 'В C++ для вывода в консоль используется:', 'text_kk' => 'C++ консольге шығару үшін қолданылады:', 'options' => ['A' => 'print', 'B' => 'cout', 'C' => 'echo'], 'options_kk' => ['A' => 'print', 'B' => 'cout', 'C' => 'echo'], 'correct' => 'B'],
            ['text' => 'Цикл for в C++ задаётся:', 'text_kk' => 'C++ for циклі беріледі:', 'options' => ['A' => 'Только условием', 'B' => 'Инициализацией, условием, шагом', 'C' => 'Только счётчиком'], 'options_kk' => ['A' => 'Тек шартпен', 'B' => 'Инициализация, шарт, қадам', 'C' => 'Тек санауышпен'], 'correct' => 'B'],
        ]);
        $this->seedExamQuiz(11, 'Итоговый квиз: 11 класс (БД и проекты)', 'Қорытынды квиз: 11 сынып (ДҚ және жобалар)', [
            ['text' => 'Язык запросов к реляционной БД:', 'text_kk' => 'Реляциялық ДҚ сұраулар тілі:', 'options' => ['A' => 'HTML', 'B' => 'SQL', 'C' => 'Python'], 'options_kk' => ['A' => 'HTML', 'B' => 'SQL', 'C' => 'Python'], 'correct' => 'B'],
            ['text' => 'Первичный ключ в таблице служит для:', 'text_kk' => 'Кестедегі бірінші кілт қызметі:', 'options' => ['A' => 'Связи таблиц', 'B' => 'Уникальной идентификации записи', 'C' => 'Сортировки'], 'options_kk' => ['A' => 'Кестелер байланысы', 'B' => 'Жазбаны бірегей анықтау', 'C' => 'Сұрыптау'], 'correct' => 'B'],
            ['text' => 'Этап проекта после реализации:', 'text_kk' => 'Іске асырғаннан кейінгі жоба кезеңі:', 'options' => ['A' => 'Только презентация', 'B' => 'Тестирование', 'C' => 'План'], 'options_kk' => ['A' => 'Тек ұсыну', 'B' => 'Тестілеу', 'C' => 'Жоспар'], 'correct' => 'B'],
        ]);
    }

    private function seedExamQuiz(int $grade, string $title, string $titleKk, array $questions): void
    {
        $quiz = Quiz::firstOrCreate(
            [
                'title' => $title,
                'grade' => $grade,
                'section_id' => null,
            ],
            [
                'title_kk' => $titleKk,
                'passing_percent' => 60,
            ]
        );

        if ($quiz->questions()->count() > 0) {
            return;
        }

        foreach ($questions as $idx => $q) {
            $quiz->questions()->create([
                'text' => $q['text'],
                'text_kk' => $q['text_kk'] ?? null,
                'type' => 'single',
                'options' => $q['options'],
                'options_kk' => $q['options_kk'] ?? null,
                'correct_answer' => [$q['correct']],
                'order' => $idx,
            ]);
        }
    }
}
