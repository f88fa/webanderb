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
        if (!Schema::hasColumn('journal_entries', 'entry_type')) {
            Schema::table('journal_entries', function (Blueprint $table) {
                $table->enum('entry_type', ['manual', 'receipt', 'payment', 'donation', 'grant', 'adjusting', 'opening', 'closing'])
                    ->default('manual')
                    ->after('description');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('journal_entries', 'entry_type')) {
            Schema::table('journal_entries', function (Blueprint $table) {
                $table->dropColumn('entry_type');
            });
        }
    }
};
