<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ben_beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->string('beneficiary_no', 50)->unique();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('national_id', 50)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('gender', 10)->nullable(); // male, female
            $table->string('status', 20)->default('active'); // active, archived
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ben_beneficiaries');
    }
};
