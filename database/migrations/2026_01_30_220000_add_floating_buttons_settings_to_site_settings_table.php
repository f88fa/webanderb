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
        // Add default floating buttons settings if they don't exist
        $defaultSettings = [
            'floating_whatsapp_enabled' => '1',
            'floating_whatsapp_number' => '',
            'floating_donate_enabled' => '1',
            'floating_donate_link' => '',
            'floating_donate_text' => 'تبرع الآن',
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
            'floating_whatsapp_enabled',
            'floating_whatsapp_number',
            'floating_donate_enabled',
            'floating_donate_link',
            'floating_donate_text',
        ])->delete();
    }
};

