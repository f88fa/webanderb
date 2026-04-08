<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollRun extends Model
{
    protected $table = 'hr_payroll_runs';

    protected $fillable = ['month', 'year', 'status', 'paid_at'];

    protected $casts = ['month' => 'integer', 'year' => 'integer', 'paid_at' => 'datetime'];

    public function lines(): HasMany
    {
        return $this->hasMany(PayrollLine::class, 'payroll_run_id');
    }
}
