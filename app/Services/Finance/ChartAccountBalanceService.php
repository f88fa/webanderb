<?php

namespace App\Services\Finance;

use App\Models\ChartAccount;
use App\Models\JournalLine;
use App\Models\AccountingPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ChartAccountBalanceService
{
    /**
     * حساب رصيد حساب معين
     */
    public function calculateBalance(
        int $accountId,
        ?int $periodId = null,
        ?Carbon $asOf = null
    ): array {
        $account = ChartAccount::findOrFail($accountId);
        
        // Cache key
        $cacheKey = "account_balance_{$accountId}_" . ($periodId ?? 'all') . '_' . ($asOf ? $asOf->format('Y-m-d') : 'all');
        
        return Cache::remember($cacheKey, 300, function () use ($account, $periodId, $asOf) {
            $query = JournalLine::query()
                ->join('journal_entries', 'journal_lines.journal_entry_id', '=', 'journal_entries.id')
                ->where('journal_lines.account_id', $account->id)
                ->where('journal_entries.status', 'posted');
            
            // فلترة حسب الفترة أو التاريخ
            if ($periodId) {
                $query->where('journal_entries.period_id', $periodId);
            }
            if ($asOf) {
                $query->where('journal_entries.entry_date', '<=', $asOf);
            }
            
            $result = $query->selectRaw('
                COALESCE(SUM(journal_lines.debit), 0) as total_debit,
                COALESCE(SUM(journal_lines.credit), 0) as total_credit
            ')->first();
            
            $totalDebit = (float) ($result->total_debit ?? 0);
            $totalCredit = (float) ($result->total_credit ?? 0);
            
            // حساب الرصيد حسب طبيعة الحساب
            if ($account->nature === 'debit') {
                $balance = $totalDebit - $totalCredit;
                $balanceType = $balance >= 0 ? 'debit' : 'credit';
            } else {
                $balance = $totalCredit - $totalDebit;
                $balanceType = $balance >= 0 ? 'credit' : 'debit';
            }
            
            return [
                'balance' => abs($balance),
                'balance_type' => $balanceType,
                'raw_balance' => $balance, // للعرض السالب/الموجب
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
            ];
        });
    }
    
    /**
     * حساب رصيد مع Rollup للأبناء (للحسابات التجميعية)
     */
    public function calculateBalanceWithRollup(
        int $accountId,
        ?int $periodId = null,
        ?Carbon $asOf = null
    ): array {
        $account = ChartAccount::findOrFail($accountId);
        
        // إذا كان الحساب قابل للترحيل، احسب رصيده مباشرة
        if ($account->is_postable) {
            return $this->calculateBalance($accountId, $periodId, $asOf);
        }
        
        // للحسابات التجميعية، احسب مجموع الأبناء
        $cacheKey = "account_balance_rollup_{$accountId}_" . ($periodId ?? 'all') . '_' . ($asOf ? $asOf->format('Y-m-d') : 'all');
        
        return Cache::remember($cacheKey, 300, function () use ($account, $periodId, $asOf) {
            // جلب جميع الأبناء (بشكل متكرر)
            $childrenIds = $this->getAllChildrenIds($account->id);
            
            if (empty($childrenIds)) {
                return [
                    'balance' => 0,
                    'balance_type' => $account->nature,
                    'raw_balance' => 0,
                    'total_debit' => 0,
                    'total_credit' => 0,
                ];
            }
            
            // حساب مجموع الأرصدة للأبناء
            $query = JournalLine::query()
                ->join('journal_entries', 'journal_lines.journal_entry_id', '=', 'journal_entries.id')
                ->whereIn('journal_lines.account_id', $childrenIds)
                ->where('journal_entries.status', 'posted');
            
            if ($periodId) {
                $query->where('journal_entries.period_id', $periodId);
            }
            if ($asOf) {
                $query->where('journal_entries.entry_date', '<=', $asOf);
            }
            
            // حساب حسب طبيعة كل حساب
            $balances = [];
            foreach ($childrenIds as $childId) {
                $childAccount = ChartAccount::find($childId);
                if (!$childAccount) continue;
                
                // إذا كان الحساب تجميعي، احسب rollup له
                if (!$childAccount->is_postable) {
                    $childBalance = $this->calculateBalanceWithRollup($childId, $periodId, $asOf);
                } else {
                    $childBalance = $this->calculateBalance($childId, $periodId, $asOf);
                }
                $balances[] = $childBalance['raw_balance'];
            }
            
            $totalBalance = array_sum($balances);
            
            return [
                'balance' => abs($totalBalance),
                'balance_type' => $totalBalance >= 0 ? ($account->nature === 'debit' ? 'debit' : 'credit') : ($account->nature === 'debit' ? 'credit' : 'debit'),
                'raw_balance' => $totalBalance,
                'total_debit' => 0, // لا معنى له في التجميعي
                'total_credit' => 0,
            ];
        });
    }
    
    /**
     * جلب جميع IDs للأبناء بشكل متكرر
     */
    private function getAllChildrenIds(int $parentId): array
    {
        $ids = [];
        $children = ChartAccount::where('parent_id', $parentId)->get();
        
        foreach ($children as $child) {
            $ids[] = $child->id;
            // إذا كان تجميعي، اجلب أبناءه أيضاً
            if (!$child->is_postable) {
                $ids = array_merge($ids, $this->getAllChildrenIds($child->id));
            }
        }
        
        return $ids;
    }
    
    /**
     * إيجاد كود الحساب الأب من تسلسل الكود: الأب = أطول بادئة من الكود موجودة في القائمة
     * مثال: 11101001 → الأب 11101 ، 111.02.003 → الأب 111.02
     */
    private function findParentCodeByPrefix(string $code, \Illuminate\Support\Collection $allCodes): ?string
    {
        $code = trim($code);
        if ($code === '') {
            return null;
        }
        $len = strlen($code);
        for ($l = $len - 1; $l >= 1; $l--) {
            $prefix = substr($code, 0, $l);
            if ($allCodes->contains($prefix)) {
                return $prefix;
            }
        }
        return null;
    }

    /**
     * بناء الشجرة حسب تسلسل كود الحساب (الأب = أطول بادئة) ثم إرجاع العقد تحت parentId
     */
    private function buildTreeWithBalancesFromCodeMap(
        array $childrenByParentId,
        ?int $parentId,
        ?int $periodId,
        ?Carbon $asOf
    ): array {
        $childAccounts = $childrenByParentId[$parentId] ?? [];
        usort($childAccounts, fn ($a, $b) => strcmp($a->code, $b->code));
        $tree = [];
        foreach ($childAccounts as $account) {
            $balanceData = $this->calculateBalanceWithRollup($account->id, $periodId, $asOf);
            $hasChildren = isset($childrenByParentId[$account->id]) && count($childrenByParentId[$account->id]) > 0;
            $node = [
                'id' => $account->id,
                'code' => $account->code,
                'name_ar' => $account->name_ar,
                'name_en' => $account->name_en,
                'level' => $account->level,
                'parent_id' => $account->parent_id,
                'type' => $account->type,
                'nature' => $account->nature,
                'is_postable' => $account->is_postable,
                'is_fixed' => $account->is_fixed,
                'status' => $account->status,
                'description' => $account->description,
                'balance' => $balanceData['balance'],
                'balance_type' => $balanceData['balance_type'],
                'raw_balance' => $balanceData['raw_balance'],
                'has_children' => $hasChildren,
                'children' => [],
            ];
            if ($hasChildren) {
                $node['children'] = $this->buildTreeWithBalancesFromCodeMap($childrenByParentId, $account->id, $periodId, $asOf);
            }
            $tree[] = $node;
        }
        return $tree;
    }

    /**
     * بناء Tree مع الأرصدة — حسب تسلسل كود الحساب (الأب = أطول بادئة للكود)
     */
    public function buildTreeWithBalances(
        ?int $periodId = null,
        ?Carbon $asOf = null,
        ?int $parentId = null
    ): array {
        $all = ChartAccount::active()->orderBy('code')->get();
        $allCodes = $all->pluck('code');
        $codeToAccount = $all->keyBy('code');
        $childrenByParentId = [];
        $rootIds = [];
        foreach ($all as $account) {
            $parentCode = $this->findParentCodeByPrefix($account->code, $allCodes);
            $parentIdByCode = $parentCode !== null && isset($codeToAccount[$parentCode])
                ? $codeToAccount[$parentCode]->id
                : null;
            if ($parentIdByCode === null) {
                $rootIds[] = $account->id;
            } else {
                $childrenByParentId[$parentIdByCode] = $childrenByParentId[$parentIdByCode] ?? [];
                $childrenByParentId[$parentIdByCode][] = $account;
            }
        }
        $roots = $all->whereIn('id', $rootIds)->sortBy('code')->values();
        $tree = [];
        foreach ($roots as $account) {
            $balanceData = $this->calculateBalanceWithRollup($account->id, $periodId, $asOf);
            $hasChildren = isset($childrenByParentId[$account->id]) && count($childrenByParentId[$account->id]) > 0;
            $node = [
                'id' => $account->id,
                'code' => $account->code,
                'name_ar' => $account->name_ar,
                'name_en' => $account->name_en,
                'level' => $account->level,
                'parent_id' => $account->parent_id,
                'type' => $account->type,
                'nature' => $account->nature,
                'is_postable' => $account->is_postable,
                'is_fixed' => $account->is_fixed,
                'status' => $account->status,
                'description' => $account->description,
                'balance' => $balanceData['balance'],
                'balance_type' => $balanceData['balance_type'],
                'raw_balance' => $balanceData['raw_balance'],
                'has_children' => $hasChildren,
                'children' => [],
            ];
            if ($hasChildren) {
                $node['children'] = $this->buildTreeWithBalancesFromCodeMap($childrenByParentId, $account->id, $periodId, $asOf);
            }
            $tree[] = $node;
        }
        return $tree;
    }

    /**
     * بناء شجرة حسابات من مستوى معين — حسب تسلسل كود الحساب (لاختيار الحساب)
     */
    private function buildTreeFromLevelByCodeMap(array $childrenByParentId, ?int $parentId): array
    {
        $childAccounts = $childrenByParentId[$parentId] ?? [];
        usort($childAccounts, fn ($a, $b) => strcmp($a->code, $b->code));
        $tree = [];
        foreach ($childAccounts as $account) {
            $hasChildren = isset($childrenByParentId[$account->id]) && count($childrenByParentId[$account->id]) > 0;
            $node = [
                'id' => $account->id,
                'code' => $account->code,
                'name_ar' => $account->name_ar,
                'level' => $account->level,
                'is_postable' => (bool) $account->is_postable,
                'has_children' => $hasChildren,
                'children' => [],
            ];
            if ($hasChildren) {
                $node['children'] = $this->buildTreeFromLevelByCodeMap($childrenByParentId, $account->id);
            }
            $tree[] = $node;
        }
        return $tree;
    }

    /**
     * بناء شجرة حسابات من مستوى معين (لاختيار الحساب - بدون أرصدة)
     * التسلسل حسب كود الحساب: الأب = أطول بادئة للكود
     */
    public function buildTreeFromLevel(int $minLevel = 3, ?int $parentId = null): array
    {
        $all = ChartAccount::active()->orderBy('code')->get();
        $allCodes = $all->pluck('code');
        $codeToAccount = $all->keyBy('code');
        $childrenByParentId = [];
        $rootIds = [];
        foreach ($all as $account) {
            $parentCode = $this->findParentCodeByPrefix($account->code, $allCodes);
            $parentIdByCode = $parentCode !== null && isset($codeToAccount[$parentCode])
                ? $codeToAccount[$parentCode]->id
                : null;
            if ($parentIdByCode === null) {
                $rootIds[] = $account->id;
            } else {
                $childrenByParentId[$parentIdByCode] = $childrenByParentId[$parentIdByCode] ?? [];
                $childrenByParentId[$parentIdByCode][] = $account;
            }
        }
        $roots = $all->whereIn('id', $rootIds)->sortBy('code')->values();
        $tree = [];
        foreach ($roots as $account) {
            $hasChildren = isset($childrenByParentId[$account->id]) && count($childrenByParentId[$account->id]) > 0;
            $node = [
                'id' => $account->id,
                'code' => $account->code,
                'name_ar' => $account->name_ar,
                'level' => $account->level,
                'is_postable' => (bool) $account->is_postable,
                'has_children' => $hasChildren,
                'children' => [],
            ];
            if ($hasChildren) {
                $node['children'] = $this->buildTreeFromLevelByCodeMap($childrenByParentId, $account->id);
            }
            $tree[] = $node;
        }
        return $tree;
    }
    
    /**
     * حساب رصيد حساب ضمن سنة مالية معينة (لإقفال السنة)
     */
    public function calculateBalanceForFiscalYear(int $accountId, int $fiscalYearId, ?Carbon $asOf = null): array
    {
        $periodIds = AccountingPeriod::where('fiscal_year_id', $fiscalYearId)->pluck('id')->toArray();
        if (empty($periodIds)) {
            return [
                'balance' => 0,
                'balance_type' => 'debit',
                'raw_balance' => 0,
                'total_debit' => 0,
                'total_credit' => 0,
            ];
        }

        $account = ChartAccount::findOrFail($accountId);
        $query = JournalLine::query()
            ->join('journal_entries', 'journal_lines.journal_entry_id', '=', 'journal_entries.id')
            ->where('journal_lines.account_id', $account->id)
            ->where('journal_entries.status', 'posted')
            ->whereIn('journal_entries.period_id', $periodIds);

        if ($asOf) {
            $query->where('journal_entries.entry_date', '<=', $asOf);
        }

        $result = $query->selectRaw('
            COALESCE(SUM(journal_lines.debit), 0) as total_debit,
            COALESCE(SUM(journal_lines.credit), 0) as total_credit
        ')->first();

        $totalDebit = (float) ($result->total_debit ?? 0);
        $totalCredit = (float) ($result->total_credit ?? 0);

        if ($account->nature === 'debit') {
            $balance = $totalDebit - $totalCredit;
            $balanceType = $balance >= 0 ? 'debit' : 'credit';
        } else {
            $balance = $totalCredit - $totalDebit;
            $balanceType = $balance >= 0 ? 'credit' : 'debit';
        }

        return [
            'balance' => abs($balance),
            'balance_type' => $balanceType,
            'raw_balance' => $balance,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
        ];
    }

    /**
     * إلغاء Cache للأرصدة عند ترحيل قيد جديد
     */
    public function clearBalanceCache(?int $accountId = null, ?int $periodId = null): void
    {
        if ($accountId) {
            // إلغاء cache لحساب محدد
            $pattern = "account_balance*_{$accountId}_*";
            Cache::flush(); // أو استخدام tags إذا كان متاحاً
        } else {
            // إلغاء جميع cache
            Cache::flush();
        }
    }
}
