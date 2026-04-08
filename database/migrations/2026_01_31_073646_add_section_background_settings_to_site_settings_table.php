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
        // لا حاجة لإضافة أعمدة جديدة، سنستخدم site_settings table
        // فقط نضيف الإعدادات الافتراضية إذا لم تكن موجودة
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // حذف الإعدادات عند التراجع
        $sections = [
            'section_about_bg_image',
            'section_about_bg_opacity',
            'section_vision_mission_bg_image',
            'section_vision_mission_bg_opacity',
            'section_services_bg_image',
            'section_services_bg_opacity',
            'section_projects_bg_image',
            'section_projects_bg_opacity',
            'section_media_bg_image',
            'section_media_bg_opacity',
            'section_testimonials_bg_image',
            'section_testimonials_bg_opacity',
            'section_partners_bg_image',
            'section_partners_bg_opacity',
            'section_news_bg_image',
            'section_news_bg_opacity',
            'section_banner_sections_bg_image',
            'section_banner_sections_bg_opacity',
            'section_staff_bg_image',
            'section_staff_bg_opacity',
        ];

        DB::table('site_settings')->whereIn('setting_key', $sections)->delete();
    }
};
