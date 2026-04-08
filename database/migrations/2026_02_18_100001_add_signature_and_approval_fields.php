<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('hr_employees', 'signature_path')) {
            Schema::table('hr_employees', function (Blueprint $table) {
                $table->string('signature_path', 500)->nullable()->after('status')->comment('التوقيع الإلكتروني');
            });
        }

        if (!Schema::hasColumn('payment_requests', 'approved_by')) {
            Schema::table('payment_requests', function (Blueprint $table) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('rejection_notes');
                $table->timestamp('approved_at')->nullable()->after('approved_by');
                $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('payment_requests', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
        });
        Schema::table('payment_requests', function (Blueprint $table) {
            $table->dropColumn(['approved_by', 'approved_at']);
        });
        Schema::table('hr_employees', function (Blueprint $table) {
            if (Schema::hasColumn('hr_employees', 'signature_path')) {
                $table->dropColumn('signature_path');
            }
        });
    }
};
