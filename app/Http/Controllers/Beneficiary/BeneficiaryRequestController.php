<?php

namespace App\Http\Controllers\Beneficiary;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary\BeneficiaryRequest;
use Illuminate\Http\Request;

class BeneficiaryRequestController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'beneficiary_id' => 'required|exists:ben_beneficiaries,id',
            'request_type' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        BeneficiaryRequest::create([
            'beneficiary_id' => $request->beneficiary_id,
            'request_type' => $request->request_type,
            'description' => $request->description,
            'notes' => $request->notes,
            'status' => 'new',
            'submitted_at' => now(),
        ]);

        return redirect()->route('wesal.beneficiaries.show', ['section' => 'requests', 'sub' => 'new'])->with('success', 'تم تقديم الطلب.');
    }

    public function moveToStudy(BeneficiaryRequest $beneficiary_request)
    {
        $beneficiary_request->update(['status' => 'under_study', 'studied_at' => now()]);
        return redirect()->back()->with('success', 'تم نقل الطلب إلى تحت الدراسة.');
    }

    public function approve(BeneficiaryRequest $beneficiary_request)
    {
        $beneficiary_request->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);
        return redirect()->back()->with('success', 'تم اعتماد الطلب.');
    }

    public function reject(Request $request, BeneficiaryRequest $beneficiary_request)
    {
        $request->validate(['rejection_reason' => 'nullable|string|max:500']);
        $beneficiary_request->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);
        return redirect()->back()->with('success', 'تم رفض الطلب.');
    }
}
