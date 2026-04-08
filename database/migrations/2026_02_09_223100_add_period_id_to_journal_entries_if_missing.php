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
        if (!Schema::hasColumn('journal_entries', 'period_id')) {
            Schema::table('journal_entries', function (Blueprint $table) {
                $table->unsignedBigInteger('period_id')->nullable()->after('entry_type');
                $table->foreign('period_id')->references('id')->on('accounting_periods')->onDelete('restrict');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('journal_entries', 'period_id')) {
            Schema::table('journal_entries', function (Blueprint $table) {
                $table->dropForeign(['period_id']);
                $table->dropColumn('period_id');
            });
        }
    }
};
