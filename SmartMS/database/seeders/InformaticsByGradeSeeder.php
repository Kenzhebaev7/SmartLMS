<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Section;
use App\Services\SectionDuplicateCleanup;
use Illuminate\Database\Seeder;

/**
 * Разделы и уроки по информатике для 9, 10 и 11 классов (Казахстан).
 *
 * Распределение по классам и этапам:
 * - У каждого класса свой набор разделов (grade = 9, 10, 11).
 * - Порядок внутри класса: order = (grade * 100) + этап (1, 2, 3…).
 * - Этап 1, 2 — основные темы (is_revision = false). Этап 3 — «Повторение основ» (is_revision = true).
 * - Кто не прошёл вступительный тест: видит только разделы с is_revision = true (повторение).
 * - Кто прошёл: видит только основные разделы (этапы 1, 2), поэтапная разблокировка по квизам.
 */
class InformaticsByGradeSeeder extends Seeder
{
    public function run(): void
    {
        app(SectionDuplicateCleanup::class)->run();

        $this->seedGrade(9, $this->sectionsGrade9());
        $this->seedGrade(10, $this->sectionsGrade10());
        $this->seedGrade(11, $this->sectionsGrade11());

        $this->markProjectActivitySectionsFeatured();
    }

    /** Избранный раздел «Проектная деятельность…» — совпадение по шаблону (как в dedupeKey). */
    private function markProjectActivitySectionsFeatured(): void
    {
        Section::query()->chunkById(50, function ($sections) {
            foreach ($sections as $section) {
                if (Section::looksLikeProjectActivitySection($section)) {
                    $section->update(['is_featured' => true]);
                }
            }
        });
    }

    private function seedGrade(int $grade, array $sectionsData): void
    {
        foreach ($sectionsData as $i => $s) {
            $quizData = $s['quiz'] ?? null;
            $lessonsData = $s['lessons'] ?? [];
            unset($s['quiz'], $s['lessons']);
            // Порядок: класс * 100 + номер этапа (1, 2, 3). Так основные темы идут первыми, повторение — последним.
            $s['order'] = ($grade * 100) + $i + 1;
            $s['grade'] = $grade;
            $s['is_revision'] = $s['is_revision'] ?? false;
            $s['is_featured'] = $s['is_featured'] ?? false;

            $section = Section::firstOrCreate(
                [
                    'title' => $s['title'],
                    'grade' => $grade,
                ],
                $s
            );
            // Обновляем казахские поля у существующих разделов (чтобы переключение языка работало)
            $section->update([
                'title_kk' => $s['title_kk'] ?? null,
                'description_kk' => $s['description_kk'] ?? null,
                'order' => $s['order'],
                'is_revision' => $s['is_revision'],
                'is_featured' => $s['is_featured'],
            ]);

            foreach ($lessonsData as $l) {
                $section->lessons()->updateOrCreate(
                    ['section_id' => $section->id, 'order' => $l['order']],
                    array_merge($l, ['file_path' => null])
                );
            }

            if ($quizData) {
                $quiz = $section->quiz()->first();
                if (!$quiz) {
                    $quiz = $section->quiz()->create([
                        'title' => $quizData['title'],
                        'title_kk' => $quizData['title_kk'] ?? null,
                        'passing_percent' => $quizData['passing_percent'] ?? 70,
                    ]);
                    foreach ($quizData['questions'] ?? [] as $idx => $q) {
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
                } else {
                    $quiz->update(['title_kk' => $quizData['title_kk'] ?? null]);
                    $questions = $quizData['questions'] ?? [];
                    foreach ($quiz->questions()->orderBy('order')->get() as $idx => $q) {
                        if (isset($questions[$idx])) {
                            $q->update([
                                'text_kk' => $questions[$idx]['text_kk'] ?? null,
                                'options_kk' => $questions[$idx]['options_kk'] ?? null,
                            ]);
                        }
                    }
                }
            }
        }
    }

    private function sectionsGrade9(): array
    {
        return array_merge([$this->projectActivitySection()], [
            [
                'title' => 'Информация и кодирование',
                'title_kk' => 'Ақпарат және кодтау',
                'description' => 'Информация, данные, кодирование. Бит, байт, системы счисления.',
                'description_kk' => 'Ақпарат, деректер, кодтау. Бит, байт, санау жүйелері.',
                'is_revision' => false,
                'lessons' => [
                    ['title' => 'Что такое информация', 'title_kk' => 'Ақпарат деген не', 'content' => "Информация — сведения об окружающем мире. Данные — представление информации для обработки. Бит — минимальная единица.", 'content_kk' => "Ақпарат — айналадағы әлем туралы мәлімет. Деректер — өңдеу үшін ақпараттың көрінісі. Бит — ең кіші бірлік.", 'video_url' => 'https://www.youtube.com/watch?v=t-JRMxluNz8', 'video_id' => 't-JRMxluNz8', 'order' => 1],
                    ['title' => 'Кодирование информации', 'title_kk' => 'Ақпаратты кодтау', 'content' => "Кодирование — представление информации в виде символов. Текстовые кодировки. Двоичное кодирование.", 'content_kk' => "Кодтау — ақпаратты таңбалар түрінде беру. Мәтіндік кодтаулар. Екілік кодтау.", 'video_url' => 'https://www.youtube.com/watch?v=1uqci6BXLM8', 'video_id' => '1uqci6BXLM8', 'order' => 2],
                    ['title' => 'Единицы измерения', 'title_kk' => 'Өлшем бірліктері', 'content' => "Бит, байт, килобайт, мегабайт. Объём информации.", 'content_kk' => "Бит, байт, килобайт, мегабайт. Ақпарат көлемі.", 'video_url' => 'https://www.youtube.com/watch?v=8WEtxJ4-sh4', 'video_id' => '8WEtxJ4-sh4', 'order' => 3],
                    ['title' => 'Передача информации', 'title_kk' => 'Ақпарат беру', 'content' => "Канал связи, скорость передачи. Сжатие данных.", 'content_kk' => "Байланыс арнасы, беру жылдамдығы. Деректерді сығу.", 'video_url' => 'https://www.youtube.com/watch?v=2VBw9dX3L8E', 'video_id' => '2VBw9dX3L8E', 'order' => 4],
                ],
                'quiz' => ['title' => 'Квиз: Информация и кодирование', 'title_kk' => 'Квиз: Ақпарат және кодтау', 'passing_percent' => 70, 'questions' => [
                    ['text' => 'Минимальная единица информации:', 'text_kk' => 'Ақпараттың ең кіші бірлігі:', 'options' => ['A' => 'Байт', 'B' => 'Бит', 'C' => 'Килобайт'], 'options_kk' => ['A' => 'Байт', 'B' => 'Бит', 'C' => 'Килобайт'], 'correct' => 'B'],
                    ['text' => '1 байт равен:', 'text_kk' => '1 байт мынаған тең:', 'options' => ['A' => '8 бит', 'B' => '10 бит', 'C' => '2 бита'], 'options_kk' => ['A' => '8 бит', 'B' => '10 бит', 'C' => '2 бит'], 'correct' => 'A'],
                ]],
            ],
            [
                'title' => 'Алгоритмы',
                'title_kk' => 'Алгоритмдер',
                'description' => 'Понятие алгоритма, свойства, блок-схемы. Ветвления и циклы.',
                'description_kk' => 'Алгоритм түсінігі, қасиеттері, блок-схемалар. Тармақталу және циклдар.',
                'is_revision' => false,
                'lessons' => [
                    ['title' => 'Понятие алгоритма', 'title_kk' => 'Алгоритм түсінігі', 'content' => "Алгоритм — последовательность шагов для решения задачи. Свойства: дискретность, определённость, результативность, массовость.", 'content_kk' => "Алгоритм — есепті шешу үшін қадамдар тізбегі. Қасиеттер: дискреттілік, анықтылық, нәтижелілік, массалылық.", 'video_url' => 'https://www.youtube.com/watch?v=t-JRMxluNz8', 'video_id' => 't-JRMxluNz8', 'order' => 1],
                    ['title' => 'Блок-схемы', 'title_kk' => 'Блок-схемалар', 'content' => "Графическое представление алгоритма. Блоки: начало/конец, действие, условие.", 'content_kk' => "Алгоритмнің графикалық көрінісі. Блоктар: басы/соңы, әрекет, шарт.", 'video_url' => 'https://www.youtube.com/watch?v=2VBw9dX3L8E', 'video_id' => '2VBw9dX3L8E', 'order' => 2],
                    ['title' => 'Ветвление и циклы', 'title_kk' => 'Тармақталу және циклдар', 'content' => "Условие «если — то — иначе». Циклы: с предусловием и по счётчику.", 'content_kk' => "«Егер — онда — әйтпесе» шарты. Циклдар: алғышартпен және санауыш бойынша.", 'video_url' => 'https://www.youtube.com/watch?v=4_4RnpL6f_A', 'video_id' => '4_4RnpL6f_A', 'order' => 3],
                ],
                'quiz' => ['title' => 'Квиз: Алгоритмы', 'title_kk' => 'Квиз: Алгоритмдер', 'passing_percent' => 70, 'questions' => [
                    ['text' => 'Алгоритм должен быть:', 'text_kk' => 'Алгоритм болуы керек:', 'options' => ['A' => 'Только быстрым', 'B' => 'Дискретным и определённым', 'C' => 'На английском'], 'options_kk' => ['A' => 'Тек жылдам', 'B' => 'Дискретті және анық', 'C' => 'Ағылшын тілінде'], 'correct' => 'B'],
                    ['text' => 'ИЛИ даёт истину, когда:', 'text_kk' => 'НЕМЕСЕ ақиқат береді, қашан:', 'options' => ['A' => 'Оба ложны', 'B' => 'Хотя бы одно истинно', 'C' => 'Оба истинны'], 'options_kk' => ['A' => 'Екеуі жалған', 'B' => 'Кем дегенде бірі ақиқат', 'C' => 'Екеуі ақиқат'], 'correct' => 'B'],
                ]],
            ],
            [
                'title' => 'Повторение основ',
                'title_kk' => 'Негіздерді қайталау',
                'description' => 'Повторение материала для тех, кто не прошёл вступительный тест.',
                'description_kk' => 'Кіру тестін тапсырмағандар үшін материалды қайталау.',
                'is_revision' => true,
                'lessons' => [
                    ['title' => 'Информация и данные', 'title_kk' => 'Ақпарат және деректер', 'content' => "Повторение: информация, данные, бит, байт.", 'content_kk' => "Қайталау: ақпарат, деректер, бит, байт.", 'video_url' => 'https://www.youtube.com/watch?v=t-JRMxluNz8', 'video_id' => 't-JRMxluNz8', 'order' => 1],
                    ['title' => 'Алгоритмы: основы', 'title_kk' => 'Алгоритмдер: негіздер', 'content' => "Повторение: что такое алгоритм, блок-схема, условия.", 'content_kk' => "Қайталау: алгоритм деген не, блок-схема, шарттар.", 'video_url' => 'https://www.youtube.com/watch?v=2VBw9dX3L8E', 'video_id' => '2VBw9dX3L8E', 'order' => 2],
                ],
                'quiz' => ['title' => 'Квиз: Повторение основ (9 класс)', 'title_kk' => 'Квиз: Негіздерді қайталау (9 сынып)', 'passing_percent' => 50, 'questions' => [
                    ['text' => 'Бит — это:', 'text_kk' => 'Бит — бұл:', 'options' => ['A' => 'Байт', 'B' => 'Минимальная единица информации', 'C' => 'Файл'], 'options_kk' => ['A' => 'Байт', 'B' => 'Ақпараттың ең кіші бірлігі', 'C' => 'Файл'], 'correct' => 'B'],
                    ['text' => 'Алгоритм — это:', 'text_kk' => 'Алгоритм — бұл:', 'options' => ['A' => 'Программа', 'B' => 'Последовательность шагов', 'C' => 'Компьютер'], 'options_kk' => ['A' => 'Бағдарлама', 'B' => 'Қадамдар тізбегі', 'C' => 'Компьютер'], 'correct' => 'B'],
                ]],
            ],
        ]);
    }

    private function sectionsGrade10(): array
    {
        return array_merge([$this->projectActivitySection()], [
            [
                'title' => 'Системы счисления',
                'title_kk' => 'Санау жүйелері',
                'description' => 'Двоичная, восьмеричная, шестнадцатеричная системы. Перевод между системами.',
                'description_kk' => 'Екілік, сегіздік, он алтылық жүйелер. Жүйелер арасында аударма.',
                'is_revision' => false,
                'lessons' => [
                    ['title' => 'Позиционные системы счисления', 'title_kk' => 'Позициялық санау жүйелері', 'content' => "Позиционная система: основание, разряды. Десятичная, двоичная системы.", 'content_kk' => "Позициялық жүйе: негіз, разрядтар. Ондық, екілік жүйелер.", 'video_url' => 'https://www.youtube.com/watch?v=1OVVvHVrF0s', 'video_id' => '1OVVvHVrF0s', 'order' => 1],
                    ['title' => 'Двоичная система', 'title_kk' => 'Екілік жүйе', 'content' => "Перевод из десятичной в двоичную и обратно. Сложение и вычитание в двоичной системе.", 'content_kk' => "Ондықтан екілікке және кері аударма. Екілік жүйеде қосу және алу.", 'video_url' => 'https://www.youtube.com/watch?v=W8F9_2OHq2A', 'video_id' => 'W8F9_2OHq2A', 'order' => 2],
                    ['title' => 'Восьмеричная и шестнадцатеричная', 'title_kk' => 'Сегіздік және он алтылық', 'content' => "Связь с двоичной системой. Использование в программировании.", 'content_kk' => "Екілік жүйемен байланыс. Бағдарламалауда қолдану.", 'video_url' => 'https://www.youtube.com/watch?v=r-7n_L0L2yc', 'video_id' => 'r-7n_L0L2yc', 'order' => 3],
                    ['title' => 'Арифметика в разных системах', 'title_kk' => 'Әр түрлі жүйелерде арифметика', 'content' => "Сложение, вычитание в двоичной системе. Контроль переполнения.", 'content_kk' => "Екілік жүйеде қосу, алу. Толып кетуді бақылау.", 'video_url' => 'https://www.youtube.com/watch?v=7kXNd7q2dQY', 'video_id' => '7kXNd7q2dQY', 'order' => 4],
                ],
                'quiz' => ['title' => 'Квиз: Системы счисления', 'title_kk' => 'Квиз: Санау жүйелері', 'passing_percent' => 70, 'questions' => [
                    ['text' => 'В двоичной системе основание:', 'text_kk' => 'Екілік жүйеде негіз:', 'options' => ['A' => '10', 'B' => '2', 'C' => '8'], 'options_kk' => ['A' => '10', 'B' => '2', 'C' => '8'], 'correct' => 'B'],
                    ['text' => 'Бит может принимать значения:', 'text_kk' => 'Бит қабылдай алады:', 'options' => ['A' => '0–9', 'B' => '0 и 1', 'C' => 'Только 1'], 'options_kk' => ['A' => '0–9', 'B' => '0 және 1', 'C' => 'Тек 1'], 'correct' => 'B'],
                ]],
            ],
            [
                'title' => 'Основы C++',
                'title_kk' => 'C++ негіздері',
                'description' => 'Синтаксис C++, переменные, типы, ветвления, циклы, функции.',
                'description_kk' => 'C++ синтаксисі, айнымалылар, типтер, тармақталу, циклдар, функциялар.',
                'is_revision' => false,
                'lessons' => [
                    ['title' => 'Первая программа на C++', 'title_kk' => 'C++ бойынша бірінші бағдарлама', 'content' => "Структура программы. cout, cin. Компиляция и запуск.", 'content_kk' => "Бағдарлама құрылымы. cout, cin. Компиляция және іске қосу.", 'video_url' => 'https://www.youtube.com/watch?v=1OVVvHVrF0s', 'video_id' => '1OVVvHVrF0s', 'order' => 1],
                    ['title' => 'Переменные и типы в C++', 'title_kk' => 'C++ айнымалылар мен типтер', 'content' => "int, double, char, string. Объявление и инициализация.", 'content_kk' => "int, double, char, string. Мәлімдеу және инициализация.", 'video_url' => 'https://www.youtube.com/watch?v=W8F9_2OHq2A', 'video_id' => 'W8F9_2OHq2A', 'order' => 2],
                    ['title' => 'Условия и циклы в C++', 'title_kk' => 'C++ шарттар мен циклдар', 'content' => "if, else, switch. for, while, do-while.", 'content_kk' => "if, else, switch. for, while, do-while.", 'video_url' => 'https://www.youtube.com/watch?v=r-7n_L0L2yc', 'video_id' => 'r-7n_L0L2yc', 'order' => 3],
                    ['title' => 'Функции в C++', 'title_kk' => 'C++ функциялар', 'content' => "Объявление и определение функции. Параметры и возврат значения.", 'content_kk' => "Функцияны мәлімдеу және анықтау. Параметрлер және мән қайтару.", 'video_url' => 'https://www.youtube.com/watch?v=3VnrAJnQp2c', 'video_id' => '3VnrAJnQp2c', 'order' => 4],
                ],
                'quiz' => ['title' => 'Квиз: Основы C++', 'title_kk' => 'Квиз: C++ негіздері', 'passing_percent' => 70, 'questions' => [
                    ['text' => 'Для вывода в консоль в C++ используют:', 'text_kk' => 'C++ консольге шығару үшін қолданылады:', 'options' => ['A' => 'print', 'B' => 'cout', 'C' => 'echo'], 'options_kk' => ['A' => 'print', 'B' => 'cout', 'C' => 'echo'], 'correct' => 'B'],
                    ['text' => 'Цикл for в C++ задаётся:', 'text_kk' => 'C++ for циклі беріледі:', 'options' => ['A' => 'Только условием', 'B' => 'Инициализацией, условием, шагом', 'C' => 'Только счётчиком'], 'options_kk' => ['A' => 'Тек шартпен', 'B' => 'Инициализация, шарт, қадам', 'C' => 'Тек санауышпен'], 'correct' => 'B'],
                ]],
            ],
            [
                'title' => 'Повторение основ',
                'title_kk' => 'Негіздерді қайталау',
                'description' => 'Повторение материала для тех, кто не прошёл вступительный тест.',
                'description_kk' => 'Кіру тестін тапсырмағандар үшін материалды қайталау.',
                'is_revision' => true,
                'lessons' => [
                    ['title' => 'Системы счисления: повторение', 'title_kk' => 'Санау жүйелері: қайталау', 'content' => "Двоичная система, перевод чисел.", 'content_kk' => "Екілік жүйе, сандарды аударма.", 'video_url' => 'https://www.youtube.com/watch?v=1OVVvHVrF0s', 'video_id' => '1OVVvHVrF0s', 'order' => 1],
                    ['title' => 'Основы программирования', 'title_kk' => 'Бағдарламалау негіздері', 'content' => "Переменные, условия, циклы.", 'content_kk' => "Айнымалылар, шарттар, циклдар.", 'video_url' => 'https://www.youtube.com/watch?v=W8F9_2OHq2A', 'video_id' => 'W8F9_2OHq2A', 'order' => 2],
                ],
                'quiz' => ['title' => 'Квиз: Повторение основ (10 класс)', 'title_kk' => 'Квиз: Негіздерді қайталау (10 сынып)', 'passing_percent' => 50, 'questions' => [
                    ['text' => 'Двоичная система имеет основание:', 'text_kk' => 'Екілік жүйенің негізі:', 'options' => ['A' => '10', 'B' => '2', 'C' => '16'], 'options_kk' => ['A' => '10', 'B' => '2', 'C' => '16'], 'correct' => 'B'],
                    ['text' => 'Переменная хранит:', 'text_kk' => 'Айнымалы сақтайды:', 'options' => ['A' => 'Только числа', 'B' => 'Данные', 'C' => 'Текст программы'], 'options_kk' => ['A' => 'Тек сандар', 'B' => 'Деректер', 'C' => 'Бағдарлама мәтіні'], 'correct' => 'B'],
                ]],
            ],
        ]);
    }

    private function sectionsGrade11(): array
    {
        return array_merge([$this->projectActivitySection()], [
            [
                'title' => 'Базы данных',
                'title_kk' => 'Деректер қорлары',
                'description' => 'Реляционные БД, таблицы, связи. Язык SQL: SELECT, INSERT, UPDATE, DELETE.',
                'description_kk' => 'Реляциялық ДҚ, кестелер, байланыстар. SQL тілі: SELECT, INSERT, UPDATE, DELETE.',
                'is_revision' => false,
                'lessons' => [
                    ['title' => 'Введение в базы данных', 'title_kk' => 'Деректер қорларына кіріспе', 'content' => "База данных — хранилище данных. СУБД. Реляционная модель: таблицы, строки, столбцы.", 'content_kk' => "Деректер қоры — деректер қоймасы. ДҚББ. Реляциялық модель: кестелер, жолдар, бағандар.", 'video_url' => 'https://www.youtube.com/watch?v=HXV3zeQKqGY', 'video_id' => 'HXV3zeQKqGY', 'order' => 1],
                    ['title' => 'Проектирование БД', 'title_kk' => 'ДҚ жобалау', 'content' => "Первичный ключ, внешний ключ. Связи один-ко-многим. Нормализация.", 'content_kk' => "Бірінші кілт, сыртқы кілт. Бір-көп байланыстар. Нормализация.", 'video_url' => 'https://www.youtube.com/watch?v=8WEtxJ4-sh4', 'video_id' => '8WEtxJ4-sh4', 'order' => 2],
                    ['title' => 'Язык SQL', 'title_kk' => 'SQL тілі', 'content' => "SELECT, WHERE, ORDER BY, LIMIT. Агрегатные функции. Группировка.", 'content_kk' => "SELECT, WHERE, ORDER BY, LIMIT. Агрегаттық функциялар. Топтау.", 'video_url' => 'https://www.youtube.com/watch?v=1uqci6BXLM8', 'video_id' => '1uqci6BXLM8', 'order' => 3],
                    ['title' => 'Изменение данных: INSERT, UPDATE, DELETE', 'title_kk' => 'Деректерді өзгерту: INSERT, UPDATE, DELETE', 'content' => "Добавление, обновление и удаление записей. Транзакции.", 'content_kk' => "Жазба қосу, жаңарту және жою. Транзакциялар.", 'video_url' => 'https://www.youtube.com/watch?v=2VBw9dX3L8E', 'video_id' => '2VBw9dX3L8E', 'order' => 4],
                ],
                'quiz' => ['title' => 'Квиз: Базы данных', 'title_kk' => 'Квиз: Деректер қорлары', 'passing_percent' => 70, 'questions' => [
                    ['text' => 'Реляционная БД хранит данные в виде:', 'text_kk' => 'Реляциялық ДҚ деректерді сақтайды:', 'options' => ['A' => 'Одного файла', 'B' => 'Таблиц и связей', 'C' => 'Только в облаке'], 'options_kk' => ['A' => 'Бір файл', 'B' => 'Кестелер мен байланыстар', 'C' => 'Тек бұлтта'], 'correct' => 'B'],
                    ['text' => 'Язык запросов к БД:', 'text_kk' => 'ДҚ сұраулар тілі:', 'options' => ['A' => 'Python', 'B' => 'SQL', 'C' => 'HTML'], 'options_kk' => ['A' => 'Python', 'B' => 'SQL', 'C' => 'HTML'], 'correct' => 'B'],
                ]],
            ],
            [
                'title' => 'Создание проектов',
                'title_kk' => 'Жобалар жасау',
                'description' => 'Этапы учебного проекта. Постановка задачи, дизайн, реализация, тестирование.',
                'description_kk' => 'Оқу жобасы кезеңдері. Есеп қою, дизайн, іске асыру, тестілеу.',
                'is_revision' => false,
                'lessons' => [
                    ['title' => 'Постановка задачи и план', 'title_kk' => 'Есеп қою және жоспар', 'content' => "Определение цели проекта. Анализ требований. План этапов и сроки.", 'content_kk' => "Жоба мақсатын анықтау. Талаптарды талдау. Кезеңдер мен мерзімдер жоспары.", 'video_url' => 'https://www.youtube.com/watch?v=J5bQ8c4ofx8', 'video_id' => 'J5bQ8c4ofx8', 'order' => 1],
                    ['title' => 'Проектирование и реализация', 'title_kk' => 'Жобалау және іске асыру', 'content' => "Архитектура решения. Выбор технологий. Написание кода и документации.", 'content_kk' => "Шешім сәулеті. Технологияларды таңдау. Код және құжаттама жазу.", 'video_url' => 'https://www.youtube.com/watch?v=PlxWf493en4', 'video_id' => 'PlxWf493en4', 'order' => 2],
                    ['title' => 'Тестирование и отладка', 'title_kk' => 'Тестілеу және жөндеу', 'content' => "Виды тестов. Поиск и исправление ошибок. Резервные копии.", 'content_kk' => "Тест түрлері. Қателерді іздеу және түзету. Резервті көшірмелер.", 'video_url' => 'https://www.youtube.com/watch?v=3VnrAJnQp2c', 'video_id' => '3VnrAJnQp2c', 'order' => 3],
                    ['title' => 'Презентация проекта', 'title_kk' => 'Жобаны ұсыну', 'content' => "Подготовка доклада. Демонстрация результата. Рефлексия и доработки.", 'content_kk' => "Доклад дайындау. Нәтижені көрсету. Рефлексия және жетілдіру.", 'video_url' => 'https://www.youtube.com/watch?v=4_4RnpL6f_A', 'video_id' => '4_4RnpL6f_A', 'order' => 4],
                ],
                'quiz' => ['title' => 'Квиз: Создание проектов', 'title_kk' => 'Квиз: Жобалар жасау', 'passing_percent' => 70, 'questions' => [
                    ['text' => 'Первый этап проекта:', 'text_kk' => 'Жобаның бірінші кезеңі:', 'options' => ['A' => 'Тестирование', 'B' => 'Постановка задачи', 'C' => 'Презентация'], 'options_kk' => ['A' => 'Тестілеу', 'B' => 'Есеп қою', 'C' => 'Ұсыну'], 'correct' => 'B'],
                    ['text' => 'Тестирование нужно для:', 'text_kk' => 'Тестілеу қажет:', 'options' => ['A' => 'Ускорения', 'B' => 'Поиска ошибок', 'C' => 'Удаления кода'], 'options_kk' => ['A' => 'Жылдамдату', 'B' => 'Қателерді іздеу', 'C' => 'Кодты жою'], 'correct' => 'B'],
                ]],
            ],
            [
                'title' => 'Повторение основ',
                'title_kk' => 'Негіздерді қайталау',
                'description' => 'Повторение материала для тех, кто не прошёл вступительный тест.',
                'description_kk' => 'Кіру тестін тапсырмағандар үшін материалды қайталау.',
                'is_revision' => true,
                'lessons' => [
                    ['title' => 'Базы данных: повторение', 'title_kk' => 'Деректер қорлары: қайталау', 'content' => "Реляционная БД, таблицы, SQL.", 'content_kk' => "Реляциялық ДҚ, кестелер, SQL.", 'video_url' => 'https://www.youtube.com/watch?v=HXV3zeQKqGY', 'video_id' => 'HXV3zeQKqGY', 'order' => 1],
                    ['title' => 'Этапы проекта', 'title_kk' => 'Жоба кезеңдері', 'content' => "План, реализация, тестирование, презентация.", 'content_kk' => "Жоспар, іске асыру, тестілеу, ұсыну.", 'video_url' => 'https://www.youtube.com/watch?v=J5bQ8c4ofx8', 'video_id' => 'J5bQ8c4ofx8', 'order' => 2],
                ],
                'quiz' => ['title' => 'Квиз: Повторение основ (11 класс)', 'title_kk' => 'Квиз: Негіздерді қайталау (11 сынып)', 'passing_percent' => 50, 'questions' => [
                    ['text' => 'SQL используется для:', 'text_kk' => 'SQL қолданылады:', 'options' => ['A' => 'Веб-дизайна', 'B' => 'Запросов к БД', 'C' => 'Игр'], 'options_kk' => ['A' => 'Веб-дизайн', 'B' => 'ДҚ сұраулары', 'C' => 'Ойындар'], 'correct' => 'B'],
                    ['text' => 'После реализации идёт:', 'text_kk' => 'Іске асырғаннан кейін:', 'options' => ['A' => 'Только презентация', 'B' => 'Тестирование', 'C' => 'Ничего'], 'options_kk' => ['A' => 'Тек ұсыну', 'B' => 'Тестілеу', 'C' => 'Ештеңе'], 'correct' => 'B'],
                ]],
            ],
        ]);
    }

    /**
     * Раздел «Проектная деятельность» — идёт первым в курсе, выделяется в интерфейсе (is_featured).
     */
    private function projectActivitySection(): array
    {
        return [
            'title' => 'Проектная деятельность на уроках информатики',
            'title_kk' => 'Ақпараттану сабақтарындағы жобалық қызмет',
            'description' => 'Теория и практика проектной деятельности: цели, задачи, виды проектов, этапы, роль учителя.',
            'description_kk' => 'Жобалық қызметтің теориясы мен тәжірибесі: мақсаттар, міндеттер, жоба түрлері, кезеңдер, мұғалім рөлі.',
            'is_revision' => false,
            'is_featured' => true,
            'lessons' => [
                [
                    'title' => 'Проектная деятельность на уроках информатики',
                    'title_kk' => 'Ақпараттану сабақтарындағы жобалық қызмет',
                    'content' => ProjectActivityInformaticsMarkdown::contentRu(),
                    'content_kk' => ProjectActivityInformaticsMarkdown::contentKk(),
                    'order' => 1,
                ],
            ],
        ];
    }
}
