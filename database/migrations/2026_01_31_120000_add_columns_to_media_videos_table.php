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
        Schema::table('media_videos', function (Blueprint $table) {
            $table->string('title')->nullable()->after('id');
            $table->string('youtube_url')->nullable()->after('title');
            $table->string('thumbnail')->nullable()->after('youtube_url');
            $table->integer('order')->default(0)->after('thumbnail');
            $table->boolean('is_active')->default(true)->after('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media_videos', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'youtube_url',
                'thumbnail',
                'order',
                'is_active',
            ]);
        });
    }
};

