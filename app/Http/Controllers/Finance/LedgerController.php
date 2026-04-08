<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\ChartAccount;
use App\Models\AccountingPeriod;
use App\Models\JournalLine;
use App\Services\Finance\ChartAccountBalanceService;
use App\Services\Finance\ExcelExportService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LedgerController extends Controller
{
    protected $balanceService;

    protected $excelExport;

    public function __construct(ChartAccountBalanceService $balanceService, ExcelExportService $excelExport)
    {
        $this->balanceService = $balanceService;
        $this->excelExport = $excelExport;
    }

    /**
     * عرض كشف حساب للحساب
     */
    public function index(Request $request, $chartAccount)
    {
        $account = ChartAccount::findOrFail($chartAccount);
        $periodId = $request->get('period_id');
        $asOf = $request->get('as_of');
        $fromDate = $request->get('from_date') ? Carbon::parse($request->get('from_date')) : null;
        $toDate = $request->get('to_date') ? Carbon::parse($request->get('to_date')) : null;
        
        // بناء query للحركات
        $query = JournalLine::with(['journalEntry.period', 'journalEntry.postedByUser', 'costCenter'])
            ->join('journal_entries', 'journal_lines.journal_entry_id', '=', 'journal_entries.id')
            ->where('journal_lines.account_id', $account->id)
            ->where('journal_entries.status', 'posted');
        
        // فلترة حسب الفترة
        if ($periodId) {
            $query->where('journal_entries.period_id', $periodId);
        }
        
        // فلترة حسب التاريخ
        if ($asOf) {
            $asOfDate = Carbon::parse($asOf);
            $query->where('journal_entries.entry_date', '<=', $asOfDate);
        } elseif ($fromDate || $toDate) {
            if ($fromDate) {
                $query->where('journal_entries.entry_date', '>=', $fromDate);
            }
            if ($toDate) {
                $query->where('journal_entries.entry_date', '<=', $toDate);
            }
        }
        
        // الرصيد الافتتاحي (قبل الحركات المحددة)
        $openingBalanceDate = null;
        if ($fromDate) {
            $openingBalanceDate = $fromDate->copy()->subDay();
        } elseif ($periodId) {
            $period = AccountingPeriod::find($periodId);
            if ($period) {
                $openingBalanceDate = $period->start_date->copy()->subDay();
            }
        }
        
        $openingBalance = $this->balanceService->calculateBalanceWithRollup(
            $account->id,
            null, // لا نستخدم period_id للرصيد الافتتاحي
            $openingBalanceDate
        );
        
        // ترتيب الحركات بشكل صحيح (journal_entries مُضمَّن في الـ join أعلاه)
        $lines = $query->select('journal_lines.*')
            ->orderBy('journal_entries.entry_date', 'asc')
            ->orderBy('journal_entries.id', 'asc')
            ->orderBy('journal_lines.line_order', 'asc')
            ->get();
        
        // حساب الرصيد الجاري (يبدأ من الرصيد الافتتاحي)
        $runningBalance = $openingBalance['raw_balance'] ?? 0;
        $transactions = [];
        $totalDebit = 0;
        $totalCredit = 0;
        
        foreach ($lines as $line) {
            $debit = (float) ($line->debit ?? 0);
            $credit = (float) ($line->credit ?? 0);
            
            // حساب الرصيد الجاري حسب طبيعة الحساب
            // للحسابات المدينة: المدين يزيد الرصيد، الدائن يقلل الرصيد
            // للحسابات الدائنة: الدائن يزيد الرصيد، المدين يقلل الرصيد
            if ($account->nature === 'debit') {
                $runningBalance += ($debit - $credit);
            } else {
                $runningBalance += ($credit - $debit);
            }
            
            $totalDebit += $debit;
            $totalCredit += $credit;
            
            $transactions[] = [
                'id' => $line->id,
                'entry_date' => $line->journalEntry->entry_date,
                'entry_no' => $line->journalEntry->entry_no,
                'description' => $line->description ?: $line->journalEntry->description,
                'debit' => $debit,
                'credit' => $credit,
                'running_balance' => $runningBalance,
                'reference' => $line->reference ?? null,
                'cost_center' => $line->costCenter ? $line->costCenter->name_ar : null,
            ];
        }
        
        // جلب الفترات للفلترة
        $periods = AccountingPeriod::with('fiscalYear')->orderBy('start_date', 'desc')->get();
        
        $chartAccount = $account; // للتوافق مع View
        
        // الرصيد النهائي
        $finalBalance = $runningBalance;
        
        return view('wesal.index', array_merge(
            [
                'page' => 'finance',
                'settings' => \App\Models\SiteSetting::getAllAsArray(),
                'formType' => 'ledger',
            ],
            compact(
                'chartAccount',
                'transactions',
                'periods',
                'periodId',
                'asOf',
                'fromDate',
                'toDate',
                'totalDebit',
                'totalCredit',
                'runningBalance',
                'finalBalance',
                'openingBalance'
            )
        ));
    }

    /**
     * تصدير كشف حساب (حركة الحساب) إلى Excel
     */
    public function exportExcel(Request $request, $chartAccount)
    {
        $account = ChartAccount::findOrFail($chartAccount);
        $periodId = $request->get('period_id');
        $fromDate = $request->get('from_date') ? Carbon::parse($request->get('from_date')) : null;
        $toDate = $request->get('to_date') ? Carbon::parse($request->get('to_date')) : null;

        $query = JournalLine::with(['journalEntry.period', 'costCenter'])
            ->join('journal_entries', 'journal_lines.journal_entry_id', '=', 'journal_entries.id')
            ->where('journal_lines.account_id', $account->id)
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

        $lines = $query->get();
        $openingBalanceDate = $fromDate ? $fromDate->copy()->subDay() : null;
        $openingBalance = $this->balanceService->calculateBalanceWithRollup($account->id, null, $openingBalanceDate);
        $runningBalance = $openingBalance['raw_balance'] ?? 0;

        $spreadsheet = $this->excelExport->newSpreadsheet();
        $sheet = $this->excelExport->setupArabicSheet($spreadsheet, 'كشف حساب - ' . $account->code);

        $title = $this->excelExport->ensureUtf8('كشف حساب - ' . $account->code . ' - ' . $account->name_ar);
        $this->excelExport->setCellValueByColumnAndRow($sheet,1, 1, $title);
        $this->excelExport->mergeCellsByColumnAndRow($sheet, 1, 1, 7, 1);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setName('Tahoma');
        $row = 3;

        $headers = ['التاريخ', 'رقم القيد', 'الوصف', 'مدين', 'دائن', 'الرصيد الجاري', 'مرجع'];
        foreach ($headers as $c => $h) {
            $this->excelExport->setCellValueByColumnAndRow($sheet,$c + 1, $row, $this->excelExport->ensureUtf8($h));
        }
        $this->excelExport->styleHeaderRow($sheet, $row, 7);
        $row++;

        if (abs($runningBalance) >= 0.01) {
            $this->excelExport->setCellValueByColumnAndRow($sheet,1, $row, $this->excelExport->ensureUtf8('رصيد افتتاحي'));
            $this->excelExport->setCellValueByColumnAndRow($sheet,4, $row, $runningBalance >= 0 ? (float) $runningBalance : '');
            $this->excelExport->setCellValueByColumnAndRow($sheet,5, $row, $runningBalance < 0 ? (float) abs($runningBalance) : '');
            $this->excelExport->setCellValueByColumnAndRow($sheet,6, $row, (float) $runningBalance);
            $row++;
        }

        foreach ($lines as $line) {
            $debit = (float) ($line->debit ?? 0);
            $credit = (float) ($line->credit ?? 0);
            if ($account->nature === 'debit') {
                $runningBalance += ($debit - $credit);
            } else {
                $runningBalance += ($credit - $debit);
            }
            $this->excelExport->setCellValueByColumnAndRow($sheet,1, $row, $line->journalEntry->entry_date?->format('Y-m-d'));
            $this->excelExport->setCellValueByColumnAndRow($sheet,2, $row, $this->excelExport->ensureUtf8($line->journalEntry->entry_no ?? ''));
            $this->excelExport->setCellValueByColumnAndRow($sheet,3, $row, $this->excelExport->ensureUtf8($line->description ?: $line->journalEntry->description ?? ''));
            $this->excelExport->setCellValueByColumnAndRow($sheet,4, $row, $debit > 0 ? $debit : '');
            $this->excelExport->setCellValueByColumnAndRow($sheet,5, $row, $credit > 0 ? $credit : '');
            $this->excelExport->setCellValueByColumnAndRow($sheet,6, $row, (float) $runningBalance);
            $this->excelExport->setCellValueByColumnAndRow($sheet,7, $row, $this->excelExport->ensureUtf8($line->reference ?? ''));
            $row++;
        }

        $this->excelExport->setCellValueByColumnAndRow($sheet,3, $row, $this->excelExport->ensureUtf8('الإجمالي'));
        $this->excelExport->setCellValueByColumnAndRow($sheet,4, $row, $lines->sum('debit'));
        $this->excelExport->setCellValueByColumnAndRow($sheet,5, $row, $lines->sum('credit'));
        $this->excelExport->setCellValueByColumnAndRow($sheet,6, $row, (float) $runningBalance);
        $sheet->getStyle("A{$row}:G{$row}")->getFont()->setBold(true)->setName('Tahoma');

        $filename = 'كشف-حساب-' . $account->code . '-' . date('Y-m-d');
        return $this->excelExport->download($spreadsheet, $filename);
    }
}
