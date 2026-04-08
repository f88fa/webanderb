<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * أعمدة مشاريع الموقع الأمامي (قسم المشاريع).
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'name')) {
                $table->string('name')->nullable()->after('id');
            }
            if (!Schema::hasColumn('projects', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
            if (!Schema::hasColumn('projects', 'image')) {
                $table->string('image')->nullable()->after('description');
            }
            if (!Schema::hasColumn('projects', 'donate_link')) {
                $table->string('donate_link')->nullable()->after('image');
            }
            if (!Schema::hasColumn('projects', 'donate_button_text')) {
                $table->string('donate_button_text')->nullable()->after('donate_link');
            }
            if (!Schema::hasColumn('projects', 'order')) {
                $table->unsignedSmallInteger('order')->default(0)->after('donate_button_text');
            }
            if (!Schema::hasColumn('projects', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('order');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $columns = ['name', 'description', 'image', 'donate_link', 'donate_button_text', 'order', 'is_active'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('projects', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
