<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\FiscalYear;
use App\Models\AuditLog;
use App\Services\Finance\FiscalYearClosingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FiscalYearController extends Controller
{
    public function __construct(
        protected FiscalYearClosingService $closingService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fiscalYears = FiscalYear::with('periods', 'closedByUser')
            ->orderBy('year_name', 'desc')
            ->get();

        return view('wesal.index', [
            'page' => 'finance',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'fiscal-years',
            'fiscalYears' => $fiscalYears,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'year_name' => ['required', 'string', 'max:50', 'unique:fiscal_years,year_name'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
        ]);

        DB::beginTransaction();
        try {
            $fiscalYear = FiscalYear::create([
                'year_name' => $request->year_name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => 'open',
            ]);

            // إنشاء الفترات الشهرية تلقائياً
            $this->createMonthlyPeriods($fiscalYear);

            DB::commit();

            AuditLog::log('create_fiscal_year', $fiscalYear, null, $fiscalYear->toArray());

            return redirect()->route('wesal.finance.fiscal-years.index')
                ->with('success', 'تم إنشاء السنة المالية بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Close fiscal year.
     */
    public function close(FiscalYear $fiscalYear)
    {
        if ($fiscalYear->status === 'closed') {
            return back()->withErrors(['error' => 'السنة المالية مغلقة بالفعل']);
        }

        if (!$fiscalYear->canBeClosed()) {
            return back()->withErrors(['error' => 'يجب إغلاق جميع الفترات أولاً']);
        }

        DB::beginTransaction();
        try {
            $closingEntry = $this->closingService->createClosingEntry($fiscalYear);
            if ($closingEntry) {
                AuditLog::log('create_closing_entry', $closingEntry, null, ['fiscal_year_id' => $fiscalYear->id]);
            }

            $fiscalYear->update([
                'status' => 'closed',
                'closed_at' => now(),
                'closed_by' => auth()->id(),
            ]);

            DB::commit();

            AuditLog::log('close_fiscal_year', $fiscalYear, ['status' => 'open'], ['status' => 'closed']);

            $message = 'تم إقفال السنة المالية بنجاح.';
            if ($closingEntry) {
                $message .= ' تم إنشاء قيد الإقفال رقم: ' . $closingEntry->entry_no;
            } else {
                $message .= ' (لا توجد أرصدة إيرادات أو مصروفات لإقفالها)';
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()]);
        }
    }

    /**
     * إنشاء الفترات الشهرية تلقائياً
     */
    private function createMonthlyPeriods(FiscalYear $fiscalYear)
    {
        $start = \Carbon\Carbon::parse($fiscalYear->start_date);
        $end = \Carbon\Carbon::parse($fiscalYear->end_date);
        $current = $start->copy();
        $periodNumber = 1;

        while ($current->lte($end)) {
            $periodStart = $current->copy()->startOfMonth();
            $periodEnd = $current->copy()->endOfMonth();

            // التأكد من أن الفترة ضمن السنة المالية
            if ($periodStart->lt($fiscalYear->start_date)) {
                $periodStart = \Carbon\Carbon::parse($fiscalYear->start_date);
            }
            if ($periodEnd->gt($fiscalYear->end_date)) {
                $periodEnd = \Carbon\Carbon::parse($fiscalYear->end_date);
            }

            \App\Models\AccountingPeriod::create([
                'fiscal_year_id' => $fiscalYear->id,
                'period_name' => $current->format('Y-m'),
                'start_date' => $periodStart,
                'end_date' => $periodEnd,
                'status' => 'open',
                'allow_posting' => true,
                'allow_adjustments' => true,
            ]);

            $current->addMonth();
            $periodNumber++;
        }
    }

}
