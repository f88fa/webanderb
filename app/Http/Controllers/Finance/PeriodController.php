<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\AccountingPeriod;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class PeriodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $fiscalYearId = $request->get('fiscal_year_id');
        
        $query = AccountingPeriod::with('fiscalYear', 'closedByUser')
            ->orderBy('start_date', 'desc');

        if ($fiscalYearId) {
            $query->where('fiscal_year_id', $fiscalYearId);
        }

        $periods = $query->get();
        $fiscalYears = \App\Models\FiscalYear::with('periods')->orderBy('year_name', 'desc')->get();

        return view('wesal.index', [
            'page' => 'finance',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'periods',
            'periods' => $periods,
            'fiscalYears' => $fiscalYears,
            'fiscalYearId' => $fiscalYearId,
        ]);
    }

    /**
     * Close posting for a period (allow only adjustments).
     */
    public function closePosting(AccountingPeriod $period)
    {
        if ($period->status === 'closed') {
            return back()->withErrors(['error' => 'الفترة مغلقة بالفعل']);
        }

        $period->update([
            'allow_posting' => false,
        ]);

        AuditLog::log('close_posting', $period, ['allow_posting' => true], ['allow_posting' => false]);

        return back()->with('success', 'تم إغلاق الترحيل العادي للفترة');
    }

    /**
     * Open posting for a period (requires FinanceAdmin permission).
     */
    public function openPosting(AccountingPeriod $period)
    {
        if (!auth()->user()->hasPermissionTo('finance.admin')) {
            return back()->withErrors(['error' => 'ليس لديك صلاحية لفتح الترحيل']);
        }

        $period->update([
            'allow_posting' => true,
        ]);

        AuditLog::log('open_posting', $period, ['allow_posting' => false], ['allow_posting' => true]);

        return back()->with('success', 'تم فتح الترحيل العادي للفترة');
    }

    /**
     * Close adjustments for a period.
     */
    public function closeAdjustments(AccountingPeriod $period)
    {
        if ($period->status === 'closed') {
            return back()->withErrors(['error' => 'الفترة مغلقة بالفعل']);
        }

        $period->update([
            'allow_adjustments' => false,
            'status' => 'closed',
            'closed_at' => now(),
            'closed_by' => auth()->id(),
        ]);

        AuditLog::log('close_period', $period, ['status' => 'open'], ['status' => 'closed']);

        return back()->with('success', 'تم إغلاق التسويات وإقفال الفترة');
    }

    /**
     * Open adjustments for a period (requires FinanceAdmin permission).
     */
    public function openAdjustments(AccountingPeriod $period)
    {
        if (!auth()->user()->hasPermissionTo('finance.admin')) {
            return back()->withErrors(['error' => 'ليس لديك صلاحية لفتح التسويات']);
        }

        $period->update([
            'allow_adjustments' => true,
            'status' => 'open',
            'closed_at' => null,
            'closed_by' => null,
        ]);

        AuditLog::log('open_adjustments', $period, ['status' => 'closed'], ['status' => 'open']);

        return back()->with('success', 'تم فتح التسويات للفترة');
    }
}
