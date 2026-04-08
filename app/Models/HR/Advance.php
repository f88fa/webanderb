<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Advance extends Model
{
    protected $table = 'hr_advances';

    protected $fillable = ['employee_id', 'amount', 'request_date', 'status', 'deduct_months', 'notes'];

    protected $casts = ['amount' => 'decimal:2', 'request_date' => 'date', 'deduct_months' => 'integer'];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
