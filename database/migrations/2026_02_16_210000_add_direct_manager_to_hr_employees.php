<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('hr_employees', 'direct_manager_id')) {
            Schema::table('hr_employees', function (Blueprint $table) {
                $table->foreignId('direct_manager_id')->nullable()->after('department_id')
                    ->constrained('hr_employees')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('hr_employees', 'direct_manager_id')) {
            Schema::table('hr_employees', function (Blueprint $table) {
                $table->dropForeign(['direct_manager_id']);
            });
        }
    }
};
