<?php

namespace App\Services\Finance;

use App\Models\ChartAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

class ChartAccountImporterService
{
    /**
     * استيراد من Excel
     */
    public function importFromExcel(string $filePath, string $sheetName = null): array
    {
        try {
            $spreadsheet = IOFactory::load($filePath);
            
            // تحديد الورقة
            if ($sheetName) {
                $worksheet = $spreadsheet->getSheetByName($sheetName);
                // إذا لم تُوجد بالاسم المحدد، جرب الورقة النشطة
                if (!$worksheet) {
                    $worksheet = $spreadsheet->getActiveSheet();
                    Log::warning("Sheet '{$sheetName}' not found, using active sheet: " . $worksheet->getTitle());
                }
            } else {
                $worksheet = $spreadsheet->getActiveSheet();
            }
            
            $rows = $worksheet->toArray();
            
            // تخطي الصف الأول (العناوين)
            array_shift($rows);
            
            return $this->processRows($rows);
        } catch (\Exception $e) {
            Log::error('Excel import failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * استيراد من CSV
     */
    public function importFromCsv(string $filePath): array
    {
        try {
            $reader = new Csv();
            $reader->setDelimiter(',');
            $reader->setEnclosure('"');
            $spreadsheet = $reader->load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            
            $rows = $worksheet->toArray();
            
            // تخطي الصف الأول (العناوين)
            array_shift($rows);
            
            return $this->processRows($rows);
        } catch (\Exception $e) {
            Log::error('CSV import failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * إنشاء/تحديث الحسابات الجذرية الأربعة فقط (دليل موحد: أصول، التزامات وصافي أصول، تبرعات وإيرادات، مصروفات)
     * وربط الحسابات 11، 12، 13 تحت جذر 1 (الأصول) إذا كانت بدون parent
     */
    public function ensureRootAccounts(): void
    {
        $roots = [
            ['code' => '1', 'name_ar' => 'الأصول', 'type' => 'asset', 'nature' => 'debit'],
            ['code' => '2', 'name_ar' => 'الالتزامات وصافي الأصول', 'type' => 'liability', 'nature' => 'credit'],
            ['code' => '3', 'name_ar' => 'التبرعات والإيرادات', 'type' => 'revenue', 'nature' => 'credit'],
            ['code' => '4', 'name_ar' => 'المصروفات', 'type' => 'expense', 'nature' => 'debit'],
        ];

        foreach ($roots as $r) {
            ChartAccount::updateOrCreate(
                ['code' => $r['code']],
                [
                    'name_ar' => $r['name_ar'],
                    'name_en' => $r['name_ar'],
                    'parent_id' => null,
                    'level' => 1,
                    'type' => $r['type'],
                    'nature' => $r['nature'],
                    'is_postable' => false,
                    'is_fixed' => true,
                    'status' => 'active',
                ]
            );
        }

        $this->reparentFirstLevelUnderRoots();
    }

    /**
     * ربط الحسابات من المستوى الثاني (11، 12، 13 تحت 1؛ 21، 22... تحت 2؛ إلخ) بجذورها إذا كانت parent_id فارغة
     */
    protected function reparentFirstLevelUnderRoots(): void
    {
        $rootCodes = ['1', '2', '3', '4'];
        foreach ($rootCodes as $rootCode) {
            $root = ChartAccount::where('code', $rootCode)->first();
            if (!$root) {
                continue;
            }
            $prefixLen = strlen($rootCode);
            ChartAccount::whereNull('parent_id')
                ->where('code', '!=', $rootCode)
                ->where(function ($q) use ($rootCode, $prefixLen) {
                    $q->where('code', 'like', $rootCode . '%');
                    $q->whereRaw('LENGTH(code) = ?', [$prefixLen + 1]);
                })
                ->update(['parent_id' => $root->id]);
        }
    }

    /**
     * معالجة الصفوف واستيرادها
     */
    protected function processRows(array $rows): array
    {
        DB::beginTransaction();
        
        try {
            $this->ensureRootAccounts();

            $inserted = 0;
            $updated = 0;
            $accounts = [];
            
            // تجميع الحسابات حسب المستوى
            foreach ($rows as $rowIndex => $row) {
                // في Excel: 
                // الصف 6: العناوين
                // من الصف 7: البيانات
                // العمود B (index 1) = الكود (نص مثل "1", "11", "111")  
                // العمود C (index 2) = الاسم (نص مثل "الأصول")
                
                // تخطي الصفوف الأولى (العناوين)
                if ($rowIndex < 6) {
                    continue;
                }
                
                // استخراج البيانات
                $code = isset($row[1]) ? trim((string) $row[1]) : '';
                $nameAr = isset($row[2]) ? trim((string) $row[2]) : '';
                
                // تخطي الصفوف الفارغة
                if (empty($code) || empty($nameAr)) {
                    continue;
                }
                
                // إزالة المسافات الزائدة من الكود
                $code = preg_replace('/\s+/', '', $code);
                
                // حساب المستوى من طول الكود
                // "1" = level 1, "11" = level 2, "111" = level 3, "11101" = level 5, "11101001" = level 8
                // لكن النظام المحاسبي يستخدم مستويات حتى 8 أرقام
                $codeLength = strlen($code);
                
                // تحديد المستوى بناءً على طول الكود
                // 1 رقم = level 1, 2 أرقام = level 2, 3 أرقام = level 3, 5 أرقام = level 4, 8 أرقام = level 5
                if ($codeLength == 1) {
                    $level = 1;
                } elseif ($codeLength == 2) {
                    $level = 2;
                } elseif ($codeLength == 3) {
                    $level = 3;
                } elseif ($codeLength == 5) {
                    $level = 4;
                } elseif ($codeLength == 8) {
                    $level = 5;
                } else {
                    // تخطي الأكواد بطول غير معروف
                    continue;
                }
                
                // تحديد النوع والطبيعة من الكود (افتراضي)
                $type = $this->determineType($code, $level);
                $nature = $this->determineNature($code, $level);
                
                $accounts[] = [
                    'level' => $level,
                    'code' => $code,
                    'name_ar' => $nameAr,
                    'type' => $type,
                    'nature' => $nature,
                ];
            }
            
            // ترتيب حسب المستوى ثم الكود (المستوى الأصغر أولاً)
            usort($accounts, function ($a, $b) {
                if ($a['level'] != $b['level']) {
                    return $a['level'] <=> $b['level'];
                }
                return strcmp($a['code'], $b['code']);
            });
            
            // بناء parent_id - نمرر على الحسابات حسب المستوى
            $codeToId = [];
            foreach ($accounts as $accountData) {
                $parentId = null;
                
                // إذا كان المستوى أكبر من 1، ابحث عن الأب
                if ($accountData['level'] > 1) {
                    // البحث عن الأب بالانتقال من المستويات الأصغر إلى الأكبر
                    // مثال: "11101" -> جرب "1110" (4) -> "111" (3) -> "11" (2) -> "1" (1)
                    $code = $accountData['code'];
                    $found = false;
                    
                    for ($tryLevel = $accountData['level'] - 1; $tryLevel >= 1 && !$found; $tryLevel--) {
                        $parentCode = substr($code, 0, $tryLevel);
                        
                        if (empty($parentCode)) {
                            continue;
                        }
                        
                        // البحث في المصفوفة المحلية أولاً
                        if (isset($codeToId[$parentCode])) {
                            $parentId = $codeToId[$parentCode];
                            $found = true;
                            break;
                        }
                        
                        // البحث في قاعدة البيانات
                        $parentAccount = ChartAccount::where('code', $parentCode)->first();
                        if ($parentAccount) {
                            $parentId = $parentAccount->id;
                            $codeToId[$parentCode] = $parentId; // حفظ في المصفوفة
                            $found = true;
                            break;
                        }
                    }
                }
                
                $account = ChartAccount::updateOrCreate(
                    ['code' => $accountData['code']],
                    [
                        'name_ar' => $accountData['name_ar'],
                        'name_en' => $accountData['name_ar'], // نفس الاسم العربي مؤقتاً
                        'parent_id' => $parentId,
                        'level' => $accountData['level'],
                        'type' => $accountData['type'],
                        'nature' => $accountData['nature'],
                        'is_postable' => true, // سيتم تحديثه لاحقاً
                        'is_fixed' => true,
                        'status' => 'active',
                        'description' => null,
                    ]
                );
                
                $codeToId[$accountData['code']] = $account->id;
                
                if ($account->wasRecentlyCreated) {
                    $inserted++;
                } else {
                    $updated++;
                }
            }
            
            // تحديث is_postable
            $this->updatePostableFlags();
            
            // إصلاح الحسابات التي لم تجد أباً (orphan accounts)
            $this->fixOrphanAccounts();
            // جعل الحسابات الجارية تحت "نقدية وودائع في البنوك" (11101)
            $this->reparentCurrentAccountsUnderBankDeposits();
            
            DB::commit();
            
            return [
                'success' => true,
                'inserted' => $inserted,
                'updated' => $updated,
                'total' => $inserted + $updated,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import process failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * الحصول على كود الأب بناءً على الكود والمستوى
     * النظام المحاسبي يستخدم مستويات غير منتظمة:
     * - "111" (level 3) قد يكون له أبناء "11101" (level 5) مباشرة
     * - نحتاج للبحث عن أقرب أب موجود
     */
    protected function getParentCode(string $code, int $level): ?string
    {
        if ($level <= 1) {
            return null; // الحسابات الرئيسية
        }
        
        // البحث عن الأب بالانتقال من المستوى الحالي إلى المستويات الأصغر
        // نبدأ من level - 1 وننزل حتى نجد أب موجود
        for ($parentLevel = $level - 1; $parentLevel >= 1; $parentLevel--) {
            $parentCode = substr($code, 0, $parentLevel);
            
            // إذا كان الكود غير فارغ، رجعه
            if (!empty($parentCode)) {
                return $parentCode;
            }
        }
        
        return null;
    }
    
    /**
     * تحديث is_postable بناءً على وجود أبناء
     */
    public function updatePostableFlags(): void
    {
        // أي حساب له أبناء => is_postable=false
        $accountsWithChildren = ChartAccount::whereHas('children')->get();
        foreach ($accountsWithChildren as $account) {
            $account->is_postable = false;
            $account->save();
        }
        
        // ما لا أبناء له => is_postable=true
        $accountsWithoutChildren = ChartAccount::whereDoesntHave('children')->get();
        foreach ($accountsWithoutChildren as $account) {
            $account->is_postable = true;
            $account->save();
        }
    }
    
    /**
     * إصلاح الحسابات التي لم تجد أباً (orphan accounts)
     * الحسابات التي تبدأ بـ 5 (مصروفات في بعض الدلائل) تُربط تحت جذر 4 عبر حساب وسيط "5"
     */
    protected function fixOrphanAccounts(): void
    {
        $rootCodes = ['1', '2', '3', '4'];
        $orphans = ChartAccount::whereNull('parent_id')
            ->whereNotIn('code', $rootCodes)
            ->get();

        $hasFiveBranch = $orphans->contains(fn ($o) => substr($o->code, 0, 1) === '5');
        if ($hasFiveBranch && !ChartAccount::where('code', '5')->exists()) {
            $root4 = ChartAccount::where('code', '4')->first();
            if ($root4) {
                ChartAccount::updateOrCreate(
                    ['code' => '5'],
                    [
                        'name_ar' => 'المصروفات (فرعي)',
                        'name_en' => 'Expenses (sub)',
                        'parent_id' => $root4->id,
                        'level' => 2,
                        'type' => 'expense',
                        'nature' => 'debit',
                        'is_postable' => false,
                        'is_fixed' => true,
                        'status' => 'active',
                    ]
                );
            }
        }

        foreach ($orphans as $orphan) {
            $code = $orphan->code;
            $level = $orphan->level;

            if ($code === '5') {
                $parent = ChartAccount::where('code', '4')->first();
                if ($parent) {
                    $orphan->parent_id = $parent->id;
                    $orphan->save();
                }
                continue;
            }

            for ($tryLevel = $level - 1; $tryLevel >= 1; $tryLevel--) {
                $parentCode = substr($code, 0, $tryLevel);
                if (empty($parentCode)) {
                    continue;
                }
                $parent = ChartAccount::where('code', $parentCode)->first();
                if ($parent) {
                    $orphan->parent_id = $parent->id;
                    $orphan->save();
                    break;
                }
            }
        }
    }

    /**
     * جعل الحسابات الجارية تحت "نقدية وودائع في البنوك" (11101) بالتسلسل المعترف به فقط:
     * 11101001، 11101002، 11101003، 11101004، 11101005، 11101006 (1110100 + 1..6).
     * أي حساب آخر كان تحت 11101 يُعاد ربطه تحت 111 (النقدية وما في حكمها).
     */
    public function reparentCurrentAccountsUnderBankDeposits(): int
    {
        $parent11101 = ChartAccount::where('code', '11101')->first();
        $parent111 = ChartAccount::where('code', '111')->first();
        if (!$parent11101) {
            return 0;
        }
        $allowedCodes = ['11101001', '11101002', '11101003', '11101004', '11101005', '11101006'];
        $count = 0;
        // ربط الحسابات 11101001..11101006 فقط تحت 11101
        foreach ($allowedCodes as $code) {
            $account = ChartAccount::where('code', $code)->first();
            if ($account && $account->parent_id != $parent11101->id) {
                $account->parent_id = $parent11101->id;
                $account->level = $parent11101->level + 1;
                $account->save();
                $count++;
            }
        }
        // إعادة ربط أي حساب آخر تحت 11101 ليكون تحت 111 (أخوة لـ 11101)
        if ($parent111) {
            $wrongUnder11101 = ChartAccount::where('parent_id', $parent11101->id)
                ->whereNotIn('code', $allowedCodes)
                ->get();
            foreach ($wrongUnder11101 as $account) {
                $account->parent_id = $parent111->id;
                $account->level = $parent111->level + 1;
                $account->save();
                $count++;
            }
        }
        if ($count > 0) {
            $this->updatePostableFlags();
        }
        return $count;
    }
    
    /**
     * تحديد نوع الحساب من الكود
     * الدليل الموحد: 1=الأصول، 2=الالتزامات وصافي الأصول، 3=التبرعات والإيرادات، 4=المصروفات
     */
    protected function determineType(string $code, int $level): string
    {
        $firstDigit = substr($code, 0, 1);

        if ($firstDigit == '1') {
            return 'asset';
        }
        if ($firstDigit == '2') {
            return 'liability';
        }
        if ($firstDigit == '3') {
            return 'revenue';
        }
        if ($firstDigit == '4' || $firstDigit == '5') {
            return 'expense';
        }

        return 'asset';
    }

    /**
     * تحديد طبيعة الحساب
     */
    protected function determineNature(string $code, int $level): string
    {
        $firstDigit = substr($code, 0, 1);

        if ($firstDigit == '1' || $firstDigit == '4' || $firstDigit == '5') {
            return 'debit';
        }

        return 'credit';
    }
}
