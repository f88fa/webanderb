<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * الميزانيات التقديرية - للمقارنة مع الفعلي (القطاع غير الربحي)
     */
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiscal_year_id')->constrained('fiscal_years')->onDelete('cascade');
            $table->foreignId('chart_account_id')->constrained('chart_accounts')->onDelete('cascade');
            $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers')->onDelete('cascade');
            $table->foreignId('period_id')->nullable()->constrained('accounting_periods')->onDelete('cascade');
            $table->enum('budget_type', ['revenue', 'expense']); // إيراد أو مصروف
            $table->decimal('amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['fiscal_year_id', 'chart_account_id', 'cost_center_id', 'period_id', 'budget_type'], 'budgets_unique');
            $table->index(['fiscal_year_id', 'period_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
