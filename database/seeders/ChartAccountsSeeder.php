<?php

namespace Database\Seeders;

use App\Models\ChartAccount;
use App\Services\Finance\ChartAccountImporterService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChartAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * هذا الـ Seeder يستقبل البيانات من CHART_TEXT
     * الصيغة: LEVEL|CODE|NAME_AR|TYPE|NATURE
     */
    public function run(): void
    {
        // TODO: استبدل هذا المصفوفة بـ CHART_TEXT الكامل من المستخدم
        $chartText = $this->getChartText();

        if (empty($chartText)) {
            $this->command->warn('CHART_TEXT فارغ. سيتم إنشاء دليل حسابي أساسي.');
            $this->createBasicChart();
            return;
        }

        DB::beginTransaction();
        try {
            $accounts = [];
            $parentMap = []; // لتخزين parent_id لكل level

            foreach ($chartText as $line) {
                if (empty(trim($line)) || strpos($line, 'LEVEL|CODE|NAME_AR|TYPE|NATURE') === 0) {
                    continue; // تخطي السطر الأول (header) أو الأسطر الفارغة
                }

                $parts = explode('|', $line);
                if (count($parts) < 5) {
                    continue;
                }

                $level = (int) trim($parts[0]);
                $code = trim($parts[1]);
                $nameAr = trim($parts[2]);
                $type = trim($parts[3]);
                $nature = trim($parts[4]);

                // تحديد parent_id
                $parentId = null;
                if ($level > 1) {
                    // البحث عن آخر حساب في المستوى السابق
                    for ($i = $level - 1; $i >= 1; $i--) {
                        if (isset($parentMap[$i])) {
                            $parentId = $parentMap[$i];
                            break;
                        }
                    }
                }

                // التحقق من عدم وجود الحساب مسبقاً
                $existingAccount = ChartAccount::where('code', $code)->first();
                if ($existingAccount) {
                    $this->command->warn("الحساب {$code} موجود مسبقاً، سيتم تخطيه.");
                    $parentMap[$level] = $existingAccount->id;
                    continue;
                }

                // إنشاء الحساب
                $account = ChartAccount::create([
                    'code' => $code,
                    'name_ar' => $nameAr,
                    'name_en' => null,
                    'parent_id' => $parentId,
                    'level' => $level,
                    'type' => $type,
                    'nature' => $nature,
                    'is_postable' => false, // سيتم تحديثه لاحقاً
                    'is_fixed' => true, // جميع الحسابات من CHART_TEXT ثابتة
                    'status' => 'active',
                    'description' => null,
                ]);

                $parentMap[$level] = $account->id;
                $accounts[] = $account;
            }

            // تحديث is_postable للحسابات التي لا يوجد لها أبناء
            $this->updatePostableStatus();

            DB::commit();

            $this->command->info('تم استيراد ' . count($accounts) . ' حساب من CHART_TEXT بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('حدث خطأ أثناء الاستيراد: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * الحصول على CHART_TEXT
     * TODO: استبدل هذا بالبيانات الفعلية من المستخدم
     */
    private function getChartText(): array
    {
        // هذا مثال - سيتم استبداله بـ CHART_TEXT الكامل
        return [
            'LEVEL|CODE|NAME_AR|TYPE|NATURE',
            '1|1|الأصول|asset|debit',
            '2|11|الأصول المتداولة|asset|debit',
            '3|111|النقدية وما في حكمها|asset|debit',
            '4|11101|نقدية وودائع في البنوك|asset|debit',
            '5|11101001|حسابات جارية - بنك الرياض|asset|debit',
            // ... سيتم إضافة باقي الحسابات هنا
        ];
    }

    /**
     * إنشاء الدليل الأساسي: الجذور الأربعة فقط (الدليل الموحد)
     * ١ الأصول  ٢ الالتزامات وصافي الأصول  ٣ التبرعات والإيرادات  ٤ المصروفات
     */
    private function createBasicChart(): void
    {
        $importer = new ChartAccountImporterService();
        $importer->ensureRootAccounts();
        $importer->updatePostableFlags();
        $this->command->info('تم إنشاء الجذور الأربعة لدليل الحسابات (الأصول، الالتزامات وصافي الأصول، التبرعات والإيرادات، المصروفات). استخدم أمر finance:import-accounts مع ملف dlleel.xlsx لاستيراد التفاصيل.');
    }

    /**
     * تحديث حالة is_postable للحسابات
     */
    private function updatePostableStatus(): void
    {
        // جميع الحسابات التي لا يوجد لها أبناء تصبح قابلة للترحيل
        $accountsWithChildren = ChartAccount::whereHas('children')->pluck('id');
        ChartAccount::whereNotIn('id', $accountsWithChildren)->update(['is_postable' => true]);
    }
}
