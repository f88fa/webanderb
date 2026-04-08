<?php

namespace App\Services\Finance;

use App\Models\ChartAccount;
use App\Models\FiscalYear;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Models\AccountingPeriod;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FiscalYearClosingService
{
    public function __construct(
        protected ChartAccountBalanceService $balanceService
    ) {}

    /**
     * إنشاء قيد الإقفال السنوي: إغلاق حسابات الإيرادات والمصروفات ونقل صافي الربح/الخسارة إلى حقوق الملكية
     */
    public function createClosingEntry(FiscalYear $fiscalYear): ?JournalEntry
    {
        $lastPeriod = AccountingPeriod::where('fiscal_year_id', $fiscalYear->id)
            ->orderBy('end_date', 'desc')
            ->first();

        if (!$lastPeriod) {
            throw new \RuntimeException('لا توجد فترات محاسبية في هذه السنة المالية');
        }

        $asOf = Carbon::parse($fiscalYear->end_date)->endOfDay();
        $resultAccount = $this->getResultAccount();

        $revenueAccounts = ChartAccount::active()
            ->postable()
            ->where('type', 'revenue')
            ->orderBy('code')
            ->get();

        $expenseAccounts = ChartAccount::active()
            ->postable()
            ->where('type', 'expense')
            ->orderBy('code')
            ->get();

        $lines = [];
        $totalDebit = 0;
        $totalCredit = 0;

        // إغلاق الإيرادات (رصيدها دائن) → مدين الحساب، دائن حساب النتيجة
        // استخدام رصيد السنة المالية فقط (ليس تراكمي)
        foreach ($revenueAccounts as $account) {
            $balanceData = $this->balanceService->calculateBalanceForFiscalYear(
                $account->id,
                $fiscalYear->id,
                $asOf
            );
            $raw = $balanceData['raw_balance'] ?? 0;
            if (abs($raw) < 0.01) {
                continue;
            }
            // طبيعة الإيرادات دائنة؛ الرصيد الموجب = دائن
            $amount = abs($raw);
            $lines[] = [
                'account_id' => $account->id,
                'debit' => $amount,
                'credit' => 0,
                'description' => "إقفال إيرادات - {$account->name_ar}",
            ];
            $lines[] = [
                'account_id' => $resultAccount->id,
                'debit' => 0,
                'credit' => $amount,
                'description' => "صافي الربح - من {$account->name_ar}",
            ];
            $totalDebit += $amount;
            $totalCredit += $amount;
        }

        // إغلاق المصروفات (رصيدها مدين) → دائن الحساب، مدين حساب النتيجة
        foreach ($expenseAccounts as $account) {
            $balanceData = $this->balanceService->calculateBalanceForFiscalYear(
                $account->id,
                $fiscalYear->id,
                $asOf
            );
            $raw = $balanceData['raw_balance'] ?? 0;
            if (abs($raw) < 0.01) {
                continue;
            }
            $amount = abs($raw);
            $lines[] = [
                'account_id' => $resultAccount->id,
                'debit' => $amount,
                'credit' => 0,
                'description' => "صافي الربح - إقفال {$account->name_ar}",
            ];
            $lines[] = [
                'account_id' => $account->id,
                'debit' => 0,
                'credit' => $amount,
                'description' => "إقفال مصروفات - {$account->name_ar}",
            ];
            $totalDebit += $amount;
            $totalCredit += $amount;
        }

        if (empty($lines)) {
            return null;
        }

        $entry = DB::transaction(function () use ($fiscalYear, $lastPeriod, $lines, $totalDebit, $totalCredit) {
            $entry = JournalEntry::create([
                'entry_no' => JournalEntry::generateEntryNo('CL'),
                'entry_date' => $fiscalYear->end_date,
                'description' => 'قيد إقفال السنة المالية - ' . $fiscalYear->year_name,
                'entry_type' => 'closing',
                'period_id' => $lastPeriod->id,
                'status' => 'posted',
                'posted_at' => now(),
                'posted_by' => auth()->id(),
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'notes' => 'قيد إقفال تلقائي',
            ]);

            foreach ($lines as $index => $line) {
                $entry->lines()->create([
                    'account_id' => $line['account_id'],
                    'cost_center_id' => null,
                    'debit' => $line['debit'],
                    'credit' => $line['credit'],
                    'description' => $line['description'] ?? null,
                    'reference' => null,
                    'line_order' => $index + 1,
                ]);
            }

            $accountIds = array_unique(array_column($lines, 'account_id'));
            foreach ($accountIds as $accountId) {
                $this->balanceService->clearBalanceCache($accountId, $lastPeriod->id);
            }

            return $entry;
        });

        return $entry;
    }

    /**
     * حساب صافي الربح/الخسارة قبل إنشاء قيد الإقفال (للتحقق أو العرض)
     */
    public function getNetResultForYear(FiscalYear $fiscalYear): array
    {
        $asOf = Carbon::parse($fiscalYear->end_date)->endOfDay();
        $totalRevenue = 0;
        $totalExpense = 0;

        foreach (ChartAccount::active()->postable()->where('type', 'revenue')->get() as $account) {
            $b = $this->balanceService->calculateBalanceForFiscalYear($account->id, $fiscalYear->id, $asOf);
            $totalRevenue += $b['raw_balance'] ?? 0;
        }

        foreach (ChartAccount::active()->postable()->where('type', 'expense')->get() as $account) {
            $b = $this->balanceService->calculateBalanceForFiscalYear($account->id, $fiscalYear->id, $asOf);
            $totalExpense += abs($b['raw_balance'] ?? 0);
        }

        $netProfit = $totalRevenue - $totalExpense;

        return [
            'total_revenue' => $totalRevenue,
            'total_expense' => $totalExpense,
            'net_profit' => $netProfit,
            'has_result' => abs($netProfit) >= 0.01 || $totalRevenue >= 0.01 || $totalExpense >= 0.01,
        ];
    }

    /**
     * الحصول على حساب النتيجة (أرباح محتجزة / صافي الربح)
     */
    protected function getResultAccount(): ChartAccount
    {
        $account = ChartAccount::active()
            ->postable()
            ->where('type', 'equity')
            ->orderBy('code')
            ->first();

        if ($account) {
            return $account;
        }

        $account = ChartAccount::active()
            ->postable()
            ->where(function ($q) {
                $q->where('name_ar', 'like', '%أرباح%')
                    ->orWhere('name_ar', 'like', '%نتائج%')
                    ->orWhere('name_ar', 'like', '%صافي%');
            })
            ->orderBy('code')
            ->first();

        if ($account) {
            return $account;
        }

        throw new \RuntimeException(
            'لم يتم العثور على حساب حقوق ملكية (أرباح محتجزة / صافي الربح). يرجى إضافة حساب من نوع حقوق ملكية في دليل الحسابات.'
        );
    }
}
