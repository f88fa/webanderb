<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\StoreChartAccountRequest;
use App\Models\ChartAccount;
use App\Models\AccountingPeriod;
use App\Services\Finance\ChartAccountBalanceService;
use App\Services\Finance\ExcelExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChartAccountController extends Controller
{
    protected $balanceService;

    protected $excelExport;

    public function __construct(ChartAccountBalanceService $balanceService, ExcelExportService $excelExport)
    {
        $this->balanceService = $balanceService;
        $this->excelExport = $excelExport;
    }

    /**
     * Display a listing of the resource (Tree view).
     */
    public function index(Request $request)
    {
        $periodId = $request->get('period_id');
        $asOf = $request->get('as_of') ? Carbon::parse($request->get('as_of')) : null;
        $selectedAccountId = $request->get('account_id');
        
        // جلب الفترات للفلترة
        $periods = AccountingPeriod::with('fiscalYear')->orderBy('start_date', 'desc')->get();
        
        // الحساب المحدد (أول حساب رئيسي أو المحدد)
        $selectedAccount = null;
        if ($selectedAccountId) {
            $selectedAccount = ChartAccount::with('parent')->find($selectedAccountId);
        } else {
            // جلب أول حساب رئيسي
            $selectedAccount = ChartAccount::whereNull('parent_id')->orderBy('code')->first();
        }
        
        // حساب رصيد الحساب المحدد
        $selectedAccountBalance = null;
        if ($selectedAccount) {
            $selectedAccountBalance = $this->balanceService->calculateBalanceWithRollup(
                $selectedAccount->id,
                $periodId,
                $asOf
            );
        }

        // إرجاع view مع البيانات
        $viewData = [
            'selectedAccount' => $selectedAccount,
            'selectedAccountBalance' => $selectedAccountBalance,
            'periods' => $periods,
            'periodId' => $periodId,
            'asOf' => $asOf,
            'page' => 'finance',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
        ];
        
        // استخدام layout wesal مع محتوى finance
        return view('wesal.index', $viewData);
    }

    /**
     * Get tree with balances (JSON endpoint)
     */
    public function tree(Request $request)
    {
        try {
            $periodId = $request->get('period_id');
            $asOf = $request->get('as_of') ? Carbon::parse($request->get('as_of')) : null;
            
            $tree = $this->balanceService->buildTreeWithBalances($periodId, $asOf);
            
            return response()->json([
                'success' => true,
                'data' => $tree,
                'count' => count($tree),
            ]);
        } catch (\Exception $e) {
            \Log::error('Tree API Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    /**
     * شجرة حسابات من مستوى معين (لاختيار الحساب - من المستوى الثالث)
     */
    public function treeFromLevel(Request $request)
    {
        try {
            $minLevel = (int) $request->get('min_level', 3);
            $minLevel = max(1, min(10, $minLevel));
            $tree = $this->balanceService->buildTreeFromLevel($minLevel);
            return response()->json([
                'success' => true,
                'data' => $tree,
                'count' => count($tree),
            ]);
        } catch (\Exception $e) {
            \Log::error('TreeFromLevel API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    /**
     * Get account details with balance
     */
    public function getAccountDetails(Request $request, $chartAccount)
    {
        $account = ChartAccount::findOrFail($chartAccount);
        $periodId = $request->get('period_id');
        $asOf = $request->get('as_of') ? Carbon::parse($request->get('as_of')) : null;
        
        $balance = $this->balanceService->calculateBalanceWithRollup(
            $account->id,
            $periodId,
            $asOf
        );
        
        return response()->json([
            'success' => true,
            'account' => [
                'id' => $account->id,
                'code' => $account->code,
                'name_ar' => $account->name_ar,
                'name_en' => $account->name_en,
                'level' => $account->level,
                'type' => $account->type,
                'nature' => $account->nature,
                'is_postable' => $account->is_postable,
                'is_fixed' => $account->is_fixed,
                'status' => $account->status,
                'description' => $account->description,
                'created_at' => $account->created_at?->format('Y-m-d H:i:s'),
                'parent' => $account->parent ? [
                    'id' => $account->parent->id,
                    'code' => $account->parent->code,
                    'name_ar' => $account->parent->name_ar,
                ] : null,
            ],
            'balance' => $balance,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $parentId = $request->get('parent_id');
        if (!$parentId) {
            return redirect()->route('wesal.finance.chart-accounts.index')
                ->withErrors(['error' => 'يجب اختيار حساب رئيسي']);
        }
        
        $parent = ChartAccount::findOrFail($parentId);
        
        // حساب التسلسل التالي تلقائياً
        $nextSequence = $this->getNextSequence($parent);
        
        // استخدام layout النظام الرئيسي
        return view('wesal.index', [
            'page' => 'finance',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'parent' => $parent,
            'nextSequence' => $nextSequence,
            'formType' => 'create',
        ]);
    }
    
    /**
     * حساب التسلسل التالي للحساب الفرعي
     */
    private function getNextSequence(ChartAccount $parent): string
    {
        // جلب جميع الحسابات الفرعية للحساب الأب
        $children = ChartAccount::where('parent_id', $parent->id)
            ->orderBy('code', 'desc')
            ->get();
        
        if ($children->isEmpty()) {
            return '01';
        }
        
        // استخراج آخر تسلسل من آخر حساب فرعي
        $lastChild = $children->first();
        $parentCodeLength = strlen($parent->code);
        $lastChildCode = $lastChild->code;
        
        // استخراج التسلسل (آخر رقمين من الكود)
        $lastSequence = substr($lastChildCode, $parentCodeLength);
        $nextSequenceNum = (int)$lastSequence + 1;
        
        // التأكد من عدم تجاوز 99
        if ($nextSequenceNum > 99) {
            throw new \Exception('تم الوصول إلى الحد الأقصى من الحسابات الفرعية (99)');
        }
        
        return str_pad($nextSequenceNum, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChartAccountRequest $request)
    {
        if (!auth()->user()->can('finance.chart_accounts.create')) {
            abort(403, 'ليس لديك صلاحية إنشاء حساب.');
        }
        DB::beginTransaction();
        try {
            $parent = $request->parent_id ? ChartAccount::findOrFail($request->parent_id) : null;
            
            // تحديد المستوى
            $level = $parent ? $parent->level + 1 : 1;
            
            // التحقق من أن الحساب الأب ليس قابل للترحيل
            if ($parent && $parent->is_postable) {
                // تحويل الحساب الأب إلى تجميعي
                $parent->is_postable = false;
                $parent->save();
            }

            // تحديد نوع الحساب وطبيعته من الحساب الأب
            $type = $request->type ?: ($parent ? $parent->type : 'asset');
            $nature = $request->nature ?: ($parent ? $parent->nature : 'debit');

            // بناء الكود تلقائياً من الحساب الأب + التسلسل التالي
            if (!$parent) {
                throw new \Exception('يجب اختيار حساب رئيسي');
            }
            
            // حساب التسلسل التالي تلقائياً
            $nextSequence = $this->getNextSequence($parent);
            $code = $parent->code . $nextSequence;

            // التحقق من عدم تكرار الكود
            if (ChartAccount::where('code', $code)->exists()) {
                throw new \Exception('كود الحساب موجود مسبقاً');
            }

            $account = ChartAccount::create([
                'code' => $code,
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'parent_id' => $request->parent_id,
                'level' => $level,
                'type' => $type,
                'nature' => $nature,
                'is_postable' => true, // الحسابات الجديدة قابلة للترحيل
                'is_fixed' => false, // الحسابات المضافة يدوياً ليست ثابتة
                'status' => $request->status ?? 'active',
                'description' => $request->description,
            ]);

            DB::commit();

            \App\Models\AuditLog::log('create_account', $account, null, $account->toArray());

            return redirect()->route('wesal.finance.chart-accounts.index')
                ->with('success', 'تم إضافة الحساب بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ChartAccount $chartAccount)
    {
        $chartAccount->load('parent', 'children', 'journalLines.journalEntry');
        
        return view('wesal.index', [
            'page' => 'finance',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'chart-account-show',
            'chartAccount' => $chartAccount,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ChartAccount $chartAccount)
    {
        if ($chartAccount->is_fixed) {
            return back()->withErrors(['error' => 'لا يمكن تعديل الحسابات الثابتة']);
        }

        $accounts = ChartAccount::active()
            ->where('id', '!=', $chartAccount->id)
            ->orderBy('code')
            ->get();
        
        // استخدام layout النظام الرئيسي
        return view('wesal.index', [
            'page' => 'finance',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'chartAccount' => $chartAccount,
            'accounts' => $accounts,
            'formType' => 'edit',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ChartAccount $chartAccount)
    {
        if (!auth()->user()->can('finance.chart_accounts.edit')) {
            abort(403, 'ليس لديك صلاحية تعديل الحسابات.');
        }
        if ($chartAccount->is_fixed) {
            return back()->withErrors(['error' => 'لا يمكن تعديل الحسابات الثابتة']);
        }

        $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'description' => ['nullable', 'string'],
        ]);

        $oldValues = $chartAccount->toArray();
        
        $chartAccount->update([
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'status' => $request->status,
            'description' => $request->description,
        ]);

        \App\Models\AuditLog::log('update_account', $chartAccount, $oldValues, $chartAccount->toArray());

        return redirect()->route('wesal.page', 'finance')
            ->with('success', 'تم تحديث الحساب بنجاح');
    }

    /**
     * Display trial balance report
     */
    public function trialBalance(Request $request)
    {
        $periodId = $request->get('period_id');
        $fiscalYearId = $request->get('fiscal_year_id');
        $asOf = $request->get('as_of') ? Carbon::parse($request->get('as_of')) : null;
        
        // جلب الفترات للفلترة (حسب السنة المالية إن وُجدت)
        $periodsQuery = AccountingPeriod::with('fiscalYear')->orderBy('start_date', 'desc');
        if ($fiscalYearId) {
            $periodsQuery->where('fiscal_year_id', $fiscalYearId);
        }
        $periods = $periodsQuery->get();
        // افتراضياً: الفترة الحالية أو أول فترة من السنة المحددة
        $currentPeriod = AccountingPeriod::getCurrent();
        $selectedPeriod = null;
        if ($periodId) {
            $selectedPeriod = AccountingPeriod::find($periodId);
        } elseif ($fiscalYearId && $periods->isNotEmpty()) {
            $selectedPeriod = $periods->first();
            $periodId = $selectedPeriod->id;
        } else {
            $selectedPeriod = $currentPeriod ?? $periods->first();
        }
        if ($selectedPeriod && !$periodId) {
            $periodId = $selectedPeriod->id;
        }
        
        // جلب جميع الحسابات القابلة للترحيل فقط
        $accounts = ChartAccount::active()
            ->postable()
            ->orderBy('code')
            ->get();
        
        $trialBalanceData = [];
        $totalDebit = 0;
        $totalCredit = 0;
        
        foreach ($accounts as $account) {
            $balanceData = $this->balanceService->calculateBalance(
                $account->id,
                $periodId,
                $asOf
            );
            
            $debitAmount = 0;
            $creditAmount = 0;
            
            // حسب طبيعة الحساب
            if ($account->nature === 'debit') {
                // الحسابات المدينة: الرصيد المدين = debit - credit
                if ($balanceData['raw_balance'] >= 0) {
                    $debitAmount = $balanceData['balance'];
                } else {
                    $creditAmount = $balanceData['balance'];
                }
            } else {
                // الحسابات الدائنة: الرصيد الدائن = credit - debit
                if ($balanceData['raw_balance'] >= 0) {
                    $creditAmount = $balanceData['balance'];
                } else {
                    $debitAmount = $balanceData['balance'];
                }
            }
            
            // إضافة فقط الحسابات التي لها رصيد
            if ($debitAmount > 0 || $creditAmount > 0) {
                $trialBalanceData[] = [
                    'account' => $account,
                    'debit' => $debitAmount,
                    'credit' => $creditAmount,
                ];
                
                $totalDebit += $debitAmount;
                $totalCredit += $creditAmount;
            }
        }
        
        $allPeriodsForDropdown = AccountingPeriod::with('fiscalYear')->orderBy('start_date', 'desc')->get();
        return view('wesal.index', [
            'page' => 'finance',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'trial-balance',
            'trialBalanceData' => $trialBalanceData,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'periods' => $allPeriodsForDropdown,
            'selectedPeriod' => $selectedPeriod,
            'asOf' => $asOf,
            'fiscalYearId' => $fiscalYearId,
        ]);
    }

    /**
     * تصدير ميزان المراجعة إلى Excel (عربي 100%، ترميز آمن)
     */
    public function trialBalanceExport(Request $request)
    {
        $periodId = $request->get('period_id');
        $asOf = $request->get('as_of') ? Carbon::parse($request->get('as_of')) : null;
        $periods = AccountingPeriod::with('fiscalYear')->orderBy('start_date', 'desc')->get();
        $currentPeriod = AccountingPeriod::getCurrent();
        $selectedPeriod = $periodId ? AccountingPeriod::find($periodId) : ($currentPeriod ?? $periods->first());
        if ($selectedPeriod && !$periodId) {
            $periodId = $selectedPeriod->id;
        }

        $accounts = ChartAccount::active()->postable()->orderBy('code')->get();
        $trialBalanceData = [];
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($accounts as $account) {
            $balanceData = $this->balanceService->calculateBalance($account->id, $periodId, $asOf);
            $debitAmount = 0;
            $creditAmount = 0;
            if ($account->nature === 'debit') {
                if (($balanceData['raw_balance'] ?? 0) >= 0) {
                    $debitAmount = $balanceData['balance'];
                } else {
                    $creditAmount = $balanceData['balance'];
                }
            } else {
                if (($balanceData['raw_balance'] ?? 0) >= 0) {
                    $creditAmount = $balanceData['balance'];
                } else {
                    $debitAmount = $balanceData['balance'];
                }
            }
            if ($debitAmount > 0 || $creditAmount > 0) {
                $trialBalanceData[] = [
                    'account' => $account,
                    'debit' => $debitAmount,
                    'credit' => $creditAmount,
                ];
                $totalDebit += $debitAmount;
                $totalCredit += $creditAmount;
            }
        }

        $spreadsheet = $this->excelExport->newSpreadsheet();
        $sheet = $this->excelExport->setupArabicSheet($spreadsheet, 'ميزان المراجعة');

        $headers = ['م', 'رقم الحساب', 'اسم الحساب', 'طبيعة الحساب', 'مدين', 'دائن'];
        $colCount = count($headers);
        $row = 1;
        foreach ($headers as $c => $h) {
            $this->excelExport->setCellValueByColumnAndRow($sheet,$c + 1, $row, $this->excelExport->ensureUtf8($h));
        }
        $this->excelExport->styleHeaderRow($sheet, $row, $colCount);
        $row++;

        foreach ($trialBalanceData as $i => $r) {
            $acc = $r['account'];
            $nature = ($acc->nature === 'debit') ? 'مدين' : 'دائن';
            $this->excelExport->setCellValueByColumnAndRow($sheet,1, $row, $i + 1);
            $this->excelExport->setCellValueByColumnAndRow($sheet,2, $row, $this->excelExport->ensureUtf8($acc->code));
            $this->excelExport->setCellValueByColumnAndRow($sheet,3, $row, $this->excelExport->ensureUtf8($acc->name_ar));
            $this->excelExport->setCellValueByColumnAndRow($sheet,4, $row, $this->excelExport->ensureUtf8($nature));
            $this->excelExport->setCellValueByColumnAndRow($sheet,5, $row, $r['debit'] > 0 ? $r['debit'] : '');
            $this->excelExport->setCellValueByColumnAndRow($sheet,6, $row, $r['credit'] > 0 ? $r['credit'] : '');
            $row++;
        }

        $this->excelExport->setCellValueByColumnAndRow($sheet,1, $row, $this->excelExport->ensureUtf8('الإجمالي'));
        $this->excelExport->mergeCellsByColumnAndRow($sheet, 1, $row, 4, $row);
        $this->excelExport->setCellValueByColumnAndRow($sheet,5, $row, $totalDebit);
        $this->excelExport->setCellValueByColumnAndRow($sheet,6, $row, $totalCredit);
        $sheet->getStyle("A{$row}:F{$row}")->getFont()->setBold(true)->setName('Tahoma');

        $filename = 'ميزان-المراجعة-' . ($selectedPeriod ? $selectedPeriod->period_name : date('Y-m-d'));
        return $this->excelExport->download($spreadsheet, $filename);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChartAccount $chartAccount)
    {
        if (!auth()->user()->can('finance.chart_accounts.delete')) {
            abort(403, 'ليس لديك صلاحية حذف الحسابات.');
        }
        if ($chartAccount->is_fixed) {
            return back()->withErrors(['error' => 'لا يمكن حذف الحسابات الثابتة']);
        }

        if ($chartAccount->hasChildren()) {
            return back()->withErrors(['error' => 'لا يمكن حذف الحساب لأنه يحتوي على حسابات فرعية']);
        }

        if ($chartAccount->journalLines()->exists()) {
            return back()->withErrors(['error' => 'لا يمكن حذف الحساب لأنه مستخدم في قيود']);
        }

        \App\Models\AuditLog::log('delete_account', $chartAccount, $chartAccount->toArray(), null);
        
        $chartAccount->delete();

        return redirect()->route('wesal.page', 'finance')
            ->with('success', 'تم حذف الحساب بنجاح');
    }

    /**
     * بناء الشجرة من القائمة المسطحة
     */
    private function buildTree($accounts, $parentId = null)
    {
        $branch = [];

        foreach ($accounts as $account) {
            if ($account->parent_id == $parentId) {
                $children = $this->buildTree($accounts, $account->id);
                if ($children) {
                    $account->children_list = $children;
                }
                $branch[] = $account;
            }
        }

        return $branch;
    }
}
