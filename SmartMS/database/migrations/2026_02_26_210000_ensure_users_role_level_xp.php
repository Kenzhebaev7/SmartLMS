<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('student')->after('password');
            }
            if (!Schema::hasColumn('users', 'level')) {
                $table->string('level')->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'xp')) {
                $table->unsignedInteger('xp')->default(0)->after('level');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'xp')) {
                $table->dropColumn('xp');
            }
            if (Schema::hasColumn('users', 'level')) {
                $table->dropColumn('level');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
