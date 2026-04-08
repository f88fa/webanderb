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
        Schema::table('media_slides', function (Blueprint $table) {
            // Add columns if they don't exist
            if (!Schema::hasColumn('media_slides', 'title')) {
                $table->string('title')->nullable()->after('id');
            }
            if (!Schema::hasColumn('media_slides', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            if (!Schema::hasColumn('media_slides', 'image')) {
                $table->string('image')->nullable()->after('description');
            }
            if (!Schema::hasColumn('media_slides', 'link')) {
                $table->string('link')->nullable()->after('image');
            }
            if (!Schema::hasColumn('media_slides', 'type')) {
                $table->enum('type', ['image', 'video'])->default('image')->after('link');
            }
            if (!Schema::hasColumn('media_slides', 'video_url')) {
                $table->string('video_url')->nullable()->after('type');
            }
            if (!Schema::hasColumn('media_slides', 'order')) {
                $table->integer('order')->default(0)->after('video_url');
            }
            if (!Schema::hasColumn('media_slides', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('order');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media_slides', function (Blueprint $table) {
            $columns = ['title', 'description', 'image', 'link', 'type', 'video_url', 'order', 'is_active'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('media_slides', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

