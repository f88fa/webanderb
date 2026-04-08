<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_request_approval_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('request_type', 50); // leave, permission, financial, general
            $table->unsignedTinyInteger('step'); // 1 = مدير مباشر، 2-6 = موافقون إضافيون
            $table->string('approver_type', 30); // direct_manager, role, employee
            $table->string('role_name', 100)->nullable();
            $table->foreignId('employee_id')->nullable()->constrained('hr_employees')->nullOnDelete();
            $table->timestamps();
            $table->unique(['request_type', 'step']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_request_approval_sequences');
    }
};
