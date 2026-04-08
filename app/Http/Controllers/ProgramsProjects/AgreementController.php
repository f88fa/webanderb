<?php

namespace App\Http\Controllers\ProgramsProjects;

use App\Http\Controllers\Controller;
use App\Models\ProgramsProjects\Agreement;
use Illuminate\Http\Request;

class AgreementController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'donor_id' => 'required|exists:pp_donors,id',
            'project_id' => 'nullable|exists:pp_projects,id',
            'agreement_no' => 'nullable|string|max:50',
            'title' => 'required|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        Agreement::create($request->only([
            'donor_id', 'project_id', 'agreement_no', 'title', 'amount',
            'start_date', 'end_date', 'notes',
        ]));

        return redirect()->back()->with('success', 'تم إضافة الاتفاقية.');
    }

    public function destroy(Agreement $agreement)
    {
        $agreement->delete();
        return redirect()->back()->with('success', 'تم حذف الاتفاقية.');
    }
}
