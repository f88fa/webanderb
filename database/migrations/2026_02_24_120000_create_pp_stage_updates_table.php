<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pp_stage_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stage_id')->constrained('pp_stages')->cascadeOnDelete();
            $table->date('update_date');
            $table->string('title')->nullable()->comment('عنوان التحديث');
            $table->text('description')->nullable()->comment('تفاصيل التحديث');
            $table->unsignedTinyInteger('progress_percentage')->nullable()->comment('نسبة الإنجاز 0-100');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pp_stage_updates');
    }
};
