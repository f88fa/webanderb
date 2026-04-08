<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Advance;
use Illuminate\Http\Request;

class AdvanceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:hr_employees,id',
            'amount' => 'required|numeric|min:0.01',
            'request_date' => 'required|date',
            'deduct_months' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        Advance::create([
            'employee_id' => $request->employee_id,
            'amount' => $request->amount,
            'request_date' => $request->request_date,
            'deduct_months' => $request->deduct_months ?? 1,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        return redirect()->route('wesal.hr.show', ['section' => 'payroll', 'sub' => 'advances'])->with('success', 'تم تقديم طلب السلفة.');
    }

    public function approve(Advance $advance)
    {
        $advance->update(['status' => 'approved']);
        return redirect()->route('wesal.hr.show', ['section' => 'payroll', 'sub' => 'advances'])->with('success', 'تمت الموافقة على السلفة.');
    }
}
