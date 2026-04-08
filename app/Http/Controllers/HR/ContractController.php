<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Contract;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function store(Request $request)
    {
        if (!auth()->user()->can('hr.contracts.create')) {
            abort(403, 'ليس لديك صلاحية إضافة عقد.');
        }
        $request->validate([
            'employee_id' => 'required|exists:hr_employees,id',
            'contract_type' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        Contract::create($request->only(['employee_id', 'contract_type', 'start_date', 'end_date', 'notes']));
        return redirect()->route('wesal.hr.show', ['section' => 'contracts'])->with('success', 'تم إضافة العقد.');
    }

    public function destroy(Contract $contract)
    {
        if (!auth()->user()->can('hr.contracts.delete')) {
            abort(403, 'ليس لديك صلاحية حذف العقود.');
        }
        $contract->delete();
        return redirect()->route('wesal.hr.show', ['section' => 'contracts'])->with('success', 'تم حذف العقد.');
    }
}
