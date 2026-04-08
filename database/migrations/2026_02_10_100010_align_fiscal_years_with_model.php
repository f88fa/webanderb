<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * محاذاة جدول fiscal_years مع FiscalYear model (year_name, status)
     */
    public function up(): void
    {
        Schema::table('fiscal_years', function (Blueprint $table) {
            if (!Schema::hasColumn('fiscal_years', 'year_name') && Schema::hasColumn('fiscal_years', 'name')) {
                $table->string('year_name')->nullable()->after('id');
            }
            if (!Schema::hasColumn('fiscal_years', 'status')) {
                $table->enum('status', ['open', 'closed'])->default('open')->after('end_date');
            }
            if (!Schema::hasColumn('fiscal_years', 'notes')) {
                $table->text('notes')->nullable()->after('closed_by');
            }
            if (!Schema::hasColumn('fiscal_years', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // نسخ البيانات
        if (Schema::hasColumn('fiscal_years', 'name') && Schema::hasColumn('fiscal_years', 'year_name')) {
            DB::statement('UPDATE fiscal_years SET year_name = name WHERE year_name IS NULL');
        }
        if (Schema::hasColumn('fiscal_years', 'is_open') && Schema::hasColumn('fiscal_years', 'status')) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement("UPDATE fiscal_years SET status = IF(is_open = 1, 'open', 'closed') WHERE status IS NULL OR status = ''");
            } else {
                DB::statement("UPDATE fiscal_years SET status = CASE WHEN is_open = 1 THEN 'open' ELSE 'closed' END WHERE status IS NULL OR status = ''");
            }
        }

        // جعل year_name NOT NULL بعد النسخ (MySQL فقط - SQLite لا يدعم MODIFY)
        if (Schema::hasColumn('fiscal_years', 'year_name') && DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE fiscal_years MODIFY year_name VARCHAR(255) NOT NULL');
        }
    }

    public function down(): void
    {
        // لا نعكس
    }
};
