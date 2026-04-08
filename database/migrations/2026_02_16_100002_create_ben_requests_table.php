<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ben_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->constrained('ben_beneficiaries')->cascadeOnDelete();
            $table->string('request_type', 50)->nullable(); // مساعدة مالية، عينية، طبية، إلخ
            $table->text('description')->nullable();
            $table->string('status', 20)->default('new'); // new, under_study, approved, rejected
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('studied_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ben_requests');
    }
};
