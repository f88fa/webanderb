<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // أنواع الاجتماعات
        Schema::create('mt_meeting_types', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // أعضاء المجلس (قسم الاجتماعات)
        Schema::create('mt_board_members', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('position_ar')->nullable();
            $table->string('position_en')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // اجتماعات المجلس
        Schema::create('mt_board_meetings', function (Blueprint $table) {
            $table->id();
            $table->string('meeting_no', 50)->unique();
            $table->string('title');
            $table->date('meeting_date');
            $table->string('location')->nullable();
            $table->text('agenda')->nullable();
            $table->text('minutes')->nullable();
            $table->string('status', 20)->default('scheduled'); // scheduled, held, postponed, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // اجتماعات الموظفين
        Schema::create('mt_staff_meetings', function (Blueprint $table) {
            $table->id();
            $table->string('meeting_no', 50)->unique();
            $table->string('title');
            $table->date('meeting_date');
            $table->string('location')->nullable();
            $table->foreignId('meeting_type_id')->nullable()->constrained('mt_meeting_types')->nullOnDelete();
            $table->text('agenda')->nullable();
            $table->text('minutes')->nullable();
            $table->string('status', 20)->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // قرارات المجلس
        Schema::create('mt_board_decisions', function (Blueprint $table) {
            $table->id();
            $table->string('decision_no', 50)->nullable();
            $table->string('title');
            $table->date('decision_date');
            $table->foreignId('board_meeting_id')->nullable()->constrained('mt_board_meetings')->nullOnDelete();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // حضور اجتماعات المجلس
        Schema::create('mt_board_meeting_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_meeting_id')->constrained('mt_board_meetings')->cascadeOnDelete();
            $table->foreignId('board_member_id')->constrained('mt_board_members')->cascadeOnDelete();
            $table->boolean('attended')->default(false);
            $table->timestamps();
            $table->unique(['board_meeting_id', 'board_member_id'], 'mt_bm_attendees_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mt_board_meeting_attendees');
        Schema::dropIfExists('mt_board_decisions');
        Schema::dropIfExists('mt_staff_meetings');
        Schema::dropIfExists('mt_board_meetings');
        Schema::dropIfExists('mt_board_members');
        Schema::dropIfExists('mt_meeting_types');
    }
};
