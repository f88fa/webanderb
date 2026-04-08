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
        $sections = [
            'section_about_title' => 'من نحن',
            'section_vision_mission_title' => 'رؤيتنا ورسالتنا',
            'section_services_title' => 'خدماتنا المميزة',
            'section_projects_title' => 'مشاريعنا المميزة',
            'section_media_title' => 'محتوى إعلامي مميز',
            'section_testimonials_title' => 'آراء عملائنا',
            'section_partners_title' => 'شركاؤنا الاستراتيجيون',
            'section_news_title' => 'آخر الأخبار',
        ];

        foreach ($sections as $key => $defaultValue) {
            $exists = DB::table('site_settings')->where('setting_key', $key)->exists();
            if (!$exists) {
                DB::table('site_settings')->insert([
                    'setting_key' => $key,
                    'setting_value' => $defaultValue,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $sections = [
            'section_about_title',
            'section_vision_mission_title',
            'section_services_title',
            'section_projects_title',
            'section_media_title',
            'section_testimonials_title',
            'section_partners_title',
            'section_news_title',
        ];

        DB::table('site_settings')->whereIn('setting_key', $sections)->delete();
    }
};
