<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contract extends Model
{
    protected $table = 'hr_contracts';

    protected $fillable = ['employee_id', 'contract_type', 'start_date', 'end_date', 'file_path', 'notes'];

    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
