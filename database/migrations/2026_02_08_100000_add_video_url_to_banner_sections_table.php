<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banner_sections', function (Blueprint $table) {
            if (!Schema::hasColumn('banner_sections', 'video_url')) {
                $table->string('video_url', 500)->nullable()->after('video');
            }
        });
    }

    public function down(): void
    {
        Schema::table('banner_sections', function (Blueprint $table) {
            $table->dropColumn('video_url');
        });
    }
};
