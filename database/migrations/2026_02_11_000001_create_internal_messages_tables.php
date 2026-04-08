<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internal_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('subject');
            $table->longText('body');
            $table->timestamps();
        });

        Schema::create('internal_message_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internal_message_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['to', 'cc'])->default('to');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->unique(['internal_message_id', 'user_id', 'type'], 'im_recip_unique');
        });

        Schema::create('internal_message_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internal_message_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('original_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internal_message_attachments');
        Schema::dropIfExists('internal_message_recipients');
        Schema::dropIfExists('internal_messages');
    }
};
