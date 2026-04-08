<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إضافة حساب الصندوق/البنك المختار (لإخفائه من طباعة السند).
     */
    public function up(): void
    {
        if (!Schema::hasColumn('journal_entries', 'cash_account_id')) {
            Schema::table('journal_entries', function (Blueprint $table) {
                $table->foreignId('cash_account_id')->nullable()->after('notes')->constrained('chart_accounts')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('journal_entries', 'cash_account_id')) {
            Schema::table('journal_entries', function (Blueprint $table) {
                $table->dropForeign(['cash_account_id']);
            });
        }
    }
};
