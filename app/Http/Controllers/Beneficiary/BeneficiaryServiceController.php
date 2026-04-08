<?php

namespace App\Http\Controllers\Beneficiary;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary\Beneficiary;
use App\Models\Beneficiary\BeneficiaryService;
use App\Models\Beneficiary\Program;
use App\Models\Beneficiary\ServiceType;
use App\Models\PaymentRequest;
use App\Models\FiscalYear;
use Illuminate\Http\Request;

class BeneficiaryServiceController extends Controller
{
    public function store(Request $request)
    {
        $isGroup = $request->has('beneficiary_ids') && is_array($request->beneficiary_ids) && count(array_filter($request->beneficiary_ids)) > 0;

        if ($isGroup) {
            return $this->storeGroup($request);
        }

        $request->validate([
            'beneficiary_id' => 'required|exists:ben_beneficiaries,id',
            'service_type_id' => 'required|exists:ben_service_types,id',
            'program_id' => 'nullable|exists:ben_programs,id',
            'service_date' => 'required|date',
            'amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $beneficiary = Beneficiary::findOrFail($request->beneficiary_id);
        $serviceType = ServiceType::findOrFail($request->service_type_id);
        $amount = (float) ($request->amount ?? 0);
        $isFinancial = $serviceType->is_financial && $amount > 0;

        $data = [
            'beneficiary_id' => $beneficiary->id,
            'service_type_id' => $serviceType->id,
            'request_id' => $request->request_id,
            'program_id' => $request->program_id,
            'service_date' => $request->service_date,
            'amount' => $amount ?: null,
            'notes' => $request->notes,
            'status' => $isFinancial ? BeneficiaryService::STATUS_APPROVED : BeneficiaryService::STATUS_EXECUTED,
            'executed_at' => $isFinancial ? null : now(),
            'executed_by' => $isFinancial ? null : auth()->id(),
        ];

        $paymentRequest = null;
        if ($isFinancial) {
            $period = $this->getFirstOpenPeriod();
            $displayName = $beneficiary->displayNameForPortal();
            $paymentRequest = PaymentRequest::create([
                'request_no' => PaymentRequest::generateRequestNo(),
                'request_date' => $request->service_date,
                'amount' => $amount,
                'beneficiary_type' => PaymentRequest::BENEFICIARY_ENTITY,
                'beneficiary' => $displayName,
                'beneficiary_id' => $beneficiary->id,
                'description' => 'دعم مالي لمستفيد: ' . $displayName . ' - ' . ($serviceType->name_ar ?? '') . ($request->notes ? ' | ' . $request->notes : ''),
                'status' => PaymentRequest::STATUS_PENDING,
                'approval_type' => 'beneficiary_support',
                'period_id' => $period?->id,
                'created_by' => auth()->id(),
            ]);
            $data['payment_request_id'] = $paymentRequest->id;
        }

        BeneficiaryService::create($data);

        $msg = $isFinancial
            ? 'تم تسجيل الدعم المالي وإنشاء طلب صرف رقم ' . ($paymentRequest->request_no ?? '') . ' بانتظار الاعتماد من المالية.'
            : 'تم تسجيل الخدمة.';
        return redirect()->route('wesal.beneficiaries.show', ['section' => 'services'])->with('success', $msg);
    }

    /**
     * دعم جماعي: مجموعة مستفيدين أو برنامج — طلب صرف واحد يتضمن أسماء المستفيدين.
     */
    private function storeGroup(Request $request)
    {
        $request->validate([
            'beneficiary_ids' => 'required|array',
            'beneficiary_ids.*' => 'exists:ben_beneficiaries,id',
            'service_type_id' => 'required|exists:ben_service_types,id',
            'program_id' => 'nullable|exists:ben_programs,id',
            'service_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $ids = array_values(array_filter(array_unique($request->beneficiary_ids)));
        if (empty($ids)) {
            return redirect()->route('wesal.beneficiaries.show', ['section' => 'services'])
                ->with('error', 'يجب اختيار مستفيد واحد على الأقل.');
        }

        $beneficiaries = Beneficiary::whereIn('id', $ids)->orderBy('beneficiary_no')->get();
        if ($beneficiaries->isEmpty()) {
            return redirect()->route('wesal.beneficiaries.show', ['section' => 'services'])
                ->with('error', 'لم يتم العثور على المستفيدين المحددين.');
        }

        $serviceType = ServiceType::findOrFail($request->service_type_id);
        $amountPerPerson = (float) $request->amount;
        $isFinancial = $serviceType->is_financial && $amountPerPerson > 0;
        $count = $beneficiaries->count();
        $totalAmount = $amountPerPerson * $count;

        $programName = null;
        if ($request->program_id) {
            $program = Program::find($request->program_id);
            $programName = $program?->name_ar;
        }

        $paymentRequest = null;
        if ($isFinancial) {
            $period = $this->getFirstOpenPeriod();
            $namesList = $beneficiaries->map(fn ($b) => $b->displayNameForPortal() . ' (' . $b->beneficiary_no . ')')->join('، ');
            $beneficiaryLabel = 'مجموعة مستفيدين (' . $count . ' أشخاص)';
            if ($programName) {
                $beneficiaryLabel .= ' - برنامج: ' . $programName;
            }
            $description = 'دعم جماعي - ' . ($serviceType->name_ar ?? '') . "\n";
            $description .= 'عدد المستفيدين: ' . $count . ' | مبلغ للفرد: ' . number_format($amountPerPerson, 2) . ' | الإجمالي: ' . number_format($totalAmount, 2) . "\n";
            $description .= 'أسماء المستفيدين: ' . $namesList;
            if ($request->notes) {
                $description .= "\nملاحظات: " . $request->notes;
            }

            $paymentRequest = PaymentRequest::create([
                'request_no' => PaymentRequest::generateRequestNo(),
                'request_date' => $request->service_date,
                'amount' => $totalAmount,
                'beneficiary_type' => PaymentRequest::BENEFICIARY_ENTITY,
                'beneficiary' => $beneficiaryLabel,
                'beneficiary_id' => null,
                'description' => $description,
                'status' => PaymentRequest::STATUS_PENDING,
                'approval_type' => 'beneficiary_support',
                'period_id' => $period?->id,
                'created_by' => auth()->id(),
            ]);
        }

        $status = $isFinancial ? BeneficiaryService::STATUS_APPROVED : BeneficiaryService::STATUS_EXECUTED;
        $executedAt = $isFinancial ? null : now();
        $executedBy = $isFinancial ? null : auth()->id();

        foreach ($beneficiaries as $beneficiary) {
            BeneficiaryService::create([
                'beneficiary_id' => $beneficiary->id,
                'service_type_id' => $serviceType->id,
                'request_id' => $request->request_id,
                'program_id' => $request->program_id,
                'service_date' => $request->service_date,
                'amount' => $amountPerPerson,
                'notes' => $request->notes,
                'status' => $status,
                'payment_request_id' => $paymentRequest?->id,
                'executed_at' => $executedAt,
                'executed_by' => $executedBy,
            ]);
        }

        $msg = 'تم تسجيل الدعم الجماعي لـ ' . $count . ' مستفيد.';
        if ($isFinancial && $paymentRequest) {
            $msg .= ' تم إنشاء طلب صرف واحد رقم ' . $paymentRequest->request_no . ' (إجمالي ' . number_format($totalAmount, 2) . ') بانتظار الاعتماد من المالية.';
        }
        return redirect()->route('wesal.beneficiaries.show', ['section' => 'services'])->with('success', $msg);
    }

    private function getFirstOpenPeriod(): ?\App\Models\AccountingPeriod
    {
        $fy = FiscalYear::orderBy('start_date', 'desc')->first();
        if (!$fy) {
            return null;
        }
        return $fy->periods()->where('allow_posting', true)->orderBy('start_date')->first();
    }
}
