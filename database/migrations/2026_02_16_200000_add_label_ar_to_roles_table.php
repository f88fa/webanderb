<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('roles') && !Schema::hasColumn('roles', 'label_ar')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->string('label_ar', 100)->nullable()->after('name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('roles') && Schema::hasColumn('roles', 'label_ar')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropColumn('label_ar');
            });
        }
    }
};
