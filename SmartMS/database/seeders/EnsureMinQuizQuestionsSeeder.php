<?php

namespace Database\Seeders;

use App\Models\Quiz;
use Illuminate\Database\Seeder;

class EnsureMinQuizQuestionsSeeder extends Seeder
{
    private const MIN_QUESTIONS = 10;

    public function run(): void
    {
        Quiz::with('section', 'questions')->get()->each(function (Quiz $quiz) {
            $count = $quiz->questions()->count();
            if ($count >= self::MIN_QUESTIONS) {
                return;
            }

            $need = self::MIN_QUESTIONS - $count;
            $startOrder = (int) ($quiz->questions()->max('order') ?? -1) + 1;

            $extra = $this->genericBilingualQuestions($startOrder, $need);

            foreach ($extra as $q) {
                $quiz->questions()->create([
                    'text' => $q['text'],
                    'text_kk' => $q['text_kk'],
                    'type' => 'single',
                    'options' => $q['options'],
                    'options_kk' => $q['options_kk'],
                    'correct_answer' => [$q['correct']],
                    'order' => $q['order'],
                ]);
            }
        });
    }

    /**
     * @return array<int, array{text: string, text_kk: string, options: array<string,string>, options_kk: array<string,string>, correct: string, order: int}>
     */
    private function genericBilingualQuestions(int $startOrder, int $need): array
    {
        $bank = [
            [
                'text' => 'Что такое информация?',
                'text_kk' => 'Ақпарат деген не?',
                'options' => [
                    'A' => 'Случайные числа',
                    'B' => 'Сведения об объектах и явлениях',
                    'C' => 'Только текст',
                ],
                'options_kk' => [
                    'A' => 'Кездейсоқ сандар',
                    'B' => 'Объектілер мен құбылыстар туралы мәліметтер',
                    'C' => 'Тек мәтін',
                ],
                'correct' => 'B',
            ],
            [
                'text' => 'Алгоритм — это:',
                'text_kk' => 'Алгоритм — бұл:',
                'options' => [
                    'A' => 'Произвольный набор команд без порядка',
                    'B' => 'Точная последовательность шагов для решения задачи',
                    'C' => 'Только программа на компьютере',
                ],
                'options_kk' => [
                    'A' => 'Реті жоқ командалар жиыны',
                    'B' => 'Есепті шешуге арналған қадамдар тізбегі',
                    'C' => 'Тек компьютердегі бағдарлама',
                ],
                'correct' => 'B',
            ],
            [
                'text' => 'Какая операция делает повторение кода?',
                'text_kk' => 'Қай амал кодты қайталайды?',
                'options' => [
                    'A' => 'Условие if',
                    'B' => 'Цикл for / while',
                    'C' => 'Оператор return',
                ],
                'options_kk' => [
                    'A' => 'if шарты',
                    'B' => 'for / while циклдары',
                    'C' => 'return операторы',
                ],
                'correct' => 'B',
            ],
            [
                'text' => 'Что хранит тип данных bool?',
                'text_kk' => 'bool деректер типі нені сақтайды?',
                'options' => [
                    'A' => 'Только числа',
                    'B' => 'Истину или ложь',
                    'C' => 'Только строки',
                ],
                'options_kk' => [
                    'A' => 'Тек сандарды',
                    'B' => 'Ақиқат немесе жалған мәнін',
                    'C' => 'Тек жолдарды',
                ],
                'correct' => 'B',
            ],
            [
                'text' => 'Что такое база данных?',
                'text_kk' => 'Деректер қоры деген не?',
                'options' => [
                    'A' => 'Случайный набор файлов',
                    'B' => 'Организованное хранилище данных',
                    'C' => 'Только один текстовый документ',
                ],
                'options_kk' => [
                    'A' => 'Кездейсоқ файлдар жиыны',
                    'B' => 'Ұйымдастырылған деректер қоймасы',
                    'C' => 'Тек бір мәтіндік құжат',
                ],
                'correct' => 'B',
            ],
            [
                'text' => 'Язык SQL нужен для:',
                'text_kk' => 'SQL тілі не үшін керек?',
                'options' => [
                    'A' => 'Оформления страниц',
                    'B' => 'Запросов к базам данных',
                    'C' => 'Создания изображений',
                ],
                'options_kk' => [
                    'A' => 'Беттерді безендіруге',
                    'B' => 'Деректер қорына сұраулар жасауға',
                    'C' => 'Суреттер жасауға',
                ],
                'correct' => 'B',
            ],
            [
                'text' => 'HTML используется для:',
                'text_kk' => 'HTML не үшін қолданылады?',
                'options' => [
                    'A' => 'Разметки веб-страниц',
                    'B' => 'Хранения данных в БД',
                    'C' => 'Компиляции программ',
                ],
                'options_kk' => [
                    'A' => 'Веб-беттерді белгілеу үшін',
                    'B' => 'ДҚ-да деректерді сақтауға',
                    'C' => 'Бағдарламаларды компиляциялауға',
                ],
                'correct' => 'A',
            ],
            [
                'text' => 'CSS отвечает за:',
                'text_kk' => 'CSS жауап береді:',
                'options' => [
                    'A' => 'Логику на сервере',
                    'B' => 'Внешний вид страницы',
                    'C' => 'Работу процессора',
                ],
                'options_kk' => [
                    'A' => 'Сервердегі логикаға',
                    'B' => 'Беттің сыртқы көрінісіне',
                    'C' => 'Процессор жұмысына',
                ],
                'correct' => 'B',
            ],
            [
                'text' => 'Объект в ООП — это:',
                'text_kk' => 'ООП-дегі объект — бұл:',
                'options' => [
                    'A' => 'Файл на диске',
                    'B' => 'Экземпляр класса',
                    'C' => 'Переменная без типа',
                ],
                'options_kk' => [
                    'A' => 'Дискідегі файл',
                    'B' => 'Класс данасы',
                    'C' => 'Типі жоқ айнымалы',
                ],
                'correct' => 'B',
            ],
            [
                'text' => 'Инкапсуляция нужна для того, чтобы:',
                'text_kk' => 'Инкапсуляция не үшін қажет:',
                'options' => [
                    'A' => 'Скрыть детали реализации и защитить данные',
                    'B' => 'Ускорить интернет',
                    'C' => 'Удалить все классы',
                ],
                'options_kk' => [
                    'A' => 'Іске асыру детальдарын жасырып, деректерді қорғау үшін',
                    'B' => 'Интернетті жылдамдату үшін',
                    'C' => 'Барлық кластарды жою үшін',
                ],
                'correct' => 'A',
            ],
        ];

        $result = [];
        for ($i = 0; $i < $need; $i++) {
            $tpl = $bank[$i % count($bank)];
            $tpl['order'] = $startOrder + $i;
            $result[] = $tpl;
        }

        return $result;
    }
}

