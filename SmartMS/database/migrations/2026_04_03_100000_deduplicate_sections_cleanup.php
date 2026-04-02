<?php

use App\Services\SectionDuplicateCleanup;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        app(SectionDuplicateCleanup::class)->run();
    }

    public function down(): void
    {
        // Необратимо: удалённые дубли не восстанавливаем.
    }
};
