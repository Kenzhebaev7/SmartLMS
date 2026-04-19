<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Seeder;

class EnsureMinGradeLevelSectionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->normalizeExistingOrders();

        foreach ($this->sectionBlueprints() as $sectionData) {
            $section = Section::updateOrCreate(
                [
                    'grade' => $sectionData['grade'],
                    'is_revision' => $sectionData['is_revision'],
                    'title' => $sectionData['title'],
                ],
                [
                    'title_kk' => $sectionData['title_kk'],
                    'description' => $sectionData['description'],
                    'description_kk' => $sectionData['description_kk'],
                    'order' => $sectionData['order'],
                    'is_featured' => false,
                ]
            );

            $section->lessons()->delete();

            foreach ($sectionData['lessons'] as $lessonData) {
                $section->lessons()->create([
                    'grade' => $sectionData['grade'],
                    'title' => $lessonData['title'],
                    'title_kk' => $lessonData['title_kk'],
                    'content' => $lessonData['content'],
                    'content_kk' => $lessonData['content_kk'],
                    'video_url' => 'https://www.youtube.com/watch?v=' . $lessonData['video_id'],
                    'video_id' => $lessonData['video_id'],
                    'video_url_kk' => 'https://www.youtube.com/watch?v=' . $lessonData['video_id'],
                    'video_id_kk' => $lessonData['video_id'],
                    'order' => $lessonData['order'],
                    'is_advanced' => false,
                ]);
            }

            $quiz = $section->quiz()->updateOrCreate(
                ['section_id' => $section->id],
                [
                    'grade' => $sectionData['grade'],
                    'title' => $sectionData['quiz']['title'],
                    'title_kk' => $sectionData['quiz']['title_kk'],
                    'passing_percent' => 50,
                ]
            );

            $quiz->questions()->delete();

            foreach ($sectionData['quiz']['questions'] as $index => $questionData) {
                $quiz->questions()->create([
                    'text' => $questionData['text'],
                    'text_kk' => $questionData['text_kk'],
                    'type' => 'single',
                    'options' => $questionData['options'],
                    'options_kk' => $questionData['options_kk'],
                    'correct_answer' => [$questionData['correct']],
                    'order' => $index,
                ]);
            }
        }
    }

    private function normalizeExistingOrders(): void
    {
        Section::query()
            ->where('grade', 9)
            ->where('is_revision', true)
            ->where('title', 'Повторение основ')
            ->update(['order' => 43]);

        Section::query()
            ->where('grade', 10)
            ->where('is_revision', true)
            ->where('title', 'Повторение основ')
            ->update(['order' => 43]);
    }

    private function sectionBlueprints(): array
    {
        return [
            $this->makeSection(
                9,
                41,
                'Логика и блок-схемы',
                'Логика және блок-схемалар',
                'Базовый раздел для 9 класса: логика, блок-схемы и простые алгоритмы.',
                '9-сыныпқа арналған базалық бөлім: логика, блок-схемалар және қарапайым алгоритмдер.',
                [
                    $this->lesson('Логические операции', 'Логикалық амалдар', 'Повторяем И, ИЛИ, НЕ и простые логические задачи.', 'ЖӘНЕ, НЕМЕСЕ, ЕМЕС амалдарын және қарапайым логикалық есептерді қайталаймыз.', '8hly31xKli0', 1),
                    $this->lesson('Блок-схемы алгоритмов', 'Алгоритмдердің блок-схемалары', 'Учимся читать и составлять блок-схемы для простых задач.', 'Қарапайым есептерге арналған блок-схемаларды оқып, құруды үйренеміз.', '8hly31xKli0', 2),
                    $this->lesson('Исполнители и команды', 'Орындаушылар және командалар', 'Разбираем команды исполнителя и пошаговое выполнение алгоритма.', 'Орындаушы командаларын және алгоритмнің қадамдап орындалуын талдаймыз.', '8hly31xKli0', 3),
                    $this->lesson('Практика по логике', 'Логика бойынша практика', 'Закрепляем тему на небольших практических примерах.', 'Тақырыпты шағын практикалық мысалдармен бекітеміз.', '8hly31xKli0', 4),
                ],
                'Квиз: Логика и блок-схемы',
                'Квиз: Логика және блок-схемалар'
            ),
            $this->makeSection(
                9,
                42,
                'Цифровая грамотность и безопасность',
                'Цифрлық сауаттылық және қауіпсіздік',
                'Базовый раздел для 9 класса: работа с интернетом, файлами и цифровой безопасностью.',
                '9-сыныпқа арналған базалық бөлім: интернетпен, файлдармен жұмыс және цифрлық қауіпсіздік.',
                [
                    $this->lesson('Как работает интернет', 'Интернет қалай жұмыс істейді', 'Разбираем, как сайты и браузеры обмениваются данными.', 'Сайттар мен браузерлердің деректермен қалай алмасатынын талдаймыз.', '4DlUO7K7SNU', 1),
                    $this->lesson('Безопасные пароли', 'Қауіпсіз құпиясөздер', 'Учимся создавать надежные пароли и защищать аккаунты.', 'Сенімді құпиясөздер құрып, аккаунттарды қорғауды үйренеміз.', '4DlUO7K7SNU', 2),
                    $this->lesson('Файлы и папки', 'Файлдар мен бумалар', 'Повторяем хранение файлов и порядок в учебных материалах.', 'Файлдарды сақтау мен оқу материалдарындағы реттілікті қайталаймыз.', '4DlUO7K7SNU', 3),
                    $this->lesson('Практика цифровой грамотности', 'Цифрлық сауаттылық практикасы', 'Закрепляем тему на жизненных цифровых ситуациях.', 'Тақырыпты күнделікті цифрлық жағдайлар арқылы бекітеміз.', '4DlUO7K7SNU', 4),
                ],
                'Квиз: Цифровая грамотность',
                'Квиз: Цифрлық сауаттылық'
            ),
            $this->makeSection(
                10,
                41,
                'Электронные таблицы: база',
                'Электрондық кестелер: база',
                'Базовый раздел для 10 класса: таблицы, формулы и анализ данных.',
                '10-сыныпқа арналған базалық бөлім: кестелер, формулалар және деректерді талдау.',
                [
                    $this->lesson('Строки, столбцы и ячейки', 'Жолдар, бағандар және ұяшықтар', 'Повторяем основу работы с электронной таблицей.', 'Электрондық кестемен жұмыстың негізін қайталаймыз.', 'qz0aGYrrlhU', 1),
                    $this->lesson('Простые формулы', 'Қарапайым формулалар', 'Учимся считать значения и использовать базовые формулы.', 'Мәндерді есептеп, негізгі формулаларды қолдануды үйренеміз.', 'qz0aGYrrlhU', 2),
                    $this->lesson('Сортировка и фильтрация', 'Сұрыптау және сүзу', 'Закрепляем работу с данными через сортировку и фильтры.', 'Деректермен жұмысты сұрыптау мен сүзгілер арқылы бекітеміз.', 'qz0aGYrrlhU', 3),
                    $this->lesson('Практика по таблицам', 'Кестелер бойынша практика', 'Решаем небольшие задания с таблицами.', 'Кестелермен шағын тапсырмаларды орындаймыз.', 'qz0aGYrrlhU', 4),
                ],
                'Квиз: Электронные таблицы',
                'Квиз: Электрондық кестелер'
            ),
            $this->makeSection(
                10,
                42,
                'Веб и цифровой контент: база',
                'Веб және цифрлық контент: база',
                'Базовый раздел для 10 класса: основы веба и цифрового контента.',
                '10-сыныпқа арналған базалық бөлім: веб негіздері және цифрлық контент.',
                [
                    $this->lesson('Что такое HTML', 'HTML деген не', 'Повторяем основу структуры веб-страницы.', 'Веб-беттің құрылым негізін қайталаймыз.', 'qz0aGYrrlhU', 1),
                    $this->lesson('Текст и изображения на странице', 'Беттегі мәтін мен суреттер', 'Учимся размещать контент на простой веб-странице.', 'Қарапайым веб-бетте контент орналастыруды үйренеміз.', 'qz0aGYrrlhU', 2),
                    $this->lesson('Основы оформления', 'Безендіру негіздері', 'Разбираем базовое оформление и удобное чтение страницы.', 'Бетті безендіру мен ыңғайлы оқуды талдаймыз.', 'qz0aGYrrlhU', 3),
                    $this->lesson('Практика цифрового контента', 'Цифрлық контент практикасы', 'Делаем небольшой цифровой материал по шаблону.', 'Үлгі бойынша шағын цифрлық материал жасаймыз.', '2t4h6t4r0jc', 4),
                ],
                'Квиз: Веб и цифровой контент',
                'Квиз: Веб және цифрлық контент'
            ),
        ];
    }

    private function makeSection(
        int $grade,
        int $order,
        string $title,
        string $titleKk,
        string $description,
        string $descriptionKk,
        array $lessons,
        string $quizTitle,
        string $quizTitleKk
    ): array {
        return [
            'grade' => $grade,
            'is_revision' => true,
            'order' => $order,
            'title' => $title,
            'title_kk' => $titleKk,
            'description' => $description,
            'description_kk' => $descriptionKk,
            'lessons' => $lessons,
            'quiz' => [
                'title' => $quizTitle,
                'title_kk' => $quizTitleKk,
                'questions' => [
                    [
                        'text' => 'Этот раздел относится к базовому уровню?',
                        'text_kk' => 'Бұл бөлім базалық деңгейге жата ма?',
                        'options' => ['A' => 'Да', 'B' => 'Нет', 'C' => 'Только для учителя'],
                        'options_kk' => ['A' => 'Иә', 'B' => 'Жоқ', 'C' => 'Тек мұғалімге'],
                        'correct' => 'A',
                    ],
                    [
                        'text' => 'После уроков раздела нужно пройти квиз?',
                        'text_kk' => 'Бөлім сабақтарынан кейін квиз өту керек пе?',
                        'options' => ['A' => 'Да', 'B' => 'Нет', 'C' => 'Только админу'],
                        'options_kk' => ['A' => 'Иә', 'B' => 'Жоқ', 'C' => 'Тек админге'],
                        'correct' => 'A',
                    ],
                ],
            ],
        ];
    }

    private function lesson(
        string $title,
        string $titleKk,
        string $content,
        string $contentKk,
        string $videoId,
        int $order
    ): array {
        return [
            'title' => $title,
            'title_kk' => $titleKk,
            'content' => $content,
            'content_kk' => $contentKk,
            'video_id' => $videoId,
            'order' => $order,
        ];
    }
}
