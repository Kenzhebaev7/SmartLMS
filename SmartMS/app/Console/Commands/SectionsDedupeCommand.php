<?php

namespace App\Console\Commands;

use App\Services\SectionDuplicateCleanup;
use Illuminate\Console\Command;

class SectionsDedupeCommand extends Command
{
    protected $signature = 'sections:dedupe';

    protected $description = 'Удалить дубли разделов (одинаковый курс по смыслу) и нормализовать заголовки';

    public function handle(SectionDuplicateCleanup $cleanup): int
    {
        $this->info('Очистка дублей разделов…');
        $cleanup->run();
        $this->info('Готово. Обновите страницу кабинета.');

        return self::SUCCESS;
    }
}
