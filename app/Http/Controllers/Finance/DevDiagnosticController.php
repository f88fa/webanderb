<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\ChartAccount;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class DevDiagnosticController extends Controller
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
     * Diagnostic endpoint
     */
    public function diagnostic(Request $request)
    {
        try {
            $dbConfig = Config::get('database.connections.mysql');
            
            // Counts
            $chartAccountsCount = ChartAccount::count();
            $journalEntriesCount = JournalEntry::count();
            
            // Sample accounts
            $sampleAccounts = ChartAccount::limit(5)->get(['id', 'code', 'name_ar', 'parent_id', 'level', 'is_postable', 'is_fixed']);
            
            // Last migrations
            $lastMigrations = DB::table('migrations')
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get(['id', 'migration', 'batch']);
            
            // Check if database is accessible
            $dbConnected = true;
            try {
                DB::connection()->getPdo();
            } catch (\Exception $e) {
                $dbConnected = false;
            }
            
            return response()->json([
                'success' => true,
                'database' => [
                    'name' => $dbConfig['database'] ?? 'unknown',
                    'host' => $dbConfig['host'] ?? 'unknown',
                    'username' => $dbConfig['username'] ?? 'unknown',
                    'connected' => $dbConnected,
                ],
                'counts' => [
                    'chart_accounts' => $chartAccountsCount,
                    'journal_entries' => $journalEntriesCount,
                ],
                'sample_accounts' => $sampleAccounts,
                'last_migrations' => $lastMigrations,
                'timestamp' => now()->toDateTimeString(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }
}
