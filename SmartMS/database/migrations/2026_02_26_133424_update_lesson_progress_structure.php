<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lesson_progress', function (Blueprint $table) {
            if (! Schema::hasColumn('lesson_progress', 'user_id')) {
                $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();
            }

            if (! Schema::hasColumn('lesson_progress', 'lesson_key')) {
                $table->string('lesson_key')->after('user_id');
            }

            if (! Schema::hasColumn('lesson_progress', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('lesson_key');
            }

            $table->unique(['user_id', 'lesson_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lesson_progress', function (Blueprint $table) {
            if (Schema::hasColumn('lesson_progress', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }

            if (Schema::hasColumn('lesson_progress', 'lesson_key')) {
                $table->dropColumn('lesson_key');
            }

            if (Schema::hasColumn('lesson_progress', 'completed_at')) {
                $table->dropColumn('completed_at');
            }
        });
    }
};
