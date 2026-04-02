<?php

use App\Services\SectionDuplicateCleanup;
use Illuminate\Database\Migrations\Migration;

/**
 * Дополнение к 2026_04_03_100000: выставить order у «Проектной деятельности» в начало курса.
 */
return new class extends Migration
{
    public function up(): void
    {
        app(SectionDuplicateCleanup::class)->fixProjectActivitySectionOrder();
    }

    public function down(): void
    {
        //
    }
};
