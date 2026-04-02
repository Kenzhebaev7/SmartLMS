<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('grade')->nullable()->after('level')->comment('9, 10, 11 — класс для школьников');
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->unsignedTinyInteger('grade')->nullable()->after('order')->comment('9, 10, 11 — для какого класса раздел');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('grade');
        });
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn('grade');
        });
    }
};
