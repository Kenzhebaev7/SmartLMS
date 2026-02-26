<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            if (!Schema::hasColumn('lessons', 'section_id')) {
                $table->foreignId('section_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            }
            if (!Schema::hasColumn('lessons', 'video_url')) {
                $table->string('video_url')->nullable();
            }
            if (!Schema::hasColumn('lessons', 'file_path')) {
                $table->string('file_path')->nullable();
            }
            if (!Schema::hasColumn('lessons', 'order')) {
                $table->unsignedInteger('order')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            if (Schema::hasColumn('lessons', 'section_id')) {
                $table->dropForeign(['section_id']);
                $table->dropColumn('section_id');
            }
            if (Schema::hasColumn('lessons', 'video_url')) {
                $table->dropColumn('video_url');
            }
            if (Schema::hasColumn('lessons', 'file_path')) {
                $table->dropColumn('file_path');
            }
            if (Schema::hasColumn('lessons', 'order')) {
                $table->dropColumn('order');
            }
        });
    }
};
