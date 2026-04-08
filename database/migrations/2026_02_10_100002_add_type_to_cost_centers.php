<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * تصنيف مراكز التكلفة حسب الوظيفة (برنامج، إداري، جمع تبرعات) - معيار القطاع غير الربحي
     */
    public function up(): void
    {
        Schema::table('cost_centers', function (Blueprint $table) {
            $table->string('center_type', 20)->default('program')->after('description'); // program | administrative | fundraising
        });
    }

    public function down(): void
    {
        Schema::table('cost_centers', function (Blueprint $table) {
            $table->dropColumn('center_type');
        });
    }
};
