<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            if (! Schema::hasColumn('lessons', 'video_url_kk')) {
                $table->string('video_url_kk', 500)->nullable()->after('video_id');
            }
            if (! Schema::hasColumn('lessons', 'video_id_kk')) {
                $table->string('video_id_kk', 32)->nullable()->after('video_url_kk');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            if (Schema::hasColumn('lessons', 'video_id_kk')) {
                $table->dropColumn('video_id_kk');
            }
            if (Schema::hasColumn('lessons', 'video_url_kk')) {
                $table->dropColumn('video_url_kk');
            }
        });
    }
};
