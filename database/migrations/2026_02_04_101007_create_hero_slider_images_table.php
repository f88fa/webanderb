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
        Schema::create('hero_slider_images', function (Blueprint $table) {
            $table->id();
            $table->string('image'); // مسار الصورة
            $table->string('title')->nullable(); // عنوان الصورة (اختياري)
            $table->text('description')->nullable(); // وصف الصورة (اختياري)
            $table->integer('order')->default(0); // ترتيب الصورة
            $table->boolean('is_active')->default(true); // حالة التفعيل
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hero_slider_images');
    }
};
