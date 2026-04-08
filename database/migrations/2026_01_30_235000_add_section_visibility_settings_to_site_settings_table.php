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
        // Add default section visibility settings if they don't exist
        $defaultSettings = [
            'section_about_visible' => '1',
            'section_vision_mission_visible' => '1',
            'section_services_visible' => '1',
            'section_media_visible' => '1',
            'section_projects_visible' => '1',
            'section_testimonials_visible' => '1',
            'section_partners_visible' => '1',
            'section_news_visible' => '1',
            'section_banner_sections_visible' => '1',
        ];

        foreach ($defaultSettings as $key => $value) {
            $exists = DB::table('site_settings')->where('setting_key', $key)->exists();
            if (!$exists) {
                DB::table('site_settings')->insert([
                    'setting_key' => $key,
                    'setting_value' => $value,
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
        DB::table('site_settings')->whereIn('setting_key', [
            'section_about_visible',
            'section_vision_mission_visible',
            'section_services_visible',
            'section_media_visible',
            'section_projects_visible',
            'section_testimonials_visible',
            'section_partners_visible',
            'section_news_visible',
            'section_banner_sections_visible',
        ])->delete();
    }
};

