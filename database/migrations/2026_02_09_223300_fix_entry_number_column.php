<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * إصلاح عمود entry_number - جعله قابل للإلغاء أو إعطاؤه قيمة افتراضية
     * لأن التطبيق يستخدم entry_no و entry_number يسبب خطأ عند الإدراج
     */
    public function up(): void
    {
        if (Schema::hasColumn('journal_entries', 'entry_number')) {
            // جعل entry_number nullable حتى لا يمنع الإدراج
            DB::statement('ALTER TABLE journal_entries MODIFY COLUMN entry_number VARCHAR(50) NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('journal_entries', 'entry_number')) {
            DB::statement('ALTER TABLE journal_entries MODIFY COLUMN entry_number VARCHAR(50) NOT NULL');
        }
    }
};
