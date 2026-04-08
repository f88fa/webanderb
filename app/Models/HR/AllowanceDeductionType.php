<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class AllowanceDeductionType extends Model
{
    protected $table = 'hr_allowance_deduction_types';

    protected $fillable = ['name_ar', 'type', 'is_fixed', 'default_amount', 'is_active'];

    protected $casts = ['is_fixed' => 'boolean', 'is_active' => 'boolean', 'default_amount' => 'decimal:2'];

    public static function allowances()
    {
        return self::where('type', 'allowance')->where('is_active', true)->get();
    }

    public static function deductions()
    {
        return self::where('type', 'deduction')->where('is_active', true)->get();
    }
}
