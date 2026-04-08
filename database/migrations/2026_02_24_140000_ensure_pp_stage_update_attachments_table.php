<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pp_stage_update_attachments')) {
            return;
        }
        Schema::create('pp_stage_update_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stage_update_id')->constrained('pp_stage_updates')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pp_stage_update_attachments');
    }
};
