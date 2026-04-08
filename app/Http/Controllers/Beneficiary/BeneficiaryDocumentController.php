<?php

namespace App\Http\Controllers\Beneficiary;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary\BeneficiaryDocument;
use Illuminate\Http\Request;

class BeneficiaryDocumentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'beneficiary_id' => 'required|exists:ben_beneficiaries,id',
            'document_type' => 'nullable|string|max:50',
            'document_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        BeneficiaryDocument::create($request->only(['beneficiary_id', 'document_type', 'document_date', 'notes']));
        return redirect()->route('wesal.beneficiaries.show', ['section' => 'documents'])->with('success', 'تم إضافة المستند.');
    }

    public function destroy(BeneficiaryDocument $beneficiary_document)
    {
        $beneficiary_document->delete();
        return redirect()->route('wesal.beneficiaries.show', ['section' => 'documents'])->with('success', 'تم حذف المستند.');
    }
}
