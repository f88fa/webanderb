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
        // إضافة إعدادات قالب الفيديو
        \App\Models\SiteSetting::updateOrCreate(
            ['setting_key' => 'hero_video_title_background'],
            ['setting_value' => '1'] // 1 = مع خلفية، 0 = بدون خلفية
        );
        
        \App\Models\SiteSetting::updateOrCreate(
            ['setting_key' => 'hero_video_show_contact_button'],
            ['setting_value' => '0'] // 1 = إظهار زر التواصل، 0 = إخفاء
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \App\Models\SiteSetting::whereIn('setting_key', [
            'hero_video_title_background',
            'hero_video_show_contact_button'
        ])->delete();
    }
};
