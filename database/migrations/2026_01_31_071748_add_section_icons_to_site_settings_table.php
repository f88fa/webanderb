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
            'section_about_icon' => 'fas fa-users',
            'section_vision_mission_icon' => 'fas fa-eye',
            'section_services_icon' => 'fas fa-concierge-bell',
            'section_projects_icon' => 'fas fa-project-diagram',
            'section_media_icon' => 'fas fa-video',
            'section_testimonials_icon' => 'fas fa-quote-right',
            'section_partners_icon' => 'fas fa-handshake',
            'section_news_icon' => 'fas fa-newspaper',
            'section_banner_sections_icon' => 'fas fa-images',
            'section_staff_icon' => 'fas fa-user-tie',
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
            'section_about_icon',
            'section_vision_mission_icon',
            'section_services_icon',
            'section_projects_icon',
            'section_media_icon',
            'section_testimonials_icon',
            'section_partners_icon',
            'section_news_icon',
            'section_banner_sections_icon',
            'section_staff_icon',
        ];

        DB::table('site_settings')->whereIn('setting_key', $sections)->delete();
    }
};
