<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pp_donors', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('notes')->constrained('users')->nullOnDelete()
                ->comment('حساب الدخول للجهة المانحة لمتابعة مشاريعها');
        });
    }

    public function down(): void
    {
        Schema::table('pp_donors', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }
};
