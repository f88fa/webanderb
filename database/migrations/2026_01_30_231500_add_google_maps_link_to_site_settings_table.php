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
        // Add Google Maps link setting if it doesn't exist
        $exists = DB::table('site_settings')->where('setting_key', 'google_maps_link')->exists();
        if (!$exists) {
            DB::table('site_settings')->insert([
                'setting_key' => 'google_maps_link',
                'setting_value' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('site_settings')->where('setting_key', 'google_maps_link')->delete();
    }
};

