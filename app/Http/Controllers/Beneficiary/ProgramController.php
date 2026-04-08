<?php

namespace App\Http\Controllers\Beneficiary;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        Program::create($request->only(['name_ar', 'description', 'start_date', 'end_date']));
        return redirect()->route('wesal.beneficiaries.show', ['section' => 'programs'])->with('success', 'تم إضافة البرنامج.');
    }

    public function destroy(Program $program)
    {
        $program->delete();
        return redirect()->route('wesal.beneficiaries.show', ['section' => 'programs'])->with('success', 'تم حذف البرنامج.');
    }
}
