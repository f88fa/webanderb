<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_requests', 'beneficiary_type')) {
                $table->string('beneficiary_type', 20)->default('entity')->after('amount')->comment('موظف / جهة');
            }
            if (!Schema::hasColumn('payment_requests', 'beneficiary_employee_id')) {
                $table->unsignedBigInteger('beneficiary_employee_id')->nullable()->after('beneficiary_type');
            }
        });
        if (Schema::hasTable('hr_employees')) {
            Schema::table('payment_requests', function (Blueprint $table) {
                $table->foreign('beneficiary_employee_id')->references('id')->on('hr_employees')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('payment_requests', function (Blueprint $table) {
            try {
                $table->dropForeign(['beneficiary_employee_id']);
            } catch (\Throwable $e) {
                // ignore if foreign doesn't exist
            }
            $table->dropColumn(['beneficiary_type', 'beneficiary_employee_id']);
        });
    }
};
