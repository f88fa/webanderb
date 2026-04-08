<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary\Beneficiary;
use App\Models\Beneficiary\BeneficiaryRequest;
use App\Models\Beneficiary\Program;
use App\Models\Beneficiary\RegistrationRequest;
use App\Models\Beneficiary\ServiceType;
use Illuminate\Http\Request;

/**
 * قسم المستفيدين — يعيد عرض wesal.index مع page=beneficiaries و formType حسب المسار ويحمّل البيانات
 */
class BeneficiariesController extends Controller
{
    public function show(Request $request, ?string $section = null, ?string $sub = null)
    {
        $formType = $section
            ? ($sub && $section !== 'edit' && $section !== 'profile' ? "ben-{$section}-{$sub}" : "ben-{$section}")
            : 'ben';

        $data = [
            'page' => 'beneficiaries',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => $formType,
            'benSection' => $section,
            'benSub' => $sub,
        ];

        $this->loadSectionData($request, $section, $sub, $data);

        return view('wesal.index', $data);
    }

    private function loadSectionData(Request $request, ?string $section, ?string $sub, array &$data): void
    {
        if (in_array($section, ['list', 'create', 'archive', 'services', 'medical', 'assessment', 'documents', 'programs', 'profile', 'reports'], true) || ($section === 'requests' && $sub)) {
            $data['beneficiaries'] = Beneficiary::active()->orderBy('beneficiary_no')->get();
        }
        if ($section === 'profile' && $sub && is_numeric($sub)) {
            $data['profileBeneficiary'] = Beneficiary::with([
                'serviceRecords' => fn ($q) => $q->with(['serviceType', 'paymentRequest', 'program'])->orderByDesc('service_date'),
                'documents',
                'programEnrollments.program',
                'beneficiaryForm' => fn ($q) => $q->with(['fields' => fn ($q2) => $q2->orderBy('sort_order')]),
            ])->findOrFail((int) $sub);
            return;
        }
        if ($section === 'create') {
            $addFormId = \App\Models\Beneficiary\BeneficiaryForm::getAddFormId();
            if ($addFormId) {
                $data['addForm'] = \App\Models\Beneficiary\BeneficiaryForm::with(['fields' => fn ($q) => $q->orderBy('sort_order')->with('dependsOnField')])->find($addFormId);
            }
            return;
        }
        if ($section === 'list') {
            $listQuery = Beneficiary::active()->withCount('requests');
            $search = trim((string) $request->input('q', ''));
            if ($search !== '') {
                $like = '%'.addcslashes($search, '%_\\').'%';
                $listQuery->where(function ($w) use ($like) {
                    $w->where('beneficiary_no', 'like', $like)
                        ->orWhere('name_ar', 'like', $like)
                        ->orWhere('name_en', 'like', $like)
                        ->orWhere('national_id', 'like', $like)
                        ->orWhere('phone', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('address', 'like', $like);
                });
            }
            $data['beneficiariesList'] = $listQuery->orderBy('beneficiary_no')->get();
            $data['beneficiariesListQuery'] = $search;

            return;
        }
        if ($section === 'registration-requests') {
            $data['registrationRequests'] = RegistrationRequest::pending()->with('user')->orderByDesc('created_at')->get();
            return;
        }
        if ($section === 'edit' && $sub && is_numeric($sub)) {
            $data['editBeneficiary'] = Beneficiary::with([
                'beneficiaryForm' => fn ($q) => $q->with(['fields' => fn ($q2) => $q2->orderBy('sort_order')->with('dependsOnField')]),
            ])->findOrFail((int) $sub);

            return;
        }
        if ($section === 'archive') {
            $data['archivedBeneficiaries'] = Beneficiary::archived()->orderBy('beneficiary_no')->get();
            return;
        }
        if ($section === 'requests') {
            $status = match ($sub) {
                'new' => 'new',
                'under-study' => 'under_study',
                'approved' => 'approved',
                'rejected' => 'rejected',
                default => 'new',
            };
            $data['requests'] = BeneficiaryRequest::where('status', $status)->with('beneficiary')->orderByDesc('created_at')->get();
            return;
        }
        if ($section === 'services') {
            $data['serviceTypes'] = ServiceType::orderBy('order')->get();
            $data['serviceRecords'] = \App\Models\Beneficiary\BeneficiaryService::with(['beneficiary', 'serviceType', 'paymentRequest'])->orderByDesc('service_date')->paginate(15);
            $data['programs'] = Program::orderBy('name_ar')->get();
            $data['programBeneficiaryIds'] = \App\Models\Beneficiary\BeneficiaryProgram::select('program_id', 'beneficiary_id')
                ->get()
                ->groupBy('program_id')
                ->map(fn ($g) => $g->pluck('beneficiary_id')->values()->toArray())
                ->toArray();
            return;
        }
        if ($section === 'medical') {
            $data['medicalRecords'] = \App\Models\Beneficiary\MedicalRecord::with('beneficiary')->orderByDesc('record_date')->paginate(15);
            return;
        }
        if ($section === 'assessment') {
            $data['assessments'] = \App\Models\Beneficiary\Assessment::with('beneficiary')->orderByDesc('assessment_date')->paginate(15);
            return;
        }
        if ($section === 'documents') {
            $data['documents'] = \App\Models\Beneficiary\BeneficiaryDocument::with('beneficiary')->orderByDesc('document_date')->paginate(15);
            return;
        }
        if ($section === 'programs') {
            $data['programs'] = Program::orderBy('name_ar')->get();
            return;
        }
        if ($section === 'reports') {
            $data['beneficiariesForReport'] = Beneficiary::active()
                ->withSum(['serviceRecords as total_support_amount' => fn ($q) => $q->whereNotNull('amount')], 'amount')
                ->withCount('serviceRecords')
                ->withCount(['serviceRecords as financial_services_count' => fn ($q) => $q->whereNotNull('payment_request_id')])
                ->orderBy('beneficiary_no')
                ->get();
            return;
        }
    }
}
