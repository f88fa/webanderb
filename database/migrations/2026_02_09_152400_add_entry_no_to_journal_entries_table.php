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
        // إضافة عمود entry_no إذا لم يكن موجوداً
        if (!Schema::hasColumn('journal_entries', 'entry_no')) {
            Schema::table('journal_entries', function (Blueprint $table) {
                // إذا كان entry_number موجوداً، نضيف entry_no كعمود nullable أولاً
                if (Schema::hasColumn('journal_entries', 'entry_number')) {
                    $table->string('entry_no')->nullable()->unique()->after('entry_number');
                } else {
                    $table->string('entry_no')->unique()->after('id');
                }
            });
            
            // بعد إضافة العمود، نسخ البيانات من entry_number إلى entry_no
            if (Schema::hasColumn('journal_entries', 'entry_number')) {
                DB::statement('UPDATE journal_entries SET entry_no = entry_number WHERE entry_number IS NOT NULL');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            if (Schema::hasColumn('journal_entries', 'entry_no')) {
                $table->dropColumn('entry_no');
            }
        });
    }
};
