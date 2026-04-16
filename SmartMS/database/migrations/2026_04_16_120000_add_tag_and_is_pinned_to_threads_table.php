<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            if (!Schema::hasColumn('threads', 'tag')) {
                $table->string('tag', 32)->nullable()->after('body');
            }

            if (!Schema::hasColumn('threads', 'is_pinned')) {
                $table->boolean('is_pinned')->default(false)->after('tag');
            }
        });
    }

    public function down(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            if (Schema::hasColumn('threads', 'is_pinned')) {
                $table->dropColumn('is_pinned');
            }

            if (Schema::hasColumn('threads', 'tag')) {
                $table->dropColumn('tag');
            }
        });
    }
};
