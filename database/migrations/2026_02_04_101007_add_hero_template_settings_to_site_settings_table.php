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
        // إضافة إعدادات نوع قالب الهيرو
        // القيم: 'default', 'video', 'slider'
        \App\Models\SiteSetting::updateOrCreate(
            ['setting_key' => 'hero_template_type'],
            ['setting_value' => 'default']
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \App\Models\SiteSetting::where('setting_key', 'hero_template_type')->delete();
    }
};
