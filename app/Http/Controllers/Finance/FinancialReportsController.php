<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\AccountingPeriod;
use App\Models\ChartAccount;
use App\Models\CostCenter;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Models\SiteSetting;
use App\Services\Finance\ChartAccountBalanceService;
use App\Services\Finance\ExcelExportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialReportsController extends Controller
{
    public function __construct(
        protected ChartAccountBalanceService $balanceService,
        protected ExcelExportService $excelExport
    ) {}

    /**
     * صفحة قائمة التقارير المالية
     */
    public function index()
    {
        $periods = AccountingPeriod::with('fiscalYear')->orderBy('start_date', 'desc')->get();
        $currentPeriod = AccountingPeriod::getCurrent();

        return view('wesal.index', [
            'page' => 'finance',
            'settings' => SiteSetting::getAllAsArray(),
            'periods' => $periods,
            'currentPeriod' => $currentPeriod,
            'formType' => 'finance-reports-index',
        ]);
    }

    /**
     * قائمة الدخل (الإيرادات والمصروفات وصافي الربح)
     */
    public function incomeStatement(Request $request)
    {
        $periodId = $request->get('period_id');
        $asOf = $request->get('as_of') ? Carbon::parse($request->get('as_of')) : null;
        $periods = AccountingPeriod::with('fiscalYear')->orderBy('start_date', 'desc')->get();
        $currentPeriod = AccountingPeriod::getCurrent();
        $selectedPeriod = $periodId ? AccountingPeriod::find($periodId) : $currentPeriod;
        if ($selectedPeriod && !$periodId) {
            $periodId = $selectedPeriod->id;
        }
        $asOfDate = $asOf ?? ($selectedPeriod ? $selectedPeriod->end_date : now());

        $revenueAccounts = ChartAccount::active()->postable()->where('type', 'revenue')->orderBy('code')->get();
        $expenseAccounts = ChartAccount::active()->postable()->where('type', 'expense')->orderBy('code')->get();

        $revenueLines = [];
        $totalRevenue = 0;
        foreach ($revenueAccounts as $account) {
            $b = $this->balanceService->calculateBalance($account->id, $periodId, $asOf);
            $raw = $b['raw_balance'] ?? 0;
            if (abs($raw) < 0.01) continue;
            $amount = abs($raw);
            $revenueLines[] = ['account' => $account, 'amount' => $amount];
            $totalRevenue += $amount;
        }

        $expenseLines = [];
        $totalExpense = 0;
        foreach ($expenseAccounts as $account) {
            $b = $this->balanceService->calculateBalance($account->id, $periodId, $asOf);
            $raw = $b['raw_balance'] ?? 0;
            if (abs($raw) < 0.01) continue;
            $amount = abs($raw);
            $expenseLines[] = ['account' => $account, 'amount' => $amount];
            $totalExpense += $amount;
        }

        $netProfit = $totalRevenue - $totalExpense;

        return view('wesal.index', [
            'page' => 'finance',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'finance-report-income-statement',
            'revenueLines' => $revenueLines,
            'expenseLines' => $expenseLines,
            'totalRevenue' => $totalRevenue,
            'totalExpense' => $totalExpense,
            'netProfit' => $netProfit,
            'periods' => $periods,
            'selectedPeriod' => $selectedPeriod,
            'asOf' => $asOf,
        ]);
    }

    /**
     * تصدير قائمة الدخل إلى Excel (عربي 100%)
     */
    public function incomeStatementExport(Request $request)
    {
        $periodId = $request->get('period_id');
        $asOf = $request->get('as_of') ? Carbon::parse($request->get('as_of')) : null;
        $periods = AccountingPeriod::with('fiscalYear')->orderBy('start_date', 'desc')->get();
        $currentPeriod = AccountingPeriod::getCurrent();
        $selectedPeriod = $periodId ? AccountingPeriod::find($periodId) : $currentPeriod;
        if ($selectedPeriod && !$periodId) {
            $periodId = $selectedPeriod->id;
        }

        $revenueAccounts = ChartAccount::active()->postable()->where('type', 'revenue')->orderBy('code')->get();
        $expenseAccounts = ChartAccount::active()->postable()->where('type', 'expense')->orderBy('code')->get();
        $revenueLines = [];
        $totalRevenue = 0;
        foreach ($revenueAccounts as $account) {
            $b = $this->balanceService->calculateBalance($account->id, $periodId, $asOf);
            $raw = $b['raw_balance'] ?? 0;
            if (abs($raw) < 0.01) continue;
            $amount = abs($raw);
            $revenueLines[] = ['account' => $account, 'amount' => $amount];
            $totalRevenue += $amount;
        }
        $expenseLines = [];
        $totalExpense = 0;
        foreach ($expenseAccounts as $account) {
            $b = $this->balanceService->calculateBalance($account->id, $periodId, $asOf);
            $raw = $b['raw_balance'] ?? 0;
            if (abs($raw) < 0.01) continue;
            $amount = abs($raw);
            $expenseLines[] = ['account' => $account, 'amount' => $amount];
            $totalExpense += $amount;
        }
        $netProfit = $totalRevenue - $totalExpense;

        $spreadsheet = $this->excelExport->newSpreadsheet();
        $sheet = $this->excelExport->setupArabicSheet($spreadsheet, 'قائمة الدخل');
        $row = 1;
        $this->excelExport->setCellValueByColumnAndRow($sheet,1, $row, $this->excelExport->ensureUtf8('البيان'));
        $this->excelExport->setCellValueByColumnAndRow($sheet,2, $row, $this->excelExport->ensureUtf8('المبلغ'));
        $this->excelExport->styleHeaderRow($sheet, $row, 2);
        $row++;
        $this->excelExport->setCellValueByColumnAndRow($sheet,1, $row, $this->excelExport->ensureUtf8('الإيرادات'));
        $this->excelExport->setCellValueByColumnAndRow($sheet,2, $row, $totalRevenue);
        $row++;
        foreach ($revenueLines as $r) {
            $this->excelExport->setCellValueByColumnAndRow($sheet,1, $row, $this->excelExport->ensureUtf8($r['account']->code . ' - ' . $r['account']->name_ar));
            $this->excelExport->setCellValueByColumnAndRow($sheet,2, $row, $r['amount']);
            $row++;
        }
        $this->excelExport->setCellValueByColumnAndRow($sheet,1, $row, $this->excelExport->ensureUtf8('المصروفات'));
        $this->excelExport->setCellValueByColumnAndRow($sheet,2, $row, $totalExpense);
        $row++;
        foreach ($expenseLines as $r) {
            $this->excelExport->setCellValueByColumnAndRow($sheet,1, $row, $this->excelExport->ensureUtf8($r['account']->code . ' - ' . $r['account']->name_ar));
            $this->excelExport->setCellValueByColumnAndRow($sheet,2, $row, $r['amount']);
            $row++;
        }
        $this->excelExport->setCellValueByColumnAndRow($sheet,1, $row, $this->excelExport->ensureUtf8('صافي الربح / (الخسارة)'));
        $this->excelExport->setCellValueByColumnAndRow($sheet,2, $row, $netProfit);
        $sheet->getStyle("A{$row}:B{$row}")->getFont()->setBold(true)->setName('Tahoma');
        $row++;

        $filename = 'قائمة-الدخل-' . ($selectedPeriod ? $selectedPeriod->period_name : date('Y-m-d'));
        return $this->excelExport->download($spreadsheet, $filename);
    }

    /**
     * الميزانية العمومية (أصول، التزامات، حقوق ملكية)
     */
    public function balanceSheet(Request $request)
    {
        $periodId = $request->get('period_id');
        $asOf = $request->get('as_of') ? Carbon::parse($request->get('as_of')) : null;
        $periods = AccountingPeriod::with('fiscalYear')->orderBy('start_date', 'desc')->get();
        $currentPeriod = AccountingPeriod::getCurrent();
        $selectedPeriod = $periodId ? AccountingPeriod::find($periodId) : $currentPeriod;
        if ($selectedPeriod && !$periodId) {
            $periodId = $selectedPeriod->id;
        }

        $assetAccounts = ChartAccount::active()->postable()->where('type', 'asset')->orderBy('code')->get();
        $liabilityAccounts = ChartAccount::active()->postable()->where('type', 'liability')->orderBy('code')->get();
        $equityAccounts = ChartAccount::active()->postable()->where('type', 'equity')->orderBy('code')->get();

        $assetLines = [];
        $totalAssets = 0;
        foreach ($assetAccounts as $account) {
            $b = $this->balanceService->calculateBalance($account->id, $periodId, $asOf);
            $raw = $b['raw_balance'] ?? 0;
            if (abs($raw) < 0.01) continue;
            $amount = abs($raw);
            $assetLines[] = ['account' => $account, 'amount' => $amount, 'balance_type' => $b['balance_type'] ?? 'debit'];
            $totalAssets += $amount;
        }

        $liabilityLines = [];
        $totalLiabilities = 0;
        foreach ($liabilityAccounts as $account) {
            $b = $this->balanceService->calculateBalance($account->id, $periodId, $asOf);
            $raw = $b['raw_balance'] ?? 0;
            if (abs($raw) < 0.01) continue;
            $amount = abs($raw);
            $liabilityLines[] = ['account' => $account, 'amount' => $amount];
            $totalLiabilities += $amount;
        }

        $equityLines = [];
        $totalEquity = 0;
        foreach ($equityAccounts as $account) {
            $b = $this->balanceService->calculateBalance($account->id, $periodId, $asOf);
            $raw = $b['raw_balance'] ?? 0;
            if (abs($raw) < 0.01) continue;
            $amount = abs($raw);
            $equityLines[] = ['account' => $account, 'amount' => $amount];
            $totalEquity += $amount;
        }

        $totalLiabilitiesEquity = $totalLiabilities + $totalEquity;

        return view('wesal.index', [
            'page' => 'finance',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'finance-report-balance-sheet',
            'assetLines' => $assetLines,
            'liabilityLines' => $liabilityLines,
            'equityLines' => $equityLines,
            'totalAssets' => $totalAssets,
            'totalLiabilities' => $totalLiabilities,
            'totalEquity' => $totalEquity,
            'totalLiabilitiesEquity' => $totalLiabilitiesEquity,
            'periods' => $periods,
            'selectedPeriod' => $selectedPeriod,
            'asOf' => $asOf,
        ]);
    }

    /**
     * تصدير الميزانية العمومية إلى Excel (عربي 100%)
     */
    public function balanceSheetExport(Request $request)
    {
        $periodId = $request->get('period_id');
        $asOf = $request->get('as_of') ? Carbon::parse($request->get('as_of')) : null;
        $periods = AccountingPeriod::with('fiscalYear')->orderBy('start_date', 'desc')->get();
        $currentPeriod = AccountingPeriod::getCurrent();
        $selectedPeriod = $periodId ? AccountingPeriod::find($periodId) : $currentPeriod;
        if ($selectedPeriod && !$periodId) {
            $periodId = $selectedPeriod->id;
        }

        $assetAccounts = ChartAccount::active()->postable()->where('type', 'asset')->orderBy('code')->get();
        $liabilityAccounts = ChartAccount::active()->postable()->where('type', 'liability')->orderBy('code')->get();
        $equityAccounts = ChartAccount::active()->postable()->where('type', 'equity')->orderBy('code')->get();

        $assetLines = [];
        $totalAssets = 0;
        foreach ($assetAccounts as $account) {
            $b = $this->balanceService->calculateBalance($account->id, $periodId, $asOf);
            $raw = $b['raw_balance'] ?? 0;
            if (abs($raw) < 0.01) continue;
            $amount = abs($raw);
            $assetLines[] = ['account' => $account, 'amount' => $amount];
            $totalAssets += $amount;
        }
        $liabilityLines = [];
        $totalLiabilities = 0;
        foreach ($liabilityAccounts as $account) {
            $b = $this->balanceService->calculateBalance($account->id, $periodId, $asOf);
            $raw = $b['raw_balance'] ?? 0;
            if (abs($raw) < 0.01) continue;
            $amount = abs($raw);
            $liabilityLines[] = ['account' => $account, 'amount' => $amount];
            $totalLiabilities += $amount;
        }
        $equityLines = [];
        $totalEquity = 0;
        foreach ($equityAccounts as $account) {
            $b = $this->balanceService->calculateBalance($account->id, $periodId, $asOf);
            $raw = $b['raw_balance'] ?? 0;
            if (abs($raw) < 0.01) continue;
            $amount = abs($raw);
            $equityLines[] = ['account' => $account, 'amount' => $amount];
            $totalEquity += $amount;
        }
        $totalLiabilitiesEquity = $totalLiabilities + $totalEquity;

        $spreadsheet = $this->excelExport->newSpreadsheet();
        $sheet = $this->excelExport->setupArabicSheet($spreadsheet, 'الميزانية العمومية');
        $row = 1;
        $this->excelExport->setCellValueByColumnAndRow($sheet,1, $row, $this->excelExport->ensureUtf8('البيان'));
        $this->excelExport->setCellValueByColumnAndRow($sheet,2, $row, $this->excelExport->ensureUtf8('المبلغ'));
        $this->excelExport->styleHeaderRow($sheet, $row, 2);
        $row++;
        $this->excelExport->setCellValueByColumnAndRow($sheet,1, $row, $this->excelExport->ensureUtf8('الأصول'));
        $row++;
        foreach ($assetLines as $r) {
            $this->excelExport->setCellValueByColumnAndRow($sheet,1, $row, $this->excelExport->ensureUtf8($r['account']->code . ' - ' . $r['account']->name_ar));
            $this->excelExport->setCellValueByColumnAndRow($sheet,2, $row, $r['amount']);
            $row++;
        }
        $this->excelExport->setCellValueByColumnAndRow($sheet,1, $row, $this->excelExport->ensureUtf8('إجمالي الأصول'));
        $this->excelExport->setCellValueByColumnAndRow($sheet,2, $row, $totalAssets);
        $sheet->getStyle("A{$row}:B{$row}")->getFont()->setBold(true)->setName('Tahoma');
        $row++;
        $row++;
        $this->excelExport->setCellValueByColumnAndRow($sheet,1, $row, $this->excelExport->ensureUtf8('الالتزامات وحقوق الملكية'));
        $row++;
        foreach ($liabilityLines as $r) {
            $this->excelExport->setCellValueByColumnAndRow($sheet,1, $row, $this->excelExport->ensureUtf8($r['account']->code . ' - ' . $r['account']->name_ar));
            $this->excelExport->setCellValueByColumnAndRow($sheet,2, $row, $r['amount']);
            $row++;
        }
        foreach ($equityLines as $r) {
            $this->excelExport->setCellValueByColumnAndRow($sheet,1, $row, $this->excelExport->ensureUtf8($r['account']->code . ' - ' . $r['account']->name_ar));
            $this->excelExport->setCellValueByColumnAndRow($sheet,2, $row, $r['amount']);
            $row++;
        }
        $this->excelExport->setCellValueByColumnAndRow($sheet,1, $row, $this->excelExport->ensureUtf8('إجمالي الالتزامات وحقوق الملكية'));
        $this->excelExport->setCellValueByColumnAndRow($sheet,2, $row, $totalLiabilitiesEquity);
        $sheet->getStyle("A{$row}:B{$row}")->getFont()->setBold(true)->setName('Tahoma');

        $filename = 'الميزانية-العمومية-' . ($selectedPeriod ? $selectedPeriod->period_name : date('Y-m-d'));
        return $this->excelExport->download($spreadsheet, $filename);
    }

    /**
     * قائمة الأنشطة حسب الوظيفة - معيار القطاع غير الربحي
     * (إيرادات، مصروفات برامج، إدارية، جمع تبرعات، صافي النشاط)
     */
    public function statementOfActivitiesByFunction(Request $request)
    {
        $periodId = $request->get('period_id');
        $asOf = $request->get('as_of') ? Carbon::parse($request->get('as_of')) : null;
        $periods = AccountingPeriod::with('fiscalYear')->orderBy('start_date', 'desc')->get();
        $currentPeriod = AccountingPeriod::getCurrent();
        $selectedPeriod = $periodId ? AccountingPeriod::find($periodId) : $currentPeriod;
        if ($selectedPeriod && !$periodId) {
            $periodId = $selectedPeriod->id;
        }

        $baseQuery = JournalLine::query()
            ->join('journal_entries', 'journal_lines.journal_entry_id', '=', 'journal_entries.id')
            ->join('chart_accounts', 'journal_lines.account_id', '=', 'chart_accounts.id')
            ->where('journal_entries.status', 'posted');
        if ($periodId) {
            $baseQuery->where('journal_entries.period_id', $periodId);
        }
        if ($asOf) {
            $baseQuery->where('journal_entries.entry_date', '<=', $asOf);
        }

        $revenueTotal = (clone $baseQuery)
            ->where('chart_accounts.type', 'revenue')
            ->selectRaw('COALESCE(SUM(journal_lines.credit - journal_lines.debit), 0) as total')
            ->value('total') ?: 0;

        $expensesByFunction = (clone $baseQuery)
            ->leftJoin('cost_centers', 'journal_lines.cost_center_id', '=', 'cost_centers.id')
            ->where('chart_accounts.type', 'expense')
            ->selectRaw("COALESCE(NULLIF(TRIM(cost_centers.center_type), ''), 'administrative') as center_type")
            ->selectRaw('COALESCE(SUM(journal_lines.debit - journal_lines.credit), 0) as total')
            ->groupBy(DB::raw("COALESCE(NULLIF(TRIM(cost_centers.center_type), ''), 'administrative')"))
            ->pluck('total', 'center_type');

        $program = (float) ($expensesByFunction->get('program') ?? 0);
        $administrative = (float) ($expensesByFunction->get('administrative') ?? 0);
        $fundraising = (float) ($expensesByFunction->get('fundraising') ?? 0);
        $unclassified = (float) ($expensesByFunction->get(null) ?? 0) + (float) ($expensesByFunction->get('') ?? 0);
        $totalExpenses = $program + $administrative + $fundraising + $unclassified;
        $netActivity = (float) $revenueTotal - $totalExpenses;

        return view('wesal.index', [
            'page' => 'finance',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'finance-report-activities-by-function',
            'periods' => $periods,
            'selectedPeriod' => $selectedPeriod,
            'asOf' => $asOf,
            'revenueTotal' => $revenueTotal,
            'expenseProgram' => $program,
            'expenseAdministrative' => $administrative,
            'expenseFundraising' => $fundraising,
            'expenseUnclassified' => $unclassified,
            'totalExpenses' => $totalExpenses,
            'netActivity' => $netActivity,
        ]);
    }

    /**
     * الحركة المالية - سجل تفصيلي لجميع القيود المرحلة (حركة مالية عامة)
     */
    public function financialMovement(Request $request)
    {
        $periodId = $request->get('period_id');
        $fromDate = $request->get('from_date') ? Carbon::parse($request->get('from_date')) : null;
        $toDate = $request->get('to_date') ? Carbon::parse($request->get('to_date')) : null;
        $accountId = $request->get('account_id');
        $entryType = $request->get('entry_type');

        $query = JournalLine::with(['journalEntry.period', 'account', 'costCenter'])
            ->join('journal_entries', 'journal_lines.journal_entry_id', '=', 'journal_entries.id')
            ->where('journal_entries.status', 'posted')
            ->select('journal_lines.*')
            ->orderBy('journal_entries.entry_date', 'asc')
            ->orderBy('journal_entries.id', 'asc')
            ->orderBy('journal_lines.line_order', 'asc');

        if ($periodId) {
            $query->where('journal_entries.period_id', $periodId);
        }
        if ($fromDate) {
            $query->where('journal_entries.entry_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->where('journal_entries.entry_date', '<=', $toDate);
        }
        if ($accountId) {
            $query->where('journal_lines.account_id', $accountId);
        }
        if ($entryType) {
            $query->where('journal_entries.entry_type', $entryType);
        }

        $lines = $query->paginate(100);
        $periods = AccountingPeriod::with('fiscalYear')->orderBy('start_date', 'desc')->get();
        $accounts = ChartAccount::active()->postable()->orderBy('code')->get();

        return view('wesal.index', [
            'page' => 'finance',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'financial-movement',
            'movementLines' => $lines,
            'periods' => $periods,
            'accounts' => $accounts,
            'periodId' => $periodId,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'accountId' => $accountId,
            'entryType' => $entryType,
        ]);
    }

    /**
     * تصدير الحركة المالية إلى Excel (تفصيلي)
     */
    public function financialMovementExport(Request $request)
    {
        $periodId = $request->get('period_id');
        $fromDate = $request->get('from_date') ? Carbon::parse($request->get('from_date')) : null;
        $toDate = $request->get('to_date') ? Carbon::parse($request->get('to_date')) : null;
        $accountId = $request->get('account_id');
        $entryType = $request->get('entry_type');

        $query = JournalLine::with(['journalEntry.period', 'account', 'costCenter'])
            ->join('journal_entries', 'journal_lines.journal_entry_id', '=', 'journal_entries.id')
            ->where('journal_entries.status', 'posted')
            ->select('journal_lines.*')
            ->orderBy('journal_entries.entry_date', 'asc')
            ->orderBy('journal_entries.id', 'asc')
            ->orderBy('journal_lines.line_order', 'asc');

        if ($periodId) {
            $query->where('journal_entries.period_id', $periodId);
        }
        if ($fromDate) {
            $query->where('journal_entries.entry_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->where('journal_entries.entry_date', '<=', $toDate);
        }
        if ($accountId) {
            $query->where('journal_lines.account_id', $accountId);
        }
        if ($entryType) {
            $query->where('journal_entries.entry_type', $entryType);
        }

        $lines = $query->limit(10000)->get();

        $spreadsheet = $this->excelExport->newSpreadsheet();
        $sheet = $this->excelExport->setupArabicSheet($spreadsheet, 'سجل القيود المالية');

        $headers = ['م', 'التاريخ', 'رقم القيد', 'نوع القيد', 'كود الحساب', 'اسم الحساب', 'البيان', 'مركز التكلفة', 'مدين', 'دائن', 'مرجع'];
        $colCount = count($headers);
        $row = 1;
        foreach ($headers as $c => $h) {
            $this->excelExport->setCellValueByColumnAndRow($sheet,$c + 1, $row, $this->excelExport->ensureUtf8($h));
        }
        $this->excelExport->styleHeaderRow($sheet, $row, $colCount);
        $row++;

        $entryTypeLabels = ['receipt' => 'سند قبض', 'payment' => 'سند صرف', 'adjusting' => 'قيد تسوية', 'manual' => 'قيد يومية'];
        foreach ($lines as $i => $line) {
            $entry = $line->journalEntry;
            $acc = $line->account;
            $this->excelExport->setCellValueByColumnAndRow($sheet,1, $row, $i + 1);
            $this->excelExport->setCellValueByColumnAndRow($sheet,2, $row, $entry ? $entry->entry_date?->format('Y-m-d') : '');
            $this->excelExport->setCellValueByColumnAndRow($sheet,3, $row, $this->excelExport->ensureUtf8($entry->entry_no ?? ''));
            $this->excelExport->setCellValueByColumnAndRow($sheet,4, $row, $this->excelExport->ensureUtf8($entryTypeLabels[$entry->entry_type ?? ''] ?? $entry->entry_type ?? ''));
            $this->excelExport->setCellValueByColumnAndRow($sheet,5, $row, $this->excelExport->ensureUtf8($acc->code ?? ''));
            $this->excelExport->setCellValueByColumnAndRow($sheet,6, $row, $this->excelExport->ensureUtf8($acc->name_ar ?? ''));
            $this->excelExport->setCellValueByColumnAndRow($sheet,7, $row, $this->excelExport->ensureUtf8($line->description ?: $entry->description ?? ''));
            $this->excelExport->setCellValueByColumnAndRow($sheet,8, $row, $this->excelExport->ensureUtf8($line->costCenter->name_ar ?? '-'));
            $this->excelExport->setCellValueByColumnAndRow($sheet,9, $row, $line->debit > 0 ? (float) $line->debit : '');
            $this->excelExport->setCellValueByColumnAndRow($sheet,10, $row, $line->credit > 0 ? (float) $line->credit : '');
            $this->excelExport->setCellValueByColumnAndRow($sheet,11, $row, $this->excelExport->ensureUtf8($line->reference ?? ''));
            $row++;
        }

        $filename = 'سجل-القيود-المالية-' . date('Y-m-d');
        return $this->excelExport->download($spreadsheet, $filename);
    }

    /**
     * تقرير التدفقات النقدية - النقدية وما في حكمها
     */
    public function cashFlow(Request $request)
    {
        $periodId = $request->get('period_id');
        $asOf = $request->get('as_of') ? Carbon::parse($request->get('as_of')) : null;
        $periods = AccountingPeriod::with('fiscalYear')->orderBy('start_date', 'desc')->get();
        $currentPeriod = AccountingPeriod::getCurrent();
        $selectedPeriod = $periodId ? AccountingPeriod::find($periodId) : $currentPeriod;
        if ($selectedPeriod && !$periodId) {
            $periodId = $selectedPeriod->id;
        }

        // حسابات النقدية (كود يبدأ بـ 111)
        $cashAccountIds = ChartAccount::active()->postable()
            ->where(function ($q) {
                $q->where('code', 'like', '111%')->orWhere('code', '111');
            })
            ->pluck('id')
            ->toArray();

        $periodStart = $selectedPeriod ? Carbon::parse($selectedPeriod->start_date)->startOfDay() : null;
        $periodEnd = $asOf ?? ($selectedPeriod ? Carbon::parse($selectedPeriod->end_date)->endOfDay() : now());

        $openingBalance = 0;
        $totalReceipts = 0;
        $totalPayments = 0;

        if (!empty($cashAccountIds)) {
            $asOfOpening = $periodStart ? $periodStart->copy()->subDay()->endOfDay() : null;
            foreach ($cashAccountIds as $aid) {
                $bOpen = $this->balanceService->calculateBalance($aid, null, $asOfOpening);
                $openingBalance += $bOpen['raw_balance'] ?? 0;
            }

            $movements = JournalLine::query()
                ->join('journal_entries', 'journal_lines.journal_entry_id', '=', 'journal_entries.id')
                ->whereIn('journal_lines.account_id', $cashAccountIds)
                ->where('journal_entries.status', 'posted');

            if ($periodId) {
                $movements->where('journal_entries.period_id', $periodId);
            }
            if ($periodStart) {
                $movements->where('journal_entries.entry_date', '>=', $periodStart);
            }
            $movements->where('journal_entries.entry_date', '<=', $periodEnd);

            $totals = $movements->selectRaw('
                COALESCE(SUM(journal_lines.debit), 0) as total_debit,
                COALESCE(SUM(journal_lines.credit), 0) as total_credit
            ')->first();

            $totalReceipts = (float) ($totals->total_debit ?? 0);
            $totalPayments = (float) ($totals->total_credit ?? 0);
        }

        $closingBalance = $openingBalance + $totalReceipts - $totalPayments;

        return view('wesal.index', [
            'page' => 'finance',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'finance-report-cash-flow',
            'periods' => $periods,
            'selectedPeriod' => $selectedPeriod,
            'asOf' => $asOf,
            'openingBalance' => $openingBalance,
            'totalReceipts' => $totalReceipts,
            'totalPayments' => $totalPayments,
            'closingBalance' => $closingBalance,
        ]);
    }

    /**
     * تصدير قائمة التدفقات النقدية إلى Excel
     */
    public function cashFlowExport(Request $request)
    {
        $periodId = $request->get('period_id');
        $asOf = $request->get('as_of') ? Carbon::parse($request->get('as_of')) : null;
        $periods = AccountingPeriod::with('fiscalYear')->orderBy('start_date', 'desc')->get();
        $currentPeriod = AccountingPeriod::getCurrent();
        $selectedPeriod = $periodId ? AccountingPeriod::find($periodId) : $currentPeriod;
        if ($selectedPeriod && !$periodId) {
            $periodId = $selectedPeriod->id;
        }
        $cashAccountIds = ChartAccount::active()->postable()
            ->where(function ($q) {
                $q->where('code', 'like', '111%')->orWhere('code', '111');
            })
            ->pluck('id')->toArray();
        $periodStart = $selectedPeriod ? Carbon::parse($selectedPeriod->start_date)->startOfDay() : null;
        $periodEnd = $asOf ?? ($selectedPeriod ? Carbon::parse($selectedPeriod->end_date)->endOfDay() : now());
        $openingBalance = 0;
        $totalReceipts = 0;
        $totalPayments = 0;
        if (!empty($cashAccountIds)) {
            $asOfOpening = $periodStart ? $periodStart->copy()->subDay()->endOfDay() : null;
            foreach ($cashAccountIds as $aid) {
                $bOpen = $this->balanceService->calculateBalance($aid, null, $asOfOpening);
                $openingBalance += $bOpen['raw_balance'] ?? 0;
            }
            $movements = JournalLine::query()
                ->join('journal_entries', 'journal_lines.journal_entry_id', '=', 'journal_entries.id')
                ->whereIn('journal_lines.account_id', $cashAccountIds)
                ->where('journal_entries.status', 'posted');
            if ($periodId) $movements->where('journal_entries.period_id', $periodId);
            if ($periodStart) $movements->where('journal_entries.entry_date', '>=', $periodStart);
            $movements->where('journal_entries.entry_date', '<=', $periodEnd);
            $totals = $movements->selectRaw('COALESCE(SUM(journal_lines.debit), 0) as total_debit, COALESCE(SUM(journal_lines.credit), 0) as total_credit')->first();
            $totalReceipts = (float) ($totals->total_debit ?? 0);
            $totalPayments = (float) ($totals->total_credit ?? 0);
        }
        $closingBalance = $openingBalance + $totalReceipts - $totalPayments;
        $spreadsheet = $this->excelExport->newSpreadsheet();
        $sheet = $this->excelExport->setupArabicSheet($spreadsheet, 'قائمة التدفقات النقدية');
        $row = 1;
        $rows = [
            [$this->excelExport->ensureUtf8('رصيد النقدية أول المدة'), $openingBalance],
            [$this->excelExport->ensureUtf8('إجمالي المقبوضات'), $totalReceipts],
            [$this->excelExport->ensureUtf8('إجمالي المدفوعات'), -$totalPayments],
            [$this->excelExport->ensureUtf8('رصيد النقدية آخر المدة'), $closingBalance],
        ];
        foreach ($rows as $r) {
            $this->excelExport->setCellValueByColumnAndRow($sheet, 1, $row, $r[0]);
            $this->excelExport->setCellValueByColumnAndRow($sheet, 2, $row, $r[1]);
            $row++;
        }
        return $this->excelExport->download($spreadsheet, 'قائمة-التدفقات-النقدية-' . date('Y-m-d'));
    }

    /**
     * كشف حساب عام - اختيار الحساب وعرض كشفه
     */
    public function generalLedger(Request $request)
    {
        $accountId = $request->get('account_id');
        if ($accountId) {
            $account = ChartAccount::find($accountId);
            if ($account) {
                return redirect()->route('wesal.finance.chart-accounts.ledger', [
                    'chartAccount' => $account,
                    'period_id' => $request->get('period_id'),
                    'from_date' => $request->get('from_date'),
                    'to_date' => $request->get('to_date'),
                ]);
            }
        }
        $periods = AccountingPeriod::with('fiscalYear')->orderBy('start_date', 'desc')->get();
        $accounts = ChartAccount::active()->postable()->orderBy('code')->get();
        return view('wesal.index', [
            'page' => 'finance',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'finance-report-general-ledger',
            'periods' => $periods,
            'accounts' => $accounts,
        ]);
    }

    /**
     * قائمة التغيرات في صافي الأصول (مطلوب للقطاع غير الربحي)
     */
    public function netAssetsChanges(Request $request)
    {
        $periodId = $request->get('period_id');
        $asOf = $request->get('as_of') ? Carbon::parse($request->get('as_of')) : null;
        $periods = AccountingPeriod::with('fiscalYear')->orderBy('start_date', 'desc')->get();
        $currentPeriod = AccountingPeriod::getCurrent();
        $selectedPeriod = $periodId ? AccountingPeriod::find($periodId) : $currentPeriod;
        if ($selectedPeriod && !$periodId) {
            $periodId = $selectedPeriod->id;
        }
        $periodStart = $selectedPeriod ? Carbon::parse($selectedPeriod->start_date)->startOfDay() : null;
        $periodEnd = $asOf ?? ($selectedPeriod ? Carbon::parse($selectedPeriod->end_date)->endOfDay() : now());

        $equityAccounts = ChartAccount::active()->postable()->where('type', 'equity')->orderBy('code')->get();
        $beginningBalance = 0;
        $changes = [];
        $totalChanges = 0;
        foreach ($equityAccounts as $acc) {
            $bStart = $this->balanceService->calculateBalance($acc->id, null, $periodStart ? $periodStart->copy()->subDay()->endOfDay() : null);
            $bEnd = $this->balanceService->calculateBalance($acc->id, $periodId, $periodEnd);
            $beginning = $bStart['raw_balance'] ?? 0;
            $ending = $bEnd['raw_balance'] ?? 0;
            $change = $ending - $beginning;
            $beginningBalance += $beginning;
            $totalChanges += $change;
            if (abs($beginning) >= 0.01 || abs($ending) >= 0.01 || abs($change) >= 0.01) {
                $changes[] = ['account' => $acc, 'beginning' => $beginning, 'ending' => $ending, 'change' => $change];
            }
        }

        return view('wesal.index', [
            'page' => 'finance',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'finance-report-net-assets-changes',
            'periods' => $periods,
            'selectedPeriod' => $selectedPeriod,
            'asOf' => $asOf,
            'changes' => $changes,
            'beginningBalance' => $beginningBalance,
            'totalChanges' => $totalChanges,
        ]);
    }

    /**
     * تصدير قائمة التغيرات في صافي الأصول إلى Excel
     */
    public function netAssetsChangesExport(Request $request)
    {
        $periodId = $request->get('period_id');
        $asOf = $request->get('as_of') ? Carbon::parse($request->get('as_of')) : null;
        $periods = AccountingPeriod::with('fiscalYear')->orderBy('start_date', 'desc')->get();
        $currentPeriod = AccountingPeriod::getCurrent();
        $selectedPeriod = $periodId ? AccountingPeriod::find($periodId) : $currentPeriod;
        if ($selectedPeriod && !$periodId) $periodId = $selectedPeriod->id;
        $periodStart = $selectedPeriod ? Carbon::parse($selectedPeriod->start_date)->startOfDay() : null;
        $periodEnd = $asOf ?? ($selectedPeriod ? Carbon::parse($selectedPeriod->end_date)->endOfDay() : now());
        $equityAccounts = ChartAccount::active()->postable()->where('type', 'equity')->orderBy('code')->get();
        $beginningBalance = 0;
        $totalChanges = 0;
        $rows = [];
        foreach ($equityAccounts as $acc) {
            $bStart = $this->balanceService->calculateBalance($acc->id, null, $periodStart ? $periodStart->copy()->subDay()->endOfDay() : null);
            $bEnd = $this->balanceService->calculateBalance($acc->id, $periodId, $periodEnd);
            $beginning = $bStart['raw_balance'] ?? 0;
            $ending = $bEnd['raw_balance'] ?? 0;
            $change = $ending - $beginning;
            $beginningBalance += $beginning;
            $totalChanges += $change;
            if (abs($beginning) >= 0.01 || abs($ending) >= 0.01 || abs($change) >= 0.01) {
                $rows[] = [$acc->code . ' - ' . $acc->name_ar, $beginning, $ending, $change];
            }
        }
        $spreadsheet = $this->excelExport->newSpreadsheet();
        $sheet = $this->excelExport->setupArabicSheet($spreadsheet, 'قائمة التغيرات في صافي الأصول');
        $headers = [$this->excelExport->ensureUtf8('الحساب'), $this->excelExport->ensureUtf8('الرصيد الافتتاحي'), $this->excelExport->ensureUtf8('الرصيد الختامي'), $this->excelExport->ensureUtf8('التغير')];
        for ($c = 0; $c < 4; $c++) {
            $this->excelExport->setCellValueByColumnAndRow($sheet, $c + 1, 1, $headers[$c]);
        }
        $this->excelExport->styleHeaderRow($sheet, 1, 4);
        $row = 2;
        foreach ($rows as $r) {
            for ($c = 0; $c < 4; $c++) {
                $this->excelExport->setCellValueByColumnAndRow($sheet, $c + 1, $row, $c > 0 ? (float) $r[$c] : $this->excelExport->ensureUtf8($r[$c]));
            }
            $row++;
        }
        return $this->excelExport->download($spreadsheet, 'قائمة-التغيرات-في-صافي-الأصول-' . date('Y-m-d'));
    }
}
