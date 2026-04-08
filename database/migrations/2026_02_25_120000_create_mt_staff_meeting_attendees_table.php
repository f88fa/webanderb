<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mt_staff_meeting_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_meeting_id')->constrained('mt_staff_meetings')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->boolean('attended')->default(false);
            $table->timestamps();
            $table->unique(['staff_meeting_id', 'employee_id'], 'mt_sm_attendees_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mt_staff_meeting_attendees');
    }
};
