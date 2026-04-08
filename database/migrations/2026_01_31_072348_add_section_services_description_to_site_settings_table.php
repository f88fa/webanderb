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
        $exists = DB::table('site_settings')->where('setting_key', 'section_services_description')->exists();
        if (!$exists) {
            DB::table('site_settings')->insert([
                'setting_key' => 'section_services_description',
                'setting_value' => 'نقدم لكم أفضل الخدمات بجودة عالية',
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
        DB::table('site_settings')->where('setting_key', 'section_services_description')->delete();
    }
};
