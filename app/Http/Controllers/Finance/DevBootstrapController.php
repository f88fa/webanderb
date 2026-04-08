<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DevBootstrapController extends Controller
{
    public function __construct()
    {
        // حماية Route - فقط في بيئة التطوير
        if (!env('FINANCE_DEV_ROUTES', false)) {
            abort(404);
        }
        
        // التحقق من الصلاحيات
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user || (!$user->hasRole('SuperAdmin') && !$user->hasRole('FinanceAdmin'))) {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        });
    }

    /**
     * Bootstrap accounts - تنفيذ كامل
     */
    public function bootstrap(Request $request)
    {
        try {
            $report = [
                'success' => true,
                'steps' => [],
                'database' => [
                    'name' => config('database.connections.mysql.database'),
                    'host' => config('database.connections.mysql.host'),
                ],
                'results' => [],
            ];
            
            // Step 1: Run Migrations (ignore errors for existing tables)
            try {
                Artisan::call('migrate', ['--force' => true]);
                $migrationOutput = Artisan::output();
                $report['steps'][] = [
                    'step' => 'migrations',
                    'status' => 'success',
                    'output' => $migrationOutput,
                ];
            } catch (\Exception $e) {
                // Ignore "table already exists" errors
                if (strpos($e->getMessage(), 'already exists') !== false) {
                    $report['steps'][] = [
                        'step' => 'migrations',
                        'status' => 'warning',
                        'message' => 'Some tables already exist (this is OK)',
                    ];
                } else {
                    $report['steps'][] = [
                        'step' => 'migrations',
                        'status' => 'error',
                        'error' => $e->getMessage(),
                    ];
                }
            }
            
            // Step 2: Run Seeders (skip if fails - not critical for accounts)
            try {
                Artisan::call('db:seed', [
                    '--class' => 'FinancePermissionsSeeder',
                    '--force' => true,
                ]);
                $report['steps'][] = [
                    'step' => 'finance_permissions_seeder',
                    'status' => 'success',
                ];
            } catch (\Exception $e) {
                // Permissions seeder may fail if roles table structure differs
                $report['steps'][] = [
                    'step' => 'finance_permissions_seeder',
                    'status' => 'warning',
                    'message' => 'Permissions seeder skipped (may already exist)',
                    'error' => $e->getMessage(),
                ];
            }
            
            // Step 3: Check if accounts exist and import if needed
            $accountsCount = \App\Models\ChartAccount::count();
            
            if ($accountsCount == 0) {
                // Step 4: Import accounts using command
                try {
                    Artisan::call('finance:import-accounts');
                    $importOutput = Artisan::output();
                    $newCount = \App\Models\ChartAccount::count();
                    $report['steps'][] = [
                        'step' => 'import_accounts',
                        'status' => 'success',
                        'output' => $importOutput,
                        'accounts_created' => $newCount,
                    ];
                    $report['results'] = [
                        'success' => true,
                        'inserted' => $newCount,
                        'total' => $newCount,
                    ];
                } catch (\Exception $e) {
                    $report['steps'][] = [
                        'step' => 'import_accounts',
                        'status' => 'error',
                        'error' => $e->getMessage(),
                    ];
                    $report['results'] = [
                        'success' => false,
                        'error' => $e->getMessage(),
                    ];
                }
            } else {
                $report['steps'][] = [
                    'step' => 'import_accounts',
                    'status' => 'skipped',
                    'reason' => "Accounts already exist ({$accountsCount} accounts)",
                ];
            }
            
            // Step 5: Run ChartAccountsSeeder if needed
            try {
                Artisan::call('db:seed', [
                    '--class' => 'ChartAccountsSeeder',
                    '--force' => true,
                ]);
                $report['steps'][] = [
                    'step' => 'chart_accounts_seeder',
                    'status' => 'success',
                ];
            } catch (\Exception $e) {
                $report['steps'][] = [
                    'step' => 'chart_accounts_seeder',
                    'status' => 'error',
                    'error' => $e->getMessage(),
                ];
            }
            
            // Step 6: Clear Cache
            try {
                Cache::flush();
                $report['steps'][] = [
                    'step' => 'clear_cache',
                    'status' => 'success',
                ];
            } catch (\Exception $e) {
                $report['steps'][] = [
                    'step' => 'clear_cache',
                    'status' => 'error',
                    'error' => $e->getMessage(),
                ];
            }
            
            // Final counts
            $report['final_counts'] = [
                'chart_accounts' => \App\Models\ChartAccount::count(),
                'journal_entries' => \App\Models\JournalEntry::count(),
            ];
            
            return response()->json($report);
        } catch (\Exception $e) {
            Log::error('Bootstrap failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }
    
}
