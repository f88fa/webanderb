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
        // Add dashboard color settings if they don't exist
        $defaultSettings = [
            'dashboard_primary_color' => '#5FB38E',
            'dashboard_primary_dark' => '#1F6B4F',
            'dashboard_secondary_color' => '#A8DCC3',
            'dashboard_accent_color' => '#5FB38E',
            'dashboard_sidebar_bg' => 'rgba(15, 61, 46, 0.95)',
            'dashboard_content_bg' => 'rgba(255, 255, 255, 0.05)',
            'dashboard_text_primary' => '#FFFFFF',
            'dashboard_text_secondary' => '#FFFFFF',
            'dashboard_border_color' => 'rgba(255, 255, 255, 0.1)',
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
            'dashboard_primary_color',
            'dashboard_primary_dark',
            'dashboard_secondary_color',
            'dashboard_accent_color',
            'dashboard_sidebar_bg',
            'dashboard_content_bg',
            'dashboard_text_primary',
            'dashboard_text_secondary',
            'dashboard_border_color',
        ])->delete();
    }
};

