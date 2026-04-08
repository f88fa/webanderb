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
        if (Schema::hasTable('journal_lines')) {
            $conn = Schema::getConnection();
            $fkExists = $conn->selectOne(
                "SELECT 1 FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = ? AND TABLE_NAME = 'journal_lines' AND CONSTRAINT_NAME = 'journal_lines_cost_center_id_foreign'",
                [$conn->getDatabaseName()]
            ) !== null;
            if (!$fkExists && Schema::hasTable('cost_centers')) {
                Schema::table('journal_lines', function (Blueprint $table) {
                    $table->foreign('cost_center_id')->references('id')->on('cost_centers')->onDelete('set null');
                });
            }
            return;
        }
        Schema::create('journal_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained('journal_entries')->onDelete('cascade');
            $table->foreignId('account_id')->constrained('chart_accounts')->onDelete('restrict');
            $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers')->onDelete('set null');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->string('reference')->nullable(); // رقم مرجع خارجي
            $table->integer('line_order')->default(0);
            $table->timestamps();

            $table->index(['journal_entry_id', 'line_order']);
            $table->index('account_id');
            $table->index('cost_center_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_lines');
    }
};
