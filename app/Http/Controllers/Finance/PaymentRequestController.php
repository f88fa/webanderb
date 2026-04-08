<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\PaymentRequest;
use App\Models\PaymentRequestApproval;
use App\Models\PaymentRequestAttachment;
use App\Models\AccountingPeriod;
use App\Models\FiscalYear;
use App\Models\HR\RequestApprovalSequence;
use Illuminate\Http\Request;

class PaymentRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentRequest::with('creator', 'period', 'journalEntry', 'approvedByUser', 'beneficiaryBeneficiary', 'beneficiaryServices.beneficiary')
            ->orderBy('request_date', 'desc')
            ->orderBy('id', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('fiscal_year_id')) {
            $query->whereHas('period', fn ($q) => $q->where('fiscal_year_id', $request->fiscal_year_id));
        }

        $requests = $query->paginate(20);
        $fiscalYears = FiscalYear::orderBy('start_date', 'desc')->get(['id', 'year_name', 'start_date', 'end_date', 'status']);

        return view('wesal.index', [
            'page' => 'finance',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'payment-requests-index',
            'paymentRequests' => $requests,
            'fiscalYears' => $fiscalYears,
        ]);
    }

    public function create()
    {
        $yearOptions = $this->getOpenFiscalYearOptions();
        $nextNo = PaymentRequest::generateRequestNo();
        $employees = \App\Models\HR\Employee::where('status', 'active')
            ->orderBy('name_ar')
            ->get(['id', 'employee_no', 'name_ar', 'name_en']);

        return view('wesal.index', [
            'page' => 'finance',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'payment-request-form',
            'yearOptions' => $yearOptions,
            'nextRequestNo' => $nextNo,
            'employees' => $employees,
        ]);
    }

    /**
     * قائمة السنوات المالية المفتوحة فقط (السنة فقط بدون الشهر)
     */
    private function getOpenFiscalYearOptions(): array
    {
        $fiscalYears = FiscalYear::orderBy('start_date', 'desc')->get();
        $options = [];
        foreach ($fiscalYears as $fy) {
            $firstOpen = $fy->periods()->where('allow_posting', true)->orderBy('start_date')->first();
            if ($firstOpen) {
                $options[] = (object)[
                    'fiscal_year_id' => $fy->id,
                    'year_name' => $fy->year_name,
                    'period_id' => $firstOpen->id,
                ];
            }
        }
        return $options;
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('finance.payment_requests.create')) {
            abort(403, 'ليس لديك صلاحية إنشاء طلب صرف.');
        }
        $rules = [
            'request_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'beneficiary_type' => ['required', 'in:employee,entity'],
            'description' => ['nullable', 'string', 'max:1000'],
            'period_id' => ['nullable', 'exists:accounting_periods,id'],
            'attachments.*' => ['nullable', 'file', 'max:10240'],
        ];
        if ($request->beneficiary_type === 'employee') {
            $rules['beneficiary_employee_id'] = ['required', 'exists:hr_employees,id'];
        } else {
            $rules['beneficiary'] = ['required', 'string', 'max:255'];
        }
        $validated = $request->validate($rules);

        $beneficiaryName = $request->beneficiary_type === 'employee'
            ? \App\Models\HR\Employee::find($request->beneficiary_employee_id)->name_ar
            : $request->beneficiary;

        $pr = PaymentRequest::create([
            'request_no' => PaymentRequest::generateRequestNo(),
            'request_date' => $request->request_date,
            'amount' => $request->amount,
            'beneficiary_type' => $request->beneficiary_type,
            'beneficiary_employee_id' => $request->beneficiary_type === 'employee' ? $request->beneficiary_employee_id : null,
            'beneficiary' => $beneficiaryName,
            'description' => $request->description,
            'period_id' => $request->period_id,
            'status' => PaymentRequest::STATUS_PENDING,
            'created_by' => auth()->id(),
        ]);

        if ($request->hasFile('attachments')) {
            $dir = 'payment_request_attachments/' . $pr->id;
            foreach ($request->file('attachments') as $file) {
                if (!$file->isValid()) {
                    continue;
                }
                $path = $file->store($dir, 'public');
                PaymentRequestAttachment::create([
                    'payment_request_id' => $pr->id,
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);
            }
        }

        return redirect()
            ->route('wesal.finance.payment-requests.index')
            ->with('success', 'تم تسجيل طلب الصرف بنجاح (رقم الطلب: ' . $pr->request_no . ')');
    }

    public function approve(PaymentRequest $paymentRequest)
    {
        if (!auth()->user()->can('finance.payment_requests.approve')) {
            abort(403, 'ليس لديك صلاحية اعتماد طلبات الصرف.');
        }
        if ($paymentRequest->status !== PaymentRequest::STATUS_PENDING) {
            return back()->withErrors(['error' => 'الطلب غير قيد الانتظار']);
        }

        $user = auth()->user();
        $approvalType = $paymentRequest->approval_type ?? 'financial';
        $sequence = RequestApprovalSequence::getForType($approvalType);
        $existingApprovals = $paymentRequest->approvals()->count();
        $currentStep = $existingApprovals + 1;

        if ($sequence->isEmpty()) {
            // لا يوجد تسلسل: موافقة واحدة كما سابقاً + تسجيلها للمخرج النهائي
            PaymentRequestApproval::create([
                'payment_request_id' => $paymentRequest->id,
                'step' => 1,
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
            $paymentRequest->update([
                'status' => PaymentRequest::STATUS_APPROVED,
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
            return back()->with('success', 'تم اعتماد طلب الصرف');
        }

        $stepConfig = $sequence->firstWhere('step', $currentStep);
        if (!$stepConfig) {
            return back()->withErrors(['error' => 'لا توجد خطوة موافقة متاحة لهذا الطلب.']);
        }
        if (!$stepConfig->isApprovedByUser($paymentRequest, $user)) {
            return back()->withErrors(['error' => 'أنت لست الموافق المعيّن للخطوة ' . $currentStep . ' في تسلسل الموافقات.']);
        }

        PaymentRequestApproval::create([
            'payment_request_id' => $paymentRequest->id,
            'step' => $currentStep,
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        $isLastStep = $currentStep >= $sequence->max('step');
        if ($isLastStep) {
            $paymentRequest->update([
                'status' => PaymentRequest::STATUS_APPROVED,
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
            return back()->with('success', 'تم اعتماد طلب الصرف (اكتمال جميع خطوات الموافقة).');
        }

        return back()->with('success', 'تم تسجيل موافقتك (الخطوة ' . $currentStep . '). بانتظار الموافقين التاليين.');
    }

    public function reject(Request $request, PaymentRequest $paymentRequest)
    {
        if ($paymentRequest->status !== PaymentRequest::STATUS_PENDING) {
            return back()->withErrors(['error' => 'الطلب غير قيد الانتظار']);
        }
        $paymentRequest->update([
            'status' => PaymentRequest::STATUS_REJECTED,
            'rejection_notes' => $request->get('rejection_notes'),
        ]);
        return back()->with('success', 'تم رفض طلب الصرف');
    }

    /** تقرير أسماء المستفيدين لطلب صرف (دعم جماعي) */
    public function showBeneficiariesReport(PaymentRequest $paymentRequest)
    {
        $paymentRequest->load(['beneficiaryServices.beneficiary', 'period']);
        return view('wesal.index', [
            'page' => 'finance',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'payment-request-beneficiaries-report',
            'paymentRequest' => $paymentRequest,
        ]);
    }
}
