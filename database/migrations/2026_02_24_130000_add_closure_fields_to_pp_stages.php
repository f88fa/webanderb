<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pp_stages', function (Blueprint $table) {
            $table->timestamp('closed_at')->nullable()->after('notes');
            $table->foreignId('closed_by')->nullable()->after('closed_at')->constrained('users')->nullOnDelete();
            $table->text('closure_reason')->nullable()->after('closed_by');
        });
    }

    public function down(): void
    {
        Schema::table('pp_stages', function (Blueprint $table) {
            $table->dropForeign(['closed_by']);
            $table->dropColumn(['closed_at', 'closed_by', 'closure_reason']);
        });
    }
};
