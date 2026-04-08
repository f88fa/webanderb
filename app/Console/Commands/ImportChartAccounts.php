<?php

namespace App\Console\Commands;

use App\Models\ChartAccount;
use App\Services\Finance\ChartAccountImporterService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportChartAccounts extends Command
{
    protected $signature = 'finance:import-accounts {--force : Force re-import even if accounts exist}';
    protected $description = 'Import chart accounts from Excel/CSV or create basic accounts';

    public function handle()
    {
        $importer = new ChartAccountImporterService();
        
        // البحث عن ملف Excel في عدة مواقع محتملة
        $excelPaths = [
            base_path('dlleel.xlsx'),
            '/mnt/data/dlleel.xlsx',
            storage_path('app/dlleel.xlsx'),
        ];
        
        $excelPath = null;
        foreach ($excelPaths as $path) {
            if (file_exists($path)) {
                $excelPath = $path;
                break;
            }
        }
        
        $csvPath = '/mnt/data/chart_accounts_unified.csv';
        
        // Check if accounts already exist - لكن نسمح بإعادة الاستيراد إذا طُلب
        $existingCount = ChartAccount::count();
        if ($existingCount > 0 && !$this->option('force')) {
            $this->info("Accounts already exist ({$existingCount} accounts). Use --force to re-import.");
            return 0;
        }
        
        // Try Excel first
        if ($excelPath) {
            try {
                $this->info("Attempting to import from Excel: {$excelPath}");
                $result = $importer->importFromExcel($excelPath, 'م1أ دليل الحسابات الموحد كاملا');
                $this->info("Import successful: {$result['inserted']} inserted, {$result['updated']} updated, {$result['total']} total");
                return 0;
            } catch (\Exception $e) {
                $this->warn("Excel import failed: " . $e->getMessage());
                $this->error($e->getTraceAsString());
            }
        } else {
            $this->warn("Excel file not found in any of the expected locations.");
        }
        
        // Try CSV
        if (file_exists($csvPath)) {
            try {
                $this->info("Attempting to import from CSV: {$csvPath}");
                $result = $importer->importFromCsv($csvPath);
                $this->info("Import successful: {$result['inserted']} inserted, {$result['updated']} updated");
                return 0;
            } catch (\Exception $e) {
                $this->warn("CSV import failed: " . $e->getMessage());
            }
        }
        
        // Create basic accounts
        $this->info("No import files found. Creating basic accounts...");
        $result = $this->createBasicAccounts($importer);
        $this->info("Basic accounts created: {$result['inserted']} inserted");
        
        return 0;
    }
    
    protected function createBasicAccounts(ChartAccountImporterService $importer): array
    {
        DB::beginTransaction();

        try {
            $importer->ensureRootAccounts();
            $importer->updatePostableFlags();

            DB::commit();

            return [
                'success' => true,
                'inserted' => 4,
                'updated' => 0,
                'total' => 4,
                'method' => 'basic_accounts',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
