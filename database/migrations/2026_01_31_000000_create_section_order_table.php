<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('section_order')) {
            return;
        }
        Schema::create('section_order', function (Blueprint $table) {
            $table->id();
            $table->string('section_key')->unique();
            $table->string('section_name');
            $table->integer('order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });

        // Insert default sections
        $defaultSections = [
            ['section_key' => 'about', 'section_name' => 'من نحن', 'order' => 1, 'is_visible' => true],
            ['section_key' => 'vision_mission', 'section_name' => 'الرؤية والرسالة', 'order' => 2, 'is_visible' => true],
            ['section_key' => 'banner_sections', 'section_name' => 'أقسام البانر', 'order' => 3, 'is_visible' => true],
            ['section_key' => 'services', 'section_name' => 'الخدمات', 'order' => 4, 'is_visible' => true],
            ['section_key' => 'projects', 'section_name' => 'المشاريع', 'order' => 5, 'is_visible' => true],
            ['section_key' => 'media', 'section_name' => 'المركز الإعلامي', 'order' => 6, 'is_visible' => true],
            ['section_key' => 'testimonials', 'section_name' => 'ماذا قالوا عنا', 'order' => 7, 'is_visible' => true],
            ['section_key' => 'partners', 'section_name' => 'الشركاء', 'order' => 8, 'is_visible' => true],
            ['section_key' => 'news', 'section_name' => 'الأخبار', 'order' => 9, 'is_visible' => true],
        ];

        foreach ($defaultSections as $section) {
            DB::table('section_order')->insert(array_merge($section, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_order');
    }
};

