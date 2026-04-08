<?php

namespace App\Http\Controllers\Beneficiary;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary\MedicalRecord;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'beneficiary_id' => 'required|exists:ben_beneficiaries,id',
            'record_date' => 'required|date',
            'diagnosis' => 'nullable|string|max:255',
            'treatment' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        MedicalRecord::create($request->only(['beneficiary_id', 'record_date', 'diagnosis', 'treatment', 'notes']));
        return redirect()->route('wesal.beneficiaries.show', ['section' => 'medical'])->with('success', 'تم إضافة السجل الطبي.');
    }
}
