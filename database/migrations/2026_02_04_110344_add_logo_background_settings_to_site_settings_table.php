<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // إضافة إعدادات خلفية اللوقو
        \App\Models\SiteSetting::updateOrCreate(
            ['setting_key' => 'logo_background_type'],
            ['setting_value' => 'white'] // white, gradient, transparent, custom
        );
        
        \App\Models\SiteSetting::updateOrCreate(
            ['setting_key' => 'logo_background_color'],
            ['setting_value' => '#FFFFFF']
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \App\Models\SiteSetting::whereIn('setting_key', [
            'logo_background_type',
            'logo_background_color'
        ])->delete();
    }
};
