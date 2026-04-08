<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_requests', function (Blueprint $table) {
            $table->string('approval_type', 50)->default('financial')->after('status')
                ->comment('financial=طلب مالي عام، beneficiary_support=طلب دعم المستفيدين من الخدمات والمساندة');
        });
    }

    public function down(): void
    {
        Schema::table('payment_requests', function (Blueprint $table) {
            $table->dropColumn('approval_type');
        });
    }
};
