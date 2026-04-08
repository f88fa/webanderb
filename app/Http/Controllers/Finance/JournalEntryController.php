<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\StoreJournalEntryRequest;
use App\Models\AccountingPeriod;
use App\Models\ChartAccount;
use App\Models\CostCenter;
use App\Models\FiscalYear;
use App\Models\JournalEntry;
use App\Services\Finance\ChartAccountBalanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JournalEntryController extends Controller
{
    protected $balanceService;

    public function __construct(ChartAccountBalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = JournalEntry::with('period', 'postedByUser', 'lines.account')
            ->orderBy('entry_date', 'desc')
            ->orderBy('id', 'desc');

        // الفلتر حسب السنة المالية (سنوات مثل 2024، 2025، 2026) وليس شهر/فترة
        $fiscalYearId = $request->has('fiscal_year_id') && strlen((string)$request->get('fiscal_year_id')) > 0
            ? $request->get('fiscal_year_id')
            : (\App\Models\FiscalYear::getCurrent()?->id);
        if ($fiscalYearId) {
            $query->whereHas('period', function ($q) use ($fiscalYearId) {
                $q->where('fiscal_year_id', $fiscalYearId);
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('entry_type')) {
            $query->where('entry_type', $request->entry_type);
        }

        // فلترة تاريخية: من تاريخ — إلى تاريخ
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        if ($fromDate) {
            $query->where('entry_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->where('entry_date', '<=', $toDate);
        }

        $entries = $query->paginate(20);
        $fiscalYears = \App\Models\FiscalYear::orderBy('start_date', 'desc')->get();
        $defaultFiscalYearId = $fiscalYearId;

        return view('wesal.index', [
            'page' => 'finance',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'journal-entries',
            'entries' => $entries,
            'fiscalYears' => $fiscalYears,
            'defaultFiscalYearId' => $defaultFiscalYearId,
            'currentEntryType' => $request->get('entry_type'),
            'filterFromDate' => $fromDate,
            'filterToDate' => $toDate,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $fiscalYearId = $request->get('fiscal_year_id');
        $entryType = $request->get('entry_type', 'manual');
        
        // التحقق من وجود fiscal_year_id (إلزامي)
        if (!$fiscalYearId) {
            return redirect()->route('wesal.finance.journal-entries.select-period')
                ->with('error', 'يجب اختيار السنة المالية أولاً');
        }
        
        $fiscalYear = \App\Models\FiscalYear::find($fiscalYearId);
        
        if (!$fiscalYear) {
            return redirect()->route('wesal.finance.journal-entries.select-period')
                ->with('error', 'السنة المالية المحددة غير موجودة');
        }
        
        // جلب جميع الحسابات القابلة للترحيل لظهورها في قائمة اختيار الحساب
        $accounts = ChartAccount::active()
            ->postable()
            ->orderBy('code')
            ->get();
        
        // جلب مراكز التكلفة النشطة
        $costCenters = CostCenter::active()->orderBy('code')->get();
        
        // الحصول على رقم القيد التالي
        $nextEntryNo = JournalEntry::generateEntryNo('JE');

        // استخدام layout النظام الرئيسي
        return view('wesal.index', [
            'page' => 'finance',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'fiscalYear' => $fiscalYear,
            'entryType' => $entryType,
            'accounts' => $accounts,
            'costCenters' => $costCenters,
            'nextEntryNo' => $nextEntryNo,
            'formType' => 'journal-entry-create',
        ]);
    }
    
    /**
     * Show the fiscal year selection page before creating journal entry
     */
    public function selectPeriod(Request $request)
    {
        $allYears = \App\Models\FiscalYear::orderBy('start_date', 'desc')->get();
        $currentFiscalYear = \App\Models\FiscalYear::getCurrent();
        // ترتيب القائمة: السنة الحالية أولاً ثم الباقي
        $fiscalYears = $allYears->sortByDesc(function ($y) use ($currentFiscalYear) {
            return $y->id === ($currentFiscalYear?->id ?? null) ? 1 : 0;
        })->values();
        
        return view('wesal.index', [
            'page' => 'finance',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'fiscalYears' => $fiscalYears,
            'currentFiscalYear' => $currentFiscalYear,
            'formType' => 'journal-entry-select-period',
        ]);
    }

    /**
     * قائمة السنوات المالية المفتوحة فقط للاختيار (السنة فقط بدون الشهر — كل سنة → أول فترة مفتوحة فيها)
     */
    private function getFiscalYearOptions(): array
    {
        $fiscalYears = FiscalYear::orderBy('start_date', 'desc')->get();
        $options = [];
        foreach ($fiscalYears as $fy) {
            $firstOpenPeriod = $fy->periods()->open()->orderBy('start_date')->first();
            if ($firstOpenPeriod) {
                $options[] = (object)[
                    'fiscal_year_id' => $fy->id,
                    'year_name' => $fy->year_name,
                    'period_id' => $firstOpenPeriod->id,
                ];
            }
        }
        return $options;
    }

    /**
     * Show the form for creating a receipt voucher.
     */
    public function createReceiptVoucher(Request $request)
    {
        $periodId = $request->get('period_id');
        $yearOptions = $this->getFiscalYearOptions();
        $periods = AccountingPeriod::open()->orderBy('start_date', 'desc')->get();
        // جلب الحسابات من المستوى السادس فما فوق فقط
        $accounts = ChartAccount::active()
            ->postable()
            ->where('level', '>=', 5)
            ->orderBy('code')
            ->get();
        // للحساب المستقبل (البنك) في نموذج الجمعيات: من المستوى الثالث فما فوق
        $accountsFromLevel3 = ChartAccount::active()
            ->postable()
            ->where('level', '>=', 3)
            ->orderBy('code')
            ->get();
        // افتراضياً: الفترة الحالية
        $selectedPeriod = $periodId ? AccountingPeriod::find($periodId) : (AccountingPeriod::getCurrent() ?? $periods->first());
        $settings = \App\Models\SiteSetting::getAllAsArray();
        $organizationType = $settings['organization_type'] ?? '';
        $isNonProfitReceipt = ($organizationType === 'non_profit');

        return view('wesal.index', [
            'page' => 'finance',
            'settings' => $settings,
            'yearOptions' => $yearOptions,
            'selectedPeriod' => $selectedPeriod,
            'accounts' => $accounts,
            'accountsFromLevel3' => $accountsFromLevel3,
            'formType' => 'receipt-voucher-create',
            'organizationType' => $organizationType,
            'isNonProfitReceipt' => $isNonProfitReceipt,
        ]);
    }

    /**
     * Show the form for creating a payment voucher.
     */
    public function createPaymentVoucher(Request $request)
    {
        $periodId = $request->get('period_id');
        $paymentRequestId = $request->get('payment_request_id');
        $paymentRequest = $paymentRequestId ? \App\Models\PaymentRequest::with('beneficiaryBeneficiary')->find($paymentRequestId) : null;

        $yearOptions = $this->getFiscalYearOptions();
        $periods = AccountingPeriod::open()->orderBy('start_date', 'desc')->get();
        $accounts = ChartAccount::active()
            ->postable()
            ->where('level', '>=', 5)
            ->orderBy('code')
            ->get();

        $selectedPeriod = $periodId ? AccountingPeriod::find($periodId) : null;
        if (!$selectedPeriod && $paymentRequest?->period_id) {
            $selectedPeriod = AccountingPeriod::find($paymentRequest->period_id);
        }
        if (!$selectedPeriod) {
            $selectedPeriod = AccountingPeriod::getCurrent() ?? $periods->first();
        }
        if (!$selectedPeriod) {
            $selectedPeriod = AccountingPeriod::orderBy('start_date', 'desc')->first();
        }

        return view('wesal.index', [
            'page' => 'finance',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'yearOptions' => $yearOptions,
            'selectedPeriod' => $selectedPeriod,
            'accounts' => $accounts,
            'formType' => 'payment-voucher-create',
            'paymentRequest' => $paymentRequest,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJournalEntryRequest $request)
    {
        if (!auth()->user()->can('finance.journal.create')) {
            abort(403, 'ليس لديك صلاحية إنشاء قيد.');
        }
        DB::beginTransaction();
        try {
            // تحديد الفترة حسب التاريخ (يتم تلقائياً من تاريخ القيد)
            $period = AccountingPeriod::findByDate($request->entry_date);
            
            if (!$period) {
                return back()->withErrors(['entry_date' => 'لا توجد فترة محاسبية لهذا التاريخ'])->withInput();
            }
            
            // التحقق من أن الفترة تنتمي إلى السنة المالية المحددة
            $fiscalYearId = $request->get('fiscal_year_id');
            if ($fiscalYearId && $period->fiscal_year_id != $fiscalYearId) {
                return back()->withErrors(['entry_date' => 'تاريخ القيد لا ينتمي إلى السنة المالية المحددة'])->withInput();
            }

            // التحقق من إمكانية الترحيل
            if ($request->entry_type === 'adjusting') {
                if (!$period->canPostAdjustments()) {
                    return back()->withErrors(['error' => 'الفترة لا تسمح بترحيل قيود التسوية'])->withInput();
                }
            } else {
                if (!$period->canPost()) {
                    return back()->withErrors(['error' => 'الفترة مغلقة أو لا تسمح بالترحيل'])->withInput();
                }
            }

            // توليد رقم القيد حسب النوع (يدعم التبرعات والمنح - القطاع غير الربحي)
            $entryNoPrefix = 'JE';
            if ($request->entry_type === 'receipt') {
                $entryNoPrefix = 'RC'; // Receipt
            } elseif ($request->entry_type === 'payment') {
                $entryNoPrefix = 'PY'; // Payment
            } elseif ($request->entry_type === 'donation') {
                $entryNoPrefix = 'DN'; // Donation
            } elseif ($request->entry_type === 'grant') {
                $entryNoPrefix = 'GR'; // Grant
            }
            
            // جمع معلومات المستلم/المستفيد في notes
            $notes = $request->notes ?? '';
            if ($request->has('recipient_name')) {
                $voucherInfo = "معلومات المستلم/المستفيد:\n";
                $voucherInfo .= "الاسم: " . ($request->recipient_name ?? '') . "\n";
                if ($request->recipient_id) {
                    $voucherInfo .= "رقم الهوية/السجل: " . $request->recipient_id . "\n";
                }
                if ($request->recipient_phone) {
                    $voucherInfo .= "الهاتف: " . $request->recipient_phone . "\n";
                }
                if ($request->recipient_address) {
                    $voucherInfo .= "العنوان: " . $request->recipient_address . "\n";
                }
                // طريقة الاستلام/الدفع وبيانات التحويل أو الشيك
                if (in_array($request->entry_type, ['receipt', 'payment'], true) && $request->filled('payment_method')) {
                    $methodLabel = [
                        'transfer' => 'تحويل',
                        'cheque' => 'شيك',
                        'cash' => 'نقدي',
                    ][$request->payment_method] ?? $request->payment_method;
                    $voucherInfo .= "\nطريقة " . ($request->entry_type === 'receipt' ? 'الاستلام' : 'الدفع') . ": " . $methodLabel . "\n";
                    if ($request->payment_method === 'cheque') {
                        if ($request->filled('cheque_no')) {
                            $voucherInfo .= "رقم الشيك: " . $request->cheque_no . "\n";
                        }
                        if ($request->filled('cheque_bank_name')) {
                            $voucherInfo .= "البنك المصدر: " . $request->cheque_bank_name . "\n";
                        }
                    }
                }
                if ($notes) {
                    $voucherInfo .= "\nملاحظات إضافية:\n" . $notes;
                }
                $notes = $voucherInfo;
            } elseif (in_array($request->entry_type, ['receipt', 'payment'], true) && $request->filled('payment_method')) {
                // سند قبض/صرف بدون مستلم: إلحاق طريقة الاستلام/الدفع وبيانات التحويل أو الشيك بالملاحظات
                $methodLabel = [
                    'transfer' => 'تحويل',
                    'cheque' => 'شيك',
                    'cash' => 'نقدي',
                ][$request->payment_method] ?? $request->payment_method;
                $paymentBlock = "طريقة " . ($request->entry_type === 'receipt' ? 'الاستلام' : 'الدفع') . ": " . $methodLabel . "\n";
                if ($request->payment_method === 'cheque') {
                    if ($request->filled('cheque_no')) {
                        $paymentBlock .= "رقم الشيك: " . $request->cheque_no . "\n";
                    }
                    if ($request->filled('cheque_bank_name')) {
                        $paymentBlock .= "البنك المصدر: " . $request->cheque_bank_name . "\n";
                    }
                }
                $notes = $paymentBlock . ($notes ? "\n" . $notes : '');
            }

            // إنشاء القيد (تسجيل المحاسب = المستخدم الحالي مباشرة)
            $entryData = [
                'entry_no' => JournalEntry::generateEntryNo($entryNoPrefix),
                'entry_date' => $request->entry_date,
                'description' => $request->description,
                'entry_type' => $request->entry_type,
                'period_id' => $period->id,
                'status' => 'draft',
                'notes' => $notes,
                'posted_by' => auth()->id(),
            ];
            if (in_array($request->entry_type, ['receipt', 'payment']) && $request->filled('cash_account_id')) {
                $entryData['cash_account_id'] = $request->cash_account_id;
            }
            $entry = JournalEntry::create($entryData);

            // إضافة السطور
            $totalDebit = 0;
            $totalCredit = 0;
            foreach ($request->lines as $index => $line) {
                $debit = (float) ($line['debit'] ?? 0);
                $credit = (float) ($line['credit'] ?? 0);
                
                // التحقق من أن الحساب قابل للترحيل
                $account = ChartAccount::findOrFail($line['account_id']);
                if (!$account->is_postable) {
                    throw new \Exception("الحساب {$account->name_ar} غير قابل للترحيل");
                }

                $entry->lines()->create([
                    'account_id' => $line['account_id'],
                    'cost_center_id' => $line['cost_center_id'] ?? null,
                    'debit' => $debit,
                    'credit' => $credit,
                    'description' => $line['description'] ?? null,
                    'reference' => $line['reference'] ?? null,
                    'line_order' => $index + 1,
                ]);

                $totalDebit += $debit;
                $totalCredit += $credit;
            }

            // تحديث المجاميع
            $entry->update([
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
            ]);

            // ترحيل القيد إذا كان مطلوباً
            if ($request->has('post_now') && $request->post_now) {
                if (!$entry->post()) {
                    throw new \Exception('فشل ترحيل القيد');
                }
                
                // إلغاء cache للأرصدة
                $accountIds = collect($request->lines)->pluck('account_id')->unique();
                foreach ($accountIds as $accountId) {
                    $this->balanceService->clearBalanceCache($accountId, $period->id);
                }
            }

            DB::commit();

            \App\Models\AuditLog::log('create_journal_entry', $entry, null, $entry->toArray());

            // ربط طلب الصرف بسند الصرف عند التنفيذ من طلب معتمد
            $paymentRequestId = $request->get('payment_request_id');
            if ($paymentRequestId && $request->entry_type === 'payment') {
                $pr = \App\Models\PaymentRequest::find($paymentRequestId);
                if ($pr && $pr->status === \App\Models\PaymentRequest::STATUS_APPROVED) {
                    $pr->update([
                        'journal_entry_id' => $entry->id,
                        'status' => \App\Models\PaymentRequest::STATUS_PAID,
                    ]);
                }
            }

            // إذا كان سند قبض أو صرف، التوجيه إلى صفحة الطباعة ثم إمكانية العودة لقائمة السندات
            if (in_array($request->entry_type, ['receipt', 'payment'])) {
                $printUrl = route('wesal.finance.journal-entries.print', $entry);
                $listUrl = route('wesal.finance.journal-entries.index', [
                    'entry_type' => $request->entry_type,
                    'fiscal_year_id' => $period->fiscal_year_id,
                ]);
                return redirect($printUrl)
                    ->with('success', 'تم إنشاء السند بنجاح. يمكنك طباعته أو العودة إلى <a href="' . $listUrl . '">قائمة سندات ' . ($request->entry_type === 'payment' ? 'الصرف' : 'القبض') . '</a>.');
            }

            return redirect()->route('wesal.finance.journal-entries.show', $entry)
                ->with('success', 'تم إنشاء القيد بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->entry_type === 'payment' && $request->filled('payment_request_id')) {
                return redirect()->route('wesal.finance.payment-voucher.create', ['payment_request_id' => $request->payment_request_id])
                    ->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()])->withInput();
            }
            return back()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(JournalEntry $journalEntry)
    {
        $journalEntry->load('period.fiscalYear', 'postedByUser', 'lines.account', 'lines.costCenter');
        
        return view('wesal.index', [
            'page' => 'finance',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'journal-entry-show',
            'journalEntry' => $journalEntry,
        ]);
    }

    /**
     * Post the journal entry.
     */
    public function post(JournalEntry $journalEntry)
    {
        if ($journalEntry->status !== 'draft') {
            return back()->withErrors(['error' => 'يمكن ترحيل القيود المسودة فقط']);
        }

        if (!$journalEntry->post()) {
            return back()->withErrors(['error' => 'فشل ترحيل القيد. تأكد من الاتزان والصلاحيات']);
        }

        // إلغاء cache للأرصدة
        $accountIds = $journalEntry->lines()->pluck('account_id')->unique();
        foreach ($accountIds as $accountId) {
            $this->balanceService->clearBalanceCache($accountId, $journalEntry->period_id);
        }

        \App\Models\AuditLog::log('post_journal_entry', $journalEntry, ['status' => 'draft'], ['status' => 'posted']);

        return back()->with('success', 'تم ترحيل القيد بنجاح');
    }

    /**
     * Reverse the journal entry.
     */
    public function reverse(Request $request, JournalEntry $journalEntry)
    {
        if ($journalEntry->status !== 'posted') {
            return back()->withErrors(['error' => 'يمكن عكس القيود المرحلة فقط']);
        }

        if (!$journalEntry->reverse($request->notes)) {
            return back()->withErrors(['error' => 'فشل عكس القيد']);
        }

        \App\Models\AuditLog::log('reverse_journal_entry', $journalEntry, ['status' => 'posted'], ['status' => 'reversed']);

        return back()->with('success', 'تم عكس القيد بنجاح');
    }

    /**
     * Show print view for journal entry (voucher)
     */
    public function print(JournalEntry $journalEntry)
    {
        $journalEntry->load([
            'period.fiscalYear',
            'lines' => fn ($q) => $q->orderBy('line_order'),
            'lines.account',
            'lines.costCenter',
            'postedByUser',
            'paymentRequest',
        ]);

        return view('wesal.pages.finance.voucher-print', [
            'entry' => $journalEntry,
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
        ]);
    }
}
