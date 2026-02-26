<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            if (!Schema::hasColumn('threads', 'lesson_id')) {
                $table->foreignId('lesson_id')->nullable()->after('user_id')->constrained('lessons')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            if (Schema::hasColumn('threads', 'lesson_id')) {
                $table->dropForeign(['lesson_id']);
            }
        });
    }
};
