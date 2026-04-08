<?php

namespace App\Http\Controllers\ProgramsProjects;

use App\Http\Controllers\Controller;
use App\Models\ProgramsProjects\Grant;
use Illuminate\Http\Request;

class GrantController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'donor_id' => 'required|exists:pp_donors,id',
            'project_id' => 'nullable|exists:pp_projects,id',
            'agreement_id' => 'nullable|exists:pp_agreements,id',
            'amount' => 'required|numeric|min:0',
            'grant_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        Grant::create($request->only([
            'donor_id', 'project_id', 'agreement_id', 'amount', 'grant_date', 'notes',
        ]));

        return redirect()->back()->with('success', 'تم إضافة المنحة.');
    }

    public function destroy(Grant $grant)
    {
        $grant->delete();
        return redirect()->back()->with('success', 'تم حذف المنحة.');
    }
}
