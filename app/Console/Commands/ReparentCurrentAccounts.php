<?php

namespace App\Console\Commands;

use App\Services\Finance\ChartAccountImporterService;
use Illuminate\Console\Command;

class ReparentCurrentAccounts extends Command
{
    protected $signature = 'finance:reparent-current-accounts';
    protected $description = 'وضع الحسابات الجارية تحت "نقدية وودائع في البنوك" (كود 11101)';

    public function handle(): int
    {
        $importer = new ChartAccountImporterService();
        $count = $importer->reparentCurrentAccountsUnderBankDeposits();
        if ($count > 0) {
            $this->info("تم نقل {$count} حساب جاري تحت نقدية وودائع في البنوك.");
        } else {
            $this->info('لا توجد حسابات جارية تحتاج نقل، أو حساب "نقدية وودائع في البنوك" (11101) غير موجود.');
        }
        return 0;
    }
}
