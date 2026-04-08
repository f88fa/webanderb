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
        if (Schema::hasTable('media_slides')) {
            Schema::table('media_slides', function (Blueprint $table) {
                if (!Schema::hasColumn('media_slides', 'type')) {
                    $table->string('type')->default('image')->after('id'); // 'image' or 'video'
                }
                if (!Schema::hasColumn('media_slides', 'video_url')) {
                    // Add at the end if columns don't exist
                    $table->text('video_url')->nullable(); // YouTube or direct video URL
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media_slides', function (Blueprint $table) {
            $table->dropColumn(['type', 'video_url']);
        });
    }
};

