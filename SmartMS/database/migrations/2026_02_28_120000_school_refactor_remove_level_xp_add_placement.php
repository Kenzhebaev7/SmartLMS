<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'level')) {
                $table->dropColumn('level');
            }
            if (Schema::hasColumn('users', 'xp')) {
                $table->dropColumn('xp');
            }
            if (!Schema::hasColumn('users', 'placement_passed')) {
                $table->boolean('placement_passed')->nullable()->after('grade')
                    ->comment('null=не проходил, true=прошел >50%, false=не прошел — при false доступ только к повторению основ');
            }
        });

        Schema::table('sections', function (Blueprint $table) {
            if (!Schema::hasColumn('sections', 'is_revision')) {
                $table->boolean('is_revision')->default(false)->after('grade')
                    ->comment('Раздел «Повторение основ» для не сдавших тест');
            }
        });

        Schema::table('lessons', function (Blueprint $table) {
            if (!Schema::hasColumn('lessons', 'grade')) {
                $table->unsignedTinyInteger('grade')->nullable()->after('section_id')
                    ->comment('Класс (9,10,11) — если null, наследуется от раздела');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'placement_passed')) {
                $table->dropColumn('placement_passed');
            }
            if (!Schema::hasColumn('users', 'level')) {
                $table->string('level')->nullable()->after('grade');
            }
            if (!Schema::hasColumn('users', 'xp')) {
                $table->unsignedInteger('xp')->default(0)->after('grade');
            }
        });

        Schema::table('sections', function (Blueprint $table) {
            if (Schema::hasColumn('sections', 'is_revision')) {
                $table->dropColumn('is_revision');
            }
        });

        Schema::table('lessons', function (Blueprint $table) {
            if (Schema::hasColumn('lessons', 'grade')) {
                $table->dropColumn('grade');
            }
        });
    }
};
