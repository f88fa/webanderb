<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_no', 50)->unique()->comment('رقم الطلب');
            $table->date('request_date');
            $table->decimal('amount', 15, 2);
            $table->string('beneficiary')->comment('المستفيد');
            $table->text('description')->nullable()->comment('الغرض / الوصف');
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid'])->default('pending');
            $table->unsignedBigInteger('period_id')->nullable();
            $table->unsignedBigInteger('journal_entry_id')->nullable()->comment('سند الصرف عند التنفيذ');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->text('rejection_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('period_id')->references('id')->on('accounting_periods')->nullOnDelete();
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_requests');
    }
};
