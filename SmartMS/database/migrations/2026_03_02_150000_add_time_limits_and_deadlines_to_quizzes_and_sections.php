<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            if (!Schema::hasColumn('quizzes', 'time_limit_seconds')) {
                $table->unsignedInteger('time_limit_seconds')
                    ->nullable()
                    ->after('passing_percent')
                    ->comment('Time limit for quiz in seconds; null = no limit');
            }
            if (!Schema::hasColumn('quizzes', 'deadline_at')) {
                $table->timestamp('deadline_at')
                    ->nullable()
                    ->after('time_limit_seconds')
                    ->comment('Optional deadline for passing this quiz');
            }
        });

        Schema::table('sections', function (Blueprint $table) {
            if (!Schema::hasColumn('sections', 'deadline_at')) {
                $table->timestamp('deadline_at')
                    ->nullable()
                    ->after('grade')
                    ->comment('Optional deadline for finishing this section');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            if (Schema::hasColumn('quizzes', 'deadline_at')) {
                $table->dropColumn('deadline_at');
            }
            if (Schema::hasColumn('quizzes', 'time_limit_seconds')) {
                $table->dropColumn('time_limit_seconds');
            }
        });

        Schema::table('sections', function (Blueprint $table) {
            if (Schema::hasColumn('sections', 'deadline_at')) {
                $table->dropColumn('deadline_at');
            }
        });
    }
};

