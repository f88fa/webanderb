<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\AllowanceDeductionType;
use Illuminate\Http\Request;

class AllowanceDeductionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'type' => 'required|in:allowance,deduction',
            'is_fixed' => 'boolean',
            'default_amount' => 'nullable|numeric|min:0',
        ]);

        AllowanceDeductionType::create([
            'name_ar' => $request->name_ar,
            'type' => $request->type,
            'is_fixed' => $request->boolean('is_fixed', true),
            'default_amount' => $request->default_amount ?? 0,
            'is_active' => true,
        ]);

        return redirect()->route('wesal.hr.show', ['section' => 'payroll', 'sub' => 'allowances'])->with('success', 'تمت الإضافة.');
    }

    public function destroy(AllowanceDeductionType $allowanceDeduction)
    {
        $allowanceDeduction->delete();
        return redirect()->route('wesal.hr.show', ['section' => 'payroll', 'sub' => 'allowances'])->with('success', 'تم الحذف.');
    }
}
