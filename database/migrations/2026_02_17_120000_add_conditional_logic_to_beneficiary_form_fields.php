<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('ben_beneficiary_form_fields', 'depends_on_field_id')) {
            Schema::table('ben_beneficiary_form_fields', function (Blueprint $table) {
                $table->foreignId('depends_on_field_id')->nullable()->after('sort_order')
                    ->constrained('ben_beneficiary_form_fields')->nullOnDelete();
                $table->string('depends_on_value', 255)->nullable()->after('depends_on_field_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('ben_beneficiary_form_fields', 'depends_on_field_id')) {
            Schema::table('ben_beneficiary_form_fields', function (Blueprint $table) {
                $table->dropForeign(['depends_on_field_id']);
                $table->dropColumn(['depends_on_field_id', 'depends_on_value']);
            });
        }
    }
};
