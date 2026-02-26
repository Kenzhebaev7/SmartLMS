<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            if (!Schema::hasColumn('threads', 'hidden_at')) {
                $table->timestamp('hidden_at')->nullable()->after('body');
            }
        });
        Schema::table('comments', function (Blueprint $table) {
            if (!Schema::hasColumn('comments', 'hidden_at')) {
                $table->timestamp('hidden_at')->nullable()->after('body');
            }
        });
    }

    public function down(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            if (Schema::hasColumn('threads', 'hidden_at')) {
                $table->dropColumn('hidden_at');
            }
        });
        Schema::table('comments', function (Blueprint $table) {
            if (Schema::hasColumn('comments', 'hidden_at')) {
                $table->dropColumn('hidden_at');
            }
        });
    }
};
