<?php

namespace App\Http\Controllers\Beneficiary;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary\Beneficiary;
use App\Models\Beneficiary\RegistrationRequest;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class RegistrationRequestController extends Controller
{
    public function show(RegistrationRequest $registration_request)
    {
        $registration_request->load(['user', 'beneficiaryForm.fields' => fn ($q) => $q->orderBy('sort_order')]);

        return view('wesal.index', [
            'page' => 'beneficiaries',
            'settings' => SiteSetting::getAllAsArray(),
            'formType' => 'ben-registration-request-detail',
            'benSection' => 'registration-requests',
            'benSub' => (string) $registration_request->id,
            'registrationRequestDetail' => $registration_request,
        ]);
    }

    public function approve(RegistrationRequest $registration_request)
    {
        if ($registration_request->status !== 'pending') {
            return redirect()->back()->with('error', 'تمت معالجة هذا الطلب مسبقاً.');
        }

        $beneficiaryNo = BeneficiaryController::generateBeneficiaryNo();
        $beneficiary = Beneficiary::create([
            'beneficiary_no' => $beneficiaryNo,
            'user_id' => $registration_request->user_id,
            'beneficiary_form_id' => $registration_request->beneficiary_form_id,
            'name_ar' => $registration_request->name_ar,
            'name_en' => $registration_request->name_en,
            'national_id' => $registration_request->national_id,
            'phone' => $registration_request->phone,
            'email' => $registration_request->email,
            'address' => $registration_request->address,
            'birth_date' => $registration_request->birth_date,
            'gender' => $registration_request->gender,
            'notes' => $registration_request->notes,
            'form_data' => $registration_request->form_data,
            'status' => 'active',
        ]);

        $registration_request->update([
            'status' => 'approved',
            'beneficiary_id' => $beneficiary->id,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('wesal.beneficiaries.show', ['section' => 'registration-requests'])
            ->with('success', 'تم اعتماد الطلب وإنشاء المستفيد برقم: '.$beneficiaryNo);
    }

    public function reject(Request $request, RegistrationRequest $registration_request)
    {
        if ($registration_request->status !== 'pending') {
            return redirect()->back()->with('error', 'تمت معالجة هذا الطلب مسبقاً.');
        }

        $registration_request->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->route('wesal.beneficiaries.show', ['section' => 'registration-requests'])
            ->with('success', 'تم رفض الطلب.');
    }
}
