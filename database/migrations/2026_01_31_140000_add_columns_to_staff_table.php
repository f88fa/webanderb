<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
            $table->string('position')->nullable()->after('name');
            $table->string('image')->nullable()->after('position');
            $table->integer('order')->default(0)->after('image');
            $table->boolean('is_active')->default(true)->after('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn([
                'name',
                'position',
                'image',
                'order',
                'is_active',
            ]);
        });
    }
};

