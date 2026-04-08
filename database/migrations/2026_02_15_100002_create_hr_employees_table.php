<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->nullable()->constrained('hr_departments')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('employee_no', 50)->unique();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('national_id', 50)->nullable();
            $table->date('hire_date')->nullable();
            $table->string('job_title')->nullable();
            $table->decimal('base_salary', 12, 2)->nullable();
            $table->string('status', 20)->default('active'); // active, left, suspended
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_employees');
    }
};
