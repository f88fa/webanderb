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
        // Add default color settings if they don't exist
        $defaultColors = [
            'site_primary_color' => '#5FB38E',
            'site_primary_dark' => '#1F6B4F',
            'site_secondary_color' => '#A8DCC3',
            'site_accent_color' => '#5FB38E',
        ];

        foreach ($defaultColors as $key => $value) {
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
            'site_primary_color',
            'site_primary_dark',
            'site_secondary_color',
            'site_accent_color',
        ])->delete();
    }
};
