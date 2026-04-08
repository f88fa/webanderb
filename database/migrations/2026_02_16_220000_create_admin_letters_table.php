<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * الخطابات الإدارية - الصادر والوارد
     */
    public function up(): void
    {
        Schema::create('admin_letters', function (Blueprint $table) {
            $table->id();
            $table->enum('direction', ['outgoing', 'incoming'])->comment('صادر / وارد');
            $table->string('letter_no', 100)->nullable()->comment('رقم الخطاب');
            $table->string('subject')->comment('الموضوع');
            $table->date('letter_date')->nullable()->comment('تاريخ الخطاب');
            $table->string('from_party')->nullable()->comment('من (جهة/شخص)');
            $table->string('to_party')->nullable()->comment('إلى (جهة/شخص)');
            $table->text('body')->nullable()->comment('محتوى الخطاب');
            $table->string('reference_no', 100)->nullable()->comment('الرقم المرجعي');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['direction', 'letter_date']);
            $table->index('letter_no');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_letters');
    }
};
