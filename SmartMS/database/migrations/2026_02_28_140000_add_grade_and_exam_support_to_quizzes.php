<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->unsignedTinyInteger('grade')->nullable()->after('section_id')->comment('9,10,11 — для экзаменационных квизов по классу');
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
        });
        Schema::table('quizzes', function (Blueprint $table) {
            $table->unsignedBigInteger('section_id')->nullable()->change();
        });
        Schema::table('quizzes', function (Blueprint $table) {
            $table->foreign('section_id')->references('id')->on('sections')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
        });
        Schema::table('quizzes', function (Blueprint $table) {
            $table->unsignedBigInteger('section_id')->nullable(false)->change();
        });
        Schema::table('quizzes', function (Blueprint $table) {
            $table->foreign('section_id')->references('id')->on('sections')->cascadeOnDelete();
        });
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn('grade');
        });
    }
};
