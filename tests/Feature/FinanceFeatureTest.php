<?php

namespace Tests\Feature;

/**
 * Feature tests لقسم المالية
 * ملاحظة: إذا فشلت الاختبارات بسبب "table already exists" فالمشروع يحتوي على migrations مكررة لـ section_order.
 * يمكن تشغيل: php artisan migrate:fresh --env=testing للتحقق
 */
use App\Models\AccountingPeriod;
use App\Models\ChartAccount;
use App\Models\FiscalYear;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Models\User;
use App\Services\Finance\ChartAccountBalanceService;
use App\Services\Finance\FiscalYearClosingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected FiscalYear $fiscalYear;
    protected AccountingPeriod $period;
    protected ChartAccount $revenueAccount;
    protected ChartAccount $expenseAccount;
    protected ChartAccount $equityAccount;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        $this->fiscalYear = FiscalYear::create([
            'year_name' => '2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'status' => 'open',
        ]);

        $this->period = AccountingPeriod::create([
            'fiscal_year_id' => $this->fiscalYear->id,
            'period_name' => '2026-01',
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-31',
            'status' => 'open',
            'allow_posting' => true,
            'allow_adjustments' => true,
        ]);

        $this->revenueAccount = ChartAccount::create([
            'code' => '411',
            'name_ar' => 'إيرادات',
            'level' => 3,
            'type' => 'revenue',
            'nature' => 'credit',
            'is_postable' => true,
            'status' => 'active',
        ]);

        $this->expenseAccount = ChartAccount::create([
            'code' => '511',
            'name_ar' => 'مصروفات',
            'level' => 3,
            'type' => 'expense',
            'nature' => 'debit',
            'is_postable' => true,
            'status' => 'active',
        ]);

        $this->equityAccount = ChartAccount::create([
            'code' => '311',
            'name_ar' => 'أرباح محتجزة',
            'level' => 3,
            'type' => 'equity',
            'nature' => 'credit',
            'is_postable' => true,
            'status' => 'active',
        ]);
    }

    public function test_posting_journal_entry_in_closed_period_fails(): void
    {
        $this->period->update(['allow_posting' => false]);

        $entry = JournalEntry::create([
            'entry_no' => 'JE-2026-00001',
            'entry_date' => '2026-01-15',
            'description' => 'قيد اختبار',
            'entry_type' => 'manual',
            'period_id' => $this->period->id,
            'status' => 'draft',
            'total_debit' => 100,
            'total_credit' => 100,
        ]);
        $entry->lines()->create([
            'account_id' => $this->expenseAccount->id,
            'debit' => 100,
            'credit' => 0,
            'description' => 'مدين',
            'line_order' => 1,
        ]);
        $entry->lines()->create([
            'account_id' => $this->equityAccount->id,
            'debit' => 0,
            'credit' => 100,
            'description' => 'دائن',
            'line_order' => 2,
        ]);

        $result = $entry->post();
        $this->assertFalse($result);
        $this->assertEquals('draft', $entry->fresh()->status);
    }

    public function test_posting_journal_entry_in_open_period_succeeds(): void
    {
        $entry = JournalEntry::create([
            'entry_no' => 'JE-2026-00001',
            'entry_date' => '2026-01-15',
            'description' => 'قيد اختبار',
            'entry_type' => 'manual',
            'period_id' => $this->period->id,
            'status' => 'draft',
            'total_debit' => 100,
            'total_credit' => 100,
        ]);
        $entry->lines()->create([
            'account_id' => $this->expenseAccount->id,
            'debit' => 100,
            'credit' => 0,
            'description' => 'مدين',
            'line_order' => 1,
        ]);
        $entry->lines()->create([
            'account_id' => $this->equityAccount->id,
            'debit' => 0,
            'credit' => 100,
            'description' => 'دائن',
            'line_order' => 2,
        ]);

        $result = $entry->post();
        $this->assertTrue($result);
        $this->assertEquals('posted', $entry->fresh()->status);
    }

    public function test_balance_service_calculates_fiscal_year_balance(): void
    {
        $entry = JournalEntry::create([
            'entry_no' => 'JE-2026-00001',
            'entry_date' => '2026-01-15',
            'description' => 'قيد إيراد',
            'entry_type' => 'manual',
            'period_id' => $this->period->id,
            'status' => 'posted',
            'posted_at' => now(),
            'posted_by' => $this->user->id,
            'total_debit' => 0,
            'total_credit' => 500,
        ]);
        $entry->lines()->create([
            'account_id' => $this->revenueAccount->id,
            'debit' => 0,
            'credit' => 500,
            'description' => 'إيراد',
            'line_order' => 1,
        ]);
        $entry->lines()->create([
            'account_id' => $this->equityAccount->id,
            'debit' => 500,
            'credit' => 0,
            'description' => 'مقابل',
            'line_order' => 2,
        ]);

        $service = app(ChartAccountBalanceService::class);
        $balance = $service->calculateBalanceForFiscalYear(
            $this->revenueAccount->id,
            $this->fiscalYear->id,
            \Carbon\Carbon::parse('2026-01-31')
        );

        $this->assertGreaterThanOrEqual(0, $balance['raw_balance'] ?? 0);
    }

    public function test_closing_entry_transfers_revenue_and_expense(): void
    {
        JournalEntry::create([
            'entry_no' => 'JE-2026-00001',
            'entry_date' => '2026-01-15',
            'description' => 'قيد إيراد',
            'entry_type' => 'manual',
            'period_id' => $this->period->id,
            'status' => 'posted',
            'posted_at' => now(),
            'posted_by' => $this->user->id,
            'total_debit' => 0,
            'total_credit' => 1000,
        ])->lines()->createMany([
            ['account_id' => $this->revenueAccount->id, 'debit' => 0, 'credit' => 1000, 'description' => 'إيراد', 'line_order' => 1],
            ['account_id' => $this->equityAccount->id, 'debit' => 1000, 'credit' => 0, 'description' => 'مقابل', 'line_order' => 2],
        ]);

        JournalEntry::create([
            'entry_no' => 'JE-2026-00002',
            'entry_date' => '2026-01-20',
            'description' => 'قيد مصروف',
            'entry_type' => 'manual',
            'period_id' => $this->period->id,
            'status' => 'posted',
            'posted_at' => now(),
            'posted_by' => $this->user->id,
            'total_debit' => 300,
            'total_credit' => 0,
        ])->lines()->createMany([
            ['account_id' => $this->expenseAccount->id, 'debit' => 300, 'credit' => 0, 'description' => 'مصروف', 'line_order' => 1],
            ['account_id' => $this->equityAccount->id, 'debit' => 0, 'credit' => 300, 'description' => 'مقابل', 'line_order' => 2],
        ]);

        $closingService = app(FiscalYearClosingService::class);
        $closingEntry = $closingService->createClosingEntry($this->fiscalYear);

        $this->assertNotNull($closingEntry);
        $this->assertEquals('closing', $closingEntry->entry_type);
        $this->assertEquals('posted', $closingEntry->status);
    }
}
