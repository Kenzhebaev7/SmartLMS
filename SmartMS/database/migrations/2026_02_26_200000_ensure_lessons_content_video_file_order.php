<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            if (!Schema::hasColumn('lessons', 'content')) {
                $table->text('content')->nullable()->after('title');
            }
            if (!Schema::hasColumn('lessons', 'video_url')) {
                $table->string('video_url')->nullable()->after('content');
            }
            if (!Schema::hasColumn('lessons', 'file_path')) {
                $table->string('file_path')->nullable()->after('video_url');
            }
            if (!Schema::hasColumn('lessons', 'order')) {
                $table->unsignedInteger('order')->default(0)->after('file_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            if (Schema::hasColumn('lessons', 'video_url')) {
                $table->dropColumn('video_url');
            }
            if (Schema::hasColumn('lessons', 'file_path')) {
                $table->dropColumn('file_path');
            }
            if (Schema::hasColumn('lessons', 'order')) {
                $table->dropColumn('order');
            }
            // content не удаляем в down — может быть единственным полем из первой миграции
        });
    }
};
