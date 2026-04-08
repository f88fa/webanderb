<?php

namespace App\Http\Controllers\Beneficiary;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary\Assessment;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'beneficiary_id' => 'required|exists:ben_beneficiaries,id',
            'assessment_date' => 'required|date',
            'eligibility_score' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        Assessment::create($request->only(['beneficiary_id', 'assessment_date', 'eligibility_score', 'notes']));
        return redirect()->route('wesal.beneficiaries.show', ['section' => 'assessment'])->with('success', 'تم إضافة التقييم.');
    }
}
