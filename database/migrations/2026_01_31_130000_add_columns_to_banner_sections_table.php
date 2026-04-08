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
            $table->string('title')->nullable()->after('id');
            $table->string('image')->nullable()->after('title');
            $table->string('link')->nullable()->after('image');
            $table->integer('order')->default(0)->after('link');
            $table->boolean('is_active')->default(true)->after('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banner_sections', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'image',
                'link',
                'order',
                'is_active',
            ]);
        });
    }
};

