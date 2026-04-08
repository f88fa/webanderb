<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * أعمدة آراء العملاء (الموقع الأمامي).
     */
    public function up(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            if (!Schema::hasColumn('testimonials', 'name')) {
                $table->string('name')->nullable()->after('id');
            }
            if (!Schema::hasColumn('testimonials', 'text')) {
                $table->text('text')->nullable()->after('name');
            }
            if (!Schema::hasColumn('testimonials', 'image')) {
                $table->string('image')->nullable()->after('text');
            }
            if (!Schema::hasColumn('testimonials', 'order')) {
                $table->unsignedSmallInteger('order')->default(0)->after('image');
            }
            if (!Schema::hasColumn('testimonials', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('order');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $columns = ['name', 'text', 'image', 'order', 'is_active'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('testimonials', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
