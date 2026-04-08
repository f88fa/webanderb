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
        // Add dashboard background gradient setting if it doesn't exist
        $exists = DB::table('site_settings')->where('setting_key', 'dashboard_bg_gradient')->exists();
        if (!$exists) {
            DB::table('site_settings')->insert([
                'setting_key' => 'dashboard_bg_gradient',
                'setting_value' => 'linear-gradient(180deg, #0F3D2E 0%, #1F6B4F 30%, #5FB38E 60%, #A8DCC3 85%, #FFFFFF 100%)',
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
        DB::table('site_settings')->where('setting_key', 'dashboard_bg_gradient')->delete();
    }
};

