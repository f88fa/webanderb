<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_requests', 'beneficiary_id')) {
                $table->unsignedBigInteger('beneficiary_id')->nullable()->after('journal_entry_id')->comment('مستفيد من ben_beneficiaries عند الصرف لصالح مستفيد');
                $table->foreign('beneficiary_id')->references('id')->on('ben_beneficiaries')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('payment_requests', function (Blueprint $table) {
            if (Schema::hasColumn('payment_requests', 'beneficiary_id')) {
                $table->dropForeign(['beneficiary_id']);
                $table->dropColumn('beneficiary_id');
            }
        });
    }
};
