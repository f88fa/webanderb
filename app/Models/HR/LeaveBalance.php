<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBalance extends Model
{
    protected $table = 'hr_leave_balances';

    protected $fillable = ['employee_id', 'leave_type_id', 'year', 'balance', 'used'];

    protected $casts = ['year' => 'integer', 'balance' => 'decimal:2', 'used' => 'decimal:2'];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    public function getRemainingAttribute(): float
    {
        return (float) $this->balance - (float) $this->used;
    }
}
