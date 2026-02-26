<?php

namespace Database\Seeders;

use App\Models\Section;
use App\Models\Lesson;
use App\Models\Quiz;
use Illuminate\Database\Seeder;

class LessonsQuizzesExtraSeeder extends Seeder
{
    /**
     * Добавляет уроки, квизы и вопросы: новый раздел + доп. вопросы к существующим квизам.
     */
    public function run(): void
    {
        $this->addQuestionsToExistingQuizzes();
        $this->addSectionWebBasics();
        $this->addSectionOOP();
    }

    private function addQuestionsToExistingQuizzes(): void
    {
        Quiz::with('section', 'questions')->get()->each(function (Quiz $quiz) {
            $count = $quiz->questions()->count();
            if ($count >= 4) {
                return;
            }
            $need = 4 - $count;
            $startOrder = (int) $quiz->questions()->max('order');
            $extra = $this->extraQuestionsForQuiz($quiz->section->title ?? '', $startOrder + 1, $need);
            foreach (array_slice($extra, 0, $need) as $q) {
                $quiz->questions()->firstOrCreate(
                    ['text' => $q['text']],
                    [
                        'type' => 'single',
                        'options' => $q['options'],
                        'correct_answer' => [$q['correct']],
                        'order' => $q['order'],
                    ]
                );
            }
        });
    }

    /** @return array<int, array{text: string, options: array, correct: string, order: int}> */
    private function extraQuestionsForQuiz(string $sectionTitle, int $startOrder, int $need): array
    {
        $generic = [
            ['text' => 'Правильный ответ в тесте можно выбрать:', 'options' => ['A' => 'Только один', 'B' => 'Один или несколько в зависимости от вопроса', 'C' => 'Всегда несколько'], 'correct' => 'B', 'order' => $startOrder],
            ['text' => 'После прохождения квиза важно:', 'options' => ['A' => 'Забыть материал', 'B' => 'Повторить ошибки и закрепить тему', 'C' => 'Не смотреть результат'], 'correct' => 'B', 'order' => $startOrder + 1],
        ];

        $bySection = [
            'Введение в информатику' => [
                ['text' => 'Данные в компьютере хранятся в виде:', 'options' => ['A' => 'Только текста', 'B' => 'Двоичных кодов (битов)', 'C' => 'Только чисел'], 'correct' => 'B', 'order' => $startOrder],
                ['text' => 'Килобайт — это примерно:', 'options' => ['A' => '100 байт', 'B' => '1000 байт', 'C' => '10000 байт'], 'correct' => 'B', 'order' => $startOrder + 1],
            ],
            'Алгоритмы' => [
                ['text' => 'Свойство «массовость» алгоритма означает:', 'options' => ['A' => 'Один раз написал — много раз применил', 'B' => 'Только для одного случая', 'C' => 'Очень большой код'], 'correct' => 'A', 'order' => $startOrder],
                ['text' => 'Операция НЕ (NOT) инвертирует:', 'options' => ['A' => 'Число', 'B' => 'Логическое значение', 'C' => 'Строку'], 'correct' => 'B', 'order' => $startOrder + 1],
            ],
            'Основы программирования' => [
                ['text' => 'Тип данных bool хранит:', 'options' => ['A' => 'Только числа', 'B' => 'Истину или ложь', 'C' => 'Текст'], 'correct' => 'B', 'order' => $startOrder],
                ['text' => 'Ветвление if-else нужно для:', 'options' => ['A' => 'Повторения кода', 'B' => 'Выбора действия по условию', 'C' => 'Объявления переменной'], 'correct' => 'B', 'order' => $startOrder + 1],
            ],
            'Структуры данных' => [
                ['text' => 'Длина строки — это:', 'options' => ['A' => 'Количество байт', 'B' => 'Количество символов', 'C' => 'Только для чисел'], 'correct' => 'B', 'order' => $startOrder],
                ['text' => 'Многомерный массив — это:', 'options' => ['A' => 'Массив из одного элемента', 'B' => 'Массив, элементами которого могут быть массивы', 'C' => 'Только двумерный'], 'correct' => 'B', 'order' => $startOrder + 1],
            ],
            'Базы данных' => [
                ['text' => 'INSERT в SQL используется для:', 'options' => ['A' => 'Удаления строк', 'B' => 'Добавления новых записей', 'C' => 'Только выборки'], 'correct' => 'B', 'order' => $startOrder],
                ['text' => 'UPDATE в SQL меняет:', 'options' => ['A' => 'Структуру таблицы', 'B' => 'Существующие записи по условию', 'C' => 'Только одну таблицу в БД'], 'correct' => 'B', 'order' => $startOrder + 1],
            ],
        ];

        foreach ($bySection as $title => $questions) {
            if (str_contains($sectionTitle, $title) || str_contains($title, $sectionTitle)) {
                foreach ($questions as $i => $q) {
                    $questions[$i]['order'] = $startOrder + $i;
                }
                return $questions;
            }
        }

        return $generic;
    }

    /** @param array<int, array{text: string, options: array, correct: string, order: int}> $questions */
    private function addQuestionsToQuiz(Quiz $quiz, array $questions): void
    {
        $startOrder = (int) ($quiz->questions()->max('order') ?? -1);
        foreach ($questions as $i => $q) {
            $quiz->questions()->firstOrCreate(
                ['text' => $q['text']],
                [
                    'type' => 'single',
                    'options' => $q['options'],
                    'correct_answer' => [$q['correct']],
                    'order' => $startOrder + 1 + $i,
                ]
            );
        }
    }

    private function addSectionWebBasics(): void
    {
        $title = 'Веб-разработка: основы';
        if (Section::where('title', $title)->exists()) {
            return;
        }

        $section = Section::create([
            'title' => $title,
            'description' => 'HTML, CSS и как устроена веб-страница. Первая страница в браузере.',
            'order' => 10,
            'level' => 'beginner',
        ]);

        $lessons = [
            [
                'title' => 'Что такое HTML',
                'content' => "HTML (HyperText Markup Language) — язык разметки для создания веб-страниц. Браузер интерпретирует теги и отображает заголовки, абзацы, ссылки, картинки.\n\nОсновные теги: заголовки (h1–h6), параграф (p), ссылки (a href=…), изображения (img src=…). Документ состоит из head (метаданные, заголовок страницы) и body (содержимое). Атрибуты задают свойства элементов. HTML задаёт структуру, а не внешний вид — за оформление отвечает CSS.",
                'video_url' => 'https://www.youtube.com/watch?v=PlxWf493en4',
                'video_id' => 'PlxWf493en4',
                'order' => 1,
            ],
            [
                'title' => 'CSS: стили и оформление',
                'content' => "CSS (Cascading Style Sheets) задаёт внешний вид страницы: цвета, шрифты, отступы, размеры, сетки и анимации.\n\nСелекторы: по тегу (p, h1), по классу (.class), по id (#id). Свойства: color, background, margin, padding, font-size, display, flex. Стили подключают к HTML через тег <link> или <style>. Каскад и специфичность определяют, какие правила применяются к элементу.",
                'video_url' => 'https://www.youtube.com/watch?v=1PnVor36_40',
                'video_id' => '1PnVor36_40',
                'order' => 2,
            ],
        ];

        foreach ($lessons as $l) {
            $section->lessons()->create(array_merge($l, ['file_path' => null, 'is_advanced' => false]));
        }

        $quiz = $section->quiz()->create([
            'title' => 'Квиз: Веб-разработка',
            'passing_percent' => 70,
        ]);

        $this->addQuestionsToQuiz($quiz, [
            ['text' => 'HTML расшифровывается как:', 'options' => ['A' => 'HyperText Markup Language', 'B' => 'High Tech Model Language', 'C' => 'Home Tool Markup Language'], 'correct' => 'A', 'order' => 0],
            ['text' => 'Тег <p> задаёт:', 'options' => ['A' => 'Заголовок', 'B' => 'Параграф', 'C' => 'Ссылку'], 'correct' => 'B', 'order' => 1],
            ['text' => 'CSS отвечает за:', 'options' => ['A' => 'Логику на сервере', 'B' => 'Внешний вид и стили страницы', 'C' => 'Только шрифты'], 'correct' => 'B', 'order' => 2],
            ['text' => 'Класс в CSS задаётся через:', 'options' => ['A' => 'class="..."', 'B' => 'id="..."', 'C' => 'Только в JS'], 'correct' => 'A', 'order' => 3],
        ]);
    }

    private function addSectionOOP(): void
    {
        $title = 'ООП и практика';
        if (Section::where('title', $title)->exists()) {
            return;
        }

        $section = Section::create([
            'title' => $title,
            'description' => 'Объектно-ориентированное программирование: классы, объекты, инкапсуляция.',
            'order' => 11,
            'level' => 'advanced',
        ]);

        $lessons = [
            [
                'title' => 'Классы и объекты',
                'content' => "Класс — шаблон (описание) для создания объектов: в нём задаются поля (данные) и методы (действия). Объект — конкретный экземпляр класса со своими значениями полей.\n\nКонструктор — специальный метод, вызываемый при создании объекта (например, new User()). Пример: класс User с полями name, email и методом sayHello(). Классы позволяют моделировать сущности из реального мира и переиспользовать код.",
                'video_url' => 'https://www.youtube.com/watch?v=J5bQ8c4ofx8',
                'video_id' => 'J5bQ8c4ofx8',
                'order' => 1,
            ],
            [
                'title' => 'Инкапсуляция и модификаторы доступа',
                'content' => "Инкапсуляция — принцип ООП: скрытие внутренней реализации объекта, доступ к данным только через разрешённые методы.\n\nМодификаторы доступа: public (доступно снаружи), private (только внутри класса), protected (внутри класса и наследников). Свойства (getters/setters) дают контролируемый доступ к полям и позволяют проверять и изменять значения по правилам. Это защищает от ошибок и упрощает поддержку кода.",
                'video_url' => 'https://www.youtube.com/watch?v=J5bQ8c4ofx8',
                'video_id' => 'J5bQ8c4ofx8',
                'order' => 2,
            ],
        ];

        foreach ($lessons as $l) {
            $section->lessons()->create(array_merge($l, ['file_path' => null, 'is_advanced' => true]));
        }

        $quiz = $section->quiz()->create([
            'title' => 'Квиз: ООП',
            'passing_percent' => 70,
        ]);

        $this->addQuestionsToQuiz($quiz, [
            ['text' => 'Объект — это:', 'options' => ['A' => 'Только функция', 'B' => 'Экземпляр класса', 'C' => 'Только переменная'], 'correct' => 'B', 'order' => 0],
            ['text' => 'Инкапсуляция позволяет:', 'options' => ['A' => 'Только ускорить код', 'B' => 'Скрыть детали реализации', 'C' => 'Удалить класс'], 'correct' => 'B', 'order' => 1],
            ['text' => 'Приватное поле (private) доступно:', 'options' => ['A' => 'Везде в программе', 'B' => 'Только внутри класса', 'C' => 'Только в другом классе'], 'correct' => 'B', 'order' => 2],
            ['text' => 'Конструктор класса вызывается:', 'options' => ['A' => 'При удалении объекта', 'B' => 'При создании объекта', 'C' => 'Только вручную'], 'correct' => 'B', 'order' => 3],
        ]);
    }
}
