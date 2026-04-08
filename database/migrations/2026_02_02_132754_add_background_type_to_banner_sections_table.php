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
            $table->string('background_type')->default('white')->after('is_active'); // 'white' or 'image'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banner_sections', function (Blueprint $table) {
            $table->dropColumn('background_type');
        });
    }
};
