<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Seeder;

class Grade11RevisionExpansionSeeder extends Seeder
{
    public function run(): void
    {
        Section::query()
            ->where('grade', 11)
            ->where('is_revision', true)
            ->where('title', 'Повторение основ')
            ->update([
                'order' => 3,
            ]);

        $sections = [
            [
                'title' => 'Повторение алгоритмов и логики',
                'title_kk' => 'Алгоритмдер мен логиканы қайталау',
                'description' => 'Дополнительный базовый раздел для учеников 11 класса, которые не прошли входной тест. Повторяем алгоритмы, условия и таблицы истинности.',
                'description_kk' => 'Кіру тестінен өтпеген 11-сынып оқушыларына арналған қосымша базалық бөлім. Алгоритмдер, шарттар және ақиқат кестелері қайталанады.',
                'order' => 4,
                'lessons' => [
                    [
                        'title' => 'Алгоритм как последовательность действий',
                        'title_kk' => 'Алгоритм әрекеттер тізбегі ретінде',
                        'content' => 'Повторяем понятие алгоритма, его свойства и способы записи. Разбираем примеры повседневных и учебных алгоритмов.',
                        'content_kk' => 'Алгоритм ұғымын, оның қасиеттерін және жазылу тәсілдерін қайталаймыз. Күнделікті және оқу алгоритмдерінің мысалдарын талдаймыз.',
                        'video_url' => 'https://www.youtube.com/watch?v=t-JRMxluNz8',
                        'video_id' => 't-JRMxluNz8',
                        'order' => 1,
                    ],
                    [
                        'title' => 'Условия, ветвления и логические выражения',
                        'title_kk' => 'Шарттар, тармақталу және логикалық өрнектер',
                        'content' => 'Повторяем, как работают условия if, логические операции И, ИЛИ, НЕ и как читать простые логические выражения.',
                        'content_kk' => 'if шарттарының, ЖӘНЕ, НЕМЕСЕ, ЕМЕС логикалық амалдарының қалай жұмыс істейтінін және қарапайым логикалық өрнектерді оқуды қайталаймыз.',
                        'video_url' => 'https://www.youtube.com/watch?v=2VBw9dX3L8E',
                        'video_id' => '2VBw9dX3L8E',
                        'order' => 2,
                    ],
                    [
                        'title' => 'Таблицы истинности и простые схемы',
                        'title_kk' => 'Ақиқат кестелері және қарапайым сызбалар',
                        'content' => 'Закрепляем логику через таблицы истинности, блок-схемы и простые задачи на проверку условий.',
                        'content_kk' => 'Логиканы ақиқат кестелері, блок-схемалар және шарттарды тексеруге арналған қарапайым тапсырмалар арқылы бекітеміз.',
                        'video_url' => 'https://www.youtube.com/watch?v=4_4RnpL6f_A',
                        'video_id' => '4_4RnpL6f_A',
                        'order' => 3,
                    ],
                ],
                'quiz' => [
                    'title' => 'Квиз: Повторение алгоритмов и логики',
                    'title_kk' => 'Квиз: Алгоритмдер мен логиканы қайталау',
                    'passing_percent' => 50,
                    'questions' => [
                        [
                            'text' => 'Что обязательно должен содержать алгоритм?',
                            'text_kk' => 'Алгоритмде міндетті түрде не болуы керек?',
                            'options' => ['A' => 'Последовательность шагов', 'B' => 'Только рисунок', 'C' => 'Только ответ'],
                            'options_kk' => ['A' => 'Қадамдар тізбегі', 'B' => 'Тек сурет', 'C' => 'Тек жауап'],
                            'correct' => 'A',
                        ],
                        [
                            'text' => 'Операция И даёт истину, когда:',
                            'text_kk' => 'ЖӘНЕ амалы ақиқат болады, қашан:',
                            'options' => ['A' => 'Хотя бы одно условие истинно', 'B' => 'Оба условия истинны', 'C' => 'Оба условия ложны'],
                            'options_kk' => ['A' => 'Кемінде бір шарт ақиқат', 'B' => 'Екі шарт та ақиқат', 'C' => 'Екі шарт та жалған'],
                            'correct' => 'B',
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Повторение SQL и структур данных',
                'title_kk' => 'SQL және деректер құрылымдарын қайталау',
                'description' => 'Ещё один поддерживающий раздел для 11 класса: простые таблицы, запросы и организация данных в проектах.',
                'description_kk' => '11-сыныпқа арналған тағы бір қолдаушы бөлім: қарапайым кестелер, сұраулар және жобадағы деректерді ұйымдастыру.',
                'order' => 5,
                'lessons' => [
                    [
                        'title' => 'Таблицы, строки и столбцы',
                        'title_kk' => 'Кестелер, жолдар және бағандар',
                        'content' => 'Повторяем, как устроены таблицы в базе данных, что такое записи и поля, и зачем нужен первичный ключ.',
                        'content_kk' => 'Деректер қорындағы кестелердің қалай құрылғанын, жазба мен өрістің не екенін және бастапқы кілт не үшін керек екенін қайталаймыз.',
                        'video_url' => 'https://www.youtube.com/watch?v=HXV3zeQKqGY',
                        'video_id' => 'HXV3zeQKqGY',
                        'order' => 1,
                    ],
                    [
                        'title' => 'Простые SQL-запросы',
                        'title_kk' => 'Қарапайым SQL-сұраулар',
                        'content' => 'Разбираем SELECT, WHERE и ORDER BY на простых школьных примерах, чтобы ученик уверенно читал готовые запросы.',
                        'content_kk' => 'SELECT, WHERE және ORDER BY операторларын қарапайым мектеп мысалдарымен талдаймыз, оқушы дайын сұрауларды сенімді оқуы үшін.',
                        'video_url' => 'https://www.youtube.com/watch?v=1uqci6BXLM8',
                        'video_id' => '1uqci6BXLM8',
                        'order' => 2,
                    ],
                    [
                        'title' => 'Как хранить данные в учебном проекте',
                        'title_kk' => 'Оқу жобасында деректерді қалай сақтау керек',
                        'content' => 'Показываем, как выбирать структуру данных для небольшого проекта: список, таблица, форма ввода и проверка данных.',
                        'content_kk' => 'Шағын жоба үшін деректер құрылымын қалай таңдау керегін көрсетеміз: тізім, кесте, енгізу формасы және деректерді тексеру.',
                        'video_url' => 'https://www.youtube.com/watch?v=8WEtxJ4-sh4',
                        'video_id' => '8WEtxJ4-sh4',
                        'order' => 3,
                    ],
                ],
                'quiz' => [
                    'title' => 'Квиз: Повторение SQL и структур данных',
                    'title_kk' => 'Квиз: SQL және деректер құрылымдарын қайталау',
                    'passing_percent' => 50,
                    'questions' => [
                        [
                            'text' => 'Для чего используется SELECT?',
                            'text_kk' => 'SELECT не үшін қолданылады?',
                            'options' => ['A' => 'Для удаления таблицы', 'B' => 'Для выборки данных', 'C' => 'Для выключения программы'],
                            'options_kk' => ['A' => 'Кестені жою үшін', 'B' => 'Деректерді таңдау үшін', 'C' => 'Бағдарламаны өшіру үшін'],
                            'correct' => 'B',
                        ],
                        [
                            'text' => 'Столбец в таблице обычно хранит:',
                            'text_kk' => 'Кестедегі баған әдетте нені сақтайды?',
                            'options' => ['A' => 'Один тип данных', 'B' => 'Сразу весь проект', 'C' => 'Только формулы'],
                            'options_kk' => ['A' => 'Бір типтегі деректерді', 'B' => 'Бірден бүкіл жобаны', 'C' => 'Тек формулаларды'],
                            'correct' => 'A',
                        ],
                    ],
                ],
            ],
        ];

        foreach ($sections as $sectionData) {
            $section = Section::updateOrCreate(
                [
                    'grade' => 11,
                    'is_revision' => true,
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
                    'grade' => 11,
                    'title' => $lessonData['title'],
                    'title_kk' => $lessonData['title_kk'],
                    'content' => $lessonData['content'],
                    'content_kk' => $lessonData['content_kk'],
                    'video_url' => $lessonData['video_url'],
                    'video_id' => $lessonData['video_id'],
                    'order' => $lessonData['order'],
                    'is_advanced' => false,
                ]);
            }

            $quiz = $section->quiz()->updateOrCreate(
                ['section_id' => $section->id],
                [
                    'grade' => 11,
                    'title' => $sectionData['quiz']['title'],
                    'title_kk' => $sectionData['quiz']['title_kk'],
                    'passing_percent' => $sectionData['quiz']['passing_percent'],
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
}
