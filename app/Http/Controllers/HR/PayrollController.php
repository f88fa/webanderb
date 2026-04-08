<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Employee;
use App\Models\HR\PayrollLine;
use App\Models\HR\PayrollRun;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function storeRun(Request $request)
    {
        if (!auth()->user()->can('hr.payroll.run')) {
            abort(403, 'ليس لديك صلاحية تنفيذ مسير الرواتب.');
        }
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2100',
        ]);

        $exists = PayrollRun::where('month', $request->month)->where('year', $request->year)->exists();
        if ($exists) {
            return redirect()->route('wesal.hr.show', ['section' => 'payroll'])->with('error', 'مسير راتب لهذا الشهر موجود مسبقاً.');
        }

        $run = PayrollRun::create([
            'month' => $request->month,
            'year' => $request->year,
            'status' => 'draft',
        ]);

        foreach (Employee::active()->get() as $emp) {
            PayrollLine::create([
                'payroll_run_id' => $run->id,
                'employee_id' => $emp->id,
                'base_salary' => $emp->base_salary ?? 0,
                'allowances' => 0,
                'deductions' => 0,
                'advance_deduction' => 0,
                'net_salary' => $emp->base_salary ?? 0,
            ]);
        }

        return redirect()->route('wesal.hr.show', ['section' => 'payroll'])->with('success', 'تم إنشاء مسير الرواتب.');
    }
}
