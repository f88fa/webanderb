<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_request_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_request_id')->constrained('payment_requests')->cascadeOnDelete();
            $table->unsignedTinyInteger('step')->comment('رقم خطوة الموافقة في التسلسل');
            $table->foreignId('approved_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('approved_at');
            $table->timestamps();
            $table->unique(['payment_request_id', 'step']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_request_approvals');
    }
};
