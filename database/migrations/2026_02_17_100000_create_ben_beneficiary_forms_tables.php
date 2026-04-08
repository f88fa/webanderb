<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ben_beneficiary_forms', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('slug', 80)->unique();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('ben_beneficiary_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_form_id')->constrained('ben_beneficiary_forms')->cascadeOnDelete();
            $table->string('field_key', 80);
            $table->string('label_ar');
            $table->string('field_type', 30)->default('text'); // text, email, number, date, select, textarea
            $table->boolean('is_required')->default(false);
            $table->json('options')->nullable(); // for select: ["خيار1","خيار2"]
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['beneficiary_form_id', 'field_key']);
        });

        Schema::create('ben_beneficiary_form_settings', function (Blueprint $table) {
            $table->string('key', 80)->primary();
            $table->string('value', 255)->nullable();
        });

        if (Schema::hasTable('ben_beneficiaries') && !Schema::hasColumn('ben_beneficiaries', 'beneficiary_form_id')) {
            Schema::table('ben_beneficiaries', function (Blueprint $table) {
                $table->foreignId('beneficiary_form_id')->nullable()->after('id')->constrained('ben_beneficiary_forms')->nullOnDelete();
                $table->json('form_data')->nullable()->after('notes');
            });
        }

        if (Schema::hasTable('ben_registration_requests')) {
            if (!Schema::hasColumn('ben_registration_requests', 'beneficiary_form_id')) {
                Schema::table('ben_registration_requests', function (Blueprint $table) {
                    $table->foreignId('beneficiary_form_id')->nullable()->after('id')->constrained('ben_beneficiary_forms')->nullOnDelete();
                });
            }
            if (!Schema::hasColumn('ben_registration_requests', 'form_data')) {
                Schema::table('ben_registration_requests', function (Blueprint $table) {
                    $table->json('form_data')->nullable()->after('notes');
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ben_beneficiaries') && Schema::hasColumn('ben_beneficiaries', 'beneficiary_form_id')) {
            Schema::table('ben_beneficiaries', function (Blueprint $table) {
                $table->dropForeign(['beneficiary_form_id']);
                $table->dropColumn(['beneficiary_form_id', 'form_data']);
            });
        }
        if (Schema::hasTable('ben_registration_requests')) {
            if (Schema::hasColumn('ben_registration_requests', 'beneficiary_form_id')) {
                Schema::table('ben_registration_requests', function (Blueprint $table) {
                    $table->dropForeign(['beneficiary_form_id']);
                    $table->dropColumn('beneficiary_form_id');
                });
            }
            if (Schema::hasColumn('ben_registration_requests', 'form_data')) {
                Schema::table('ben_registration_requests', function (Blueprint $table) {
                    $table->dropColumn('form_data');
                });
            }
        }
        Schema::dropIfExists('ben_beneficiary_form_settings');
        Schema::dropIfExists('ben_beneficiary_form_fields');
        Schema::dropIfExists('ben_beneficiary_forms');
    }
};
