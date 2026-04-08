<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_allowance_deduction_types', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('type', 20); // allowance, deduction
            $table->boolean('is_fixed')->default(true);
            $table->decimal('default_amount', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('hr_payroll_runs', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->string('status', 20)->default('draft'); // draft, approved, paid
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->unique(['month', 'year']);
        });

        Schema::create('hr_payroll_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_run_id')->constrained('hr_payroll_runs')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->decimal('base_salary', 12, 2)->default(0);
            $table->decimal('allowances', 12, 2)->default(0);
            $table->decimal('deductions', 12, 2)->default(0);
            $table->decimal('advance_deduction', 12, 2)->default(0);
            $table->decimal('net_salary', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['payroll_run_id', 'employee_id']);
        });

        Schema::create('hr_advances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->date('request_date');
            $table->string('status', 20)->default('pending'); // pending, approved, paid, deducted
            $table->unsignedTinyInteger('deduct_months')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_advances');
        Schema::dropIfExists('hr_payroll_lines');
        Schema::dropIfExists('hr_payroll_runs');
        Schema::dropIfExists('hr_allowance_deduction_types');
    }
};
