<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Decision;
use Illuminate\Http\Request;

class DecisionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:hr_employees,id',
            'decision_type' => 'required|string|max:50',
            'decision_date' => 'required|date',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        Decision::create($request->only(['employee_id', 'decision_type', 'decision_date', 'reference', 'notes']));
        return redirect()->route('wesal.hr.show', ['section' => 'decisions'])->with('success', 'تم إضافة القرار.');
    }

    public function destroy(Decision $decision)
    {
        $decision->delete();
        return redirect()->route('wesal.hr.show', ['section' => 'decisions'])->with('success', 'تم حذف القرار.');
    }
}
