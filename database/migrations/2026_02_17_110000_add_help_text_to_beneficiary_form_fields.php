<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ben_beneficiary_form_fields') && !Schema::hasColumn('ben_beneficiary_form_fields', 'help_text')) {
            Schema::table('ben_beneficiary_form_fields', function (Blueprint $table) {
                $table->string('help_text', 500)->nullable()->after('label_ar');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ben_beneficiary_form_fields') && Schema::hasColumn('ben_beneficiary_form_fields', 'help_text')) {
            Schema::table('ben_beneficiary_form_fields', function (Blueprint $table) {
                $table->dropColumn('help_text');
            });
        }
    }
};
