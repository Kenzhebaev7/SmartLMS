<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->text('text_kk')->nullable()->after('text');
            $table->json('options_kk')->nullable()->after('options');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->string('title_kk')->nullable()->after('title');
            $table->text('content_kk')->nullable()->after('content');
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->string('title_kk')->nullable()->after('title');
            $table->text('description_kk')->nullable()->after('description');
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->string('title_kk')->nullable()->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['text_kk', 'options_kk']);
        });
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn(['title_kk', 'content_kk']);
        });
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn(['title_kk', 'description_kk']);
        });
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn('title_kk');
        });
    }
};
