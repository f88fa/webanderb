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
        // لا حاجة لإضافة أعمدة جديدة، سنستخدم site_settings table
        // فقط نضيف الإعدادات الافتراضية إذا لم تكن موجودة
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // حذف الإعدادات عند التراجع
        DB::table('site_settings')->whereIn('setting_key', [
            'hero_background_image',
            'hero_background_opacity'
        ])->delete();
    }
};
