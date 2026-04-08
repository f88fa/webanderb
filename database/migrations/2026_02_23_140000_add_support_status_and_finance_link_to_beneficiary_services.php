<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ben_service_types', function (Blueprint $table) {
            if (!Schema::hasColumn('ben_service_types', 'is_financial')) {
                $table->boolean('is_financial')->default(false)->after('is_active')->comment('دعم مالي يمر لطلب صرف');
            }
        });

        Schema::table('ben_beneficiary_services', function (Blueprint $table) {
            if (!Schema::hasColumn('ben_beneficiary_services', 'status')) {
                $table->string('status', 30)->default('executed')->after('notes')->comment('pending, approved, executed, rejected');
            }
            if (!Schema::hasColumn('ben_beneficiary_services', 'payment_request_id')) {
                $table->unsignedBigInteger('payment_request_id')->nullable()->after('request_id');
                $table->foreign('payment_request_id')->references('id')->on('payment_requests')->nullOnDelete();
            }
            if (!Schema::hasColumn('ben_beneficiary_services', 'program_id')) {
                $table->unsignedBigInteger('program_id')->nullable()->after('service_type_id');
                $table->foreign('program_id')->references('id')->on('ben_programs')->nullOnDelete();
            }
            if (!Schema::hasColumn('ben_beneficiary_services', 'executed_at')) {
                $table->timestamp('executed_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('ben_beneficiary_services', 'executed_by')) {
                $table->unsignedBigInteger('executed_by')->nullable()->after('executed_at');
                $table->foreign('executed_by')->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('ben_beneficiary_services', function (Blueprint $table) {
            if (Schema::hasColumn('ben_beneficiary_services', 'executed_by')) {
                $table->dropForeign(['executed_by']);
                $table->dropColumn('executed_by');
            }
            if (Schema::hasColumn('ben_beneficiary_services', 'executed_at')) {
                $table->dropColumn('executed_at');
            }
            if (Schema::hasColumn('ben_beneficiary_services', 'program_id')) {
                $table->dropForeign(['program_id']);
                $table->dropColumn('program_id');
            }
            if (Schema::hasColumn('ben_beneficiary_services', 'payment_request_id')) {
                $table->dropForeign(['payment_request_id']);
                $table->dropColumn('payment_request_id');
            }
            if (Schema::hasColumn('ben_beneficiary_services', 'status')) {
                $table->dropColumn('status');
            }
        });
        Schema::table('ben_service_types', function (Blueprint $table) {
            if (Schema::hasColumn('ben_service_types', 'is_financial')) {
                $table->dropColumn('is_financial');
            }
        });
    }
};
