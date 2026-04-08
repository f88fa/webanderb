<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إضافة جميع الأعمدة الناقصة في جدول journal_entries
     */
    public function up(): void
    {
        $add = function (string $col, callable $fn) {
            if (!Schema::hasColumn('journal_entries', $col)) {
                Schema::table('journal_entries', $fn);
            }
        };
        $add('notes', fn (Blueprint $t) => $t->text('notes')->nullable());
        $add('status', fn (Blueprint $t) => $t->enum('status', ['draft', 'posted', 'reversed', 'void'])->default('draft'));
        $add('posted_at', fn (Blueprint $t) => $t->timestamp('posted_at')->nullable());
        $add('posted_by', fn (Blueprint $t) => $t->unsignedBigInteger('posted_by')->nullable());
        $add('reversed_by', fn (Blueprint $t) => $t->unsignedBigInteger('reversed_by')->nullable());
        $add('reversed_at', fn (Blueprint $t) => $t->timestamp('reversed_at')->nullable());
        $add('reversal_notes', fn (Blueprint $t) => $t->text('reversal_notes')->nullable());
        $add('total_debit', fn (Blueprint $t) => $t->decimal('total_debit', 15, 2)->default(0));
        $add('total_credit', fn (Blueprint $t) => $t->decimal('total_credit', 15, 2)->default(0));
        $add('deleted_at', fn (Blueprint $t) => $t->softDeletes());
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // لا نعكس - قد تفقد البيانات
    }
};
