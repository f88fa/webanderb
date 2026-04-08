<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ben_service_types', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('ben_beneficiary_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->constrained('ben_beneficiaries')->cascadeOnDelete();
            $table->foreignId('service_type_id')->constrained('ben_service_types')->cascadeOnDelete();
            $table->foreignId('request_id')->nullable()->constrained('ben_requests')->nullOnDelete();
            $table->decimal('amount', 12, 2)->nullable();
            $table->date('service_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('ben_medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->constrained('ben_beneficiaries')->cascadeOnDelete();
            $table->date('record_date');
            $table->string('diagnosis')->nullable();
            $table->text('treatment')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('ben_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->constrained('ben_beneficiaries')->cascadeOnDelete();
            $table->date('assessment_date');
            $table->decimal('eligibility_score', 4, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('ben_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->constrained('ben_beneficiaries')->cascadeOnDelete();
            $table->string('document_type', 50)->nullable();
            $table->string('file_path')->nullable();
            $table->date('document_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('ben_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ben_beneficiary_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->constrained('ben_beneficiaries')->cascadeOnDelete();
            $table->foreignId('program_id')->constrained('ben_programs')->cascadeOnDelete();
            $table->date('joined_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ben_beneficiary_programs');
        Schema::dropIfExists('ben_programs');
        Schema::dropIfExists('ben_documents');
        Schema::dropIfExists('ben_assessments');
        Schema::dropIfExists('ben_medical_records');
        Schema::dropIfExists('ben_beneficiary_services');
        Schema::dropIfExists('ben_service_types');
    }
};
