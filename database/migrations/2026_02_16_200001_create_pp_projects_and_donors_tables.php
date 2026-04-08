<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pp_donors', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('pp_projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_no', 50)->unique();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('donor_id')->nullable()->constrained('pp_donors')->nullOnDelete();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('budget_amount', 15, 2)->nullable();
            $table->decimal('spent_amount', 15, 2)->default(0);
            $table->string('status', 20)->default('active'); // active, completed, archived
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('pp_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('pp_projects')->cascadeOnDelete();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->unsignedSmallInteger('order')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status', 20)->default('pending'); // pending, in_progress, completed
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('pp_project_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('pp_projects')->cascadeOnDelete();
            $table->foreignId('stage_id')->nullable()->constrained('pp_stages')->nullOnDelete();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('due_date')->nullable();
            $table->string('status', 20)->default('todo'); // todo, in_progress, done
            $table->string('priority', 20)->default('medium'); // low, medium, high
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('pp_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained('pp_donors')->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('pp_projects')->nullOnDelete();
            $table->string('agreement_no', 50)->nullable();
            $table->string('title');
            $table->decimal('amount', 15, 2)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('pp_grants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained('pp_donors')->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('pp_projects')->nullOnDelete();
            $table->foreignId('agreement_id')->nullable()->constrained('pp_agreements')->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('grant_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('pp_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('pp_projects')->cascadeOnDelete();
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->date('expense_date');
            $table->string('category', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('pp_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('pp_projects')->cascadeOnDelete();
            $table->string('title');
            $table->string('file_path')->nullable();
            $table->string('document_type', 50)->nullable();
            $table->date('document_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pp_documents');
        Schema::dropIfExists('pp_expenses');
        Schema::dropIfExists('pp_grants');
        Schema::dropIfExists('pp_agreements');
        Schema::dropIfExists('pp_project_tasks');
        Schema::dropIfExists('pp_stages');
        Schema::dropIfExists('pp_projects');
        Schema::dropIfExists('pp_donors');
    }
};
