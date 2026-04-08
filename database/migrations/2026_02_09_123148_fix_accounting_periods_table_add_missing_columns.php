<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('accounting_periods', function (Blueprint $table) {
            // إضافة عمود status أولاً
            if (!Schema::hasColumn('accounting_periods', 'status')) {
                $table->enum('status', ['open', 'closed'])->default('open')->after('end_date');
            }
            
            // إضافة allow_posting إذا لم يكن موجوداً
            if (!Schema::hasColumn('accounting_periods', 'allow_posting')) {
                $table->boolean('allow_posting')->default(true)->after('status');
            }
            
            // إضافة allow_adjustments إذا لم يكن موجوداً
            if (!Schema::hasColumn('accounting_periods', 'allow_adjustments')) {
                $table->boolean('allow_adjustments')->default(true)->after('allow_posting');
            }
            
            // إضافة notes إذا لم يكن موجوداً
            if (!Schema::hasColumn('accounting_periods', 'notes')) {
                $table->text('notes')->nullable()->after('closed_by');
            }
        });
        
        // نسخ البيانات من is_open إلى status بعد إنشاء العمود
        if (Schema::hasColumn('accounting_periods', 'is_open') && Schema::hasColumn('accounting_periods', 'status')) {
            DB::statement("UPDATE accounting_periods SET status = IF(is_open = 1, 'open', 'closed') WHERE status IS NULL OR status = ''");
        }
        
        // حذف is_open بعد نسخ البيانات
        Schema::table('accounting_periods', function (Blueprint $table) {
            if (Schema::hasColumn('accounting_periods', 'is_open')) {
                $table->dropColumn('is_open');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounting_periods', function (Blueprint $table) {
            // إعادة is_open
            if (!Schema::hasColumn('accounting_periods', 'is_open')) {
                $table->boolean('is_open')->default(true)->after('end_date');
            }
            
            // نسخ البيانات من status إلى is_open
            if (Schema::hasColumn('accounting_periods', 'status')) {
                DB::statement("UPDATE accounting_periods SET is_open = IF(status = 'open', 1, 0)");
            }
            
            // حذف الأعمدة المضافة
            if (Schema::hasColumn('accounting_periods', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('accounting_periods', 'allow_posting')) {
                $table->dropColumn('allow_posting');
            }
            if (Schema::hasColumn('accounting_periods', 'allow_adjustments')) {
                $table->dropColumn('allow_adjustments');
            }
            if (Schema::hasColumn('accounting_periods', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};
