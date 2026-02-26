<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lesson_progress', function (Blueprint $table) {
            if (!Schema::hasColumn('lesson_progress', 'lesson_id')) {
                $table->foreignId('lesson_id')->nullable()->after('user_id')->constrained()->cascadeOnDelete();
            }
        });

        // Backfill: lesson_key that is numeric -> lesson_id
        if (Schema::hasColumn('lesson_progress', 'lesson_key')) {
            $rows = DB::table('lesson_progress')->whereNotNull('lesson_key')->get();
            foreach ($rows as $row) {
                if (is_numeric($row->lesson_key)) {
                    DB::table('lesson_progress')->where('id', $row->id)->update(['lesson_id' => (int) $row->lesson_key]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::table('lesson_progress', function (Blueprint $table) {
            if (Schema::hasColumn('lesson_progress', 'lesson_id')) {
                $table->dropForeign(['lesson_id']);
                $table->dropColumn('lesson_id');
            }
        });
    }
};
