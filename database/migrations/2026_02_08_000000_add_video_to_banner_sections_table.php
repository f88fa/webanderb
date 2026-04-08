<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('banner_sections', function (Blueprint $table) {
            if (!Schema::hasColumn('banner_sections', 'video')) {
                $table->string('video')->nullable()->after('image');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banner_sections', function (Blueprint $table) {
            $table->dropColumn('video');
        });
    }
};
