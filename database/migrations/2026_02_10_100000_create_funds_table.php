<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * محاسبة الأموال - القطاع غير الربحي (أموال مقيدة / غير مقيدة / أوقاف)
     */
    public function up(): void
    {
        Schema::create('funds', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->enum('restriction_type', ['unrestricted', 'restricted', 'endowment'])->default('unrestricted');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index('restriction_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funds');
    }
};
