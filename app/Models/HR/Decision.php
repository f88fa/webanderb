<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Decision extends Model
{
    protected $table = 'hr_decisions';

    protected $fillable = ['employee_id', 'decision_type', 'decision_date', 'reference', 'file_path', 'notes'];

    protected $casts = ['decision_date' => 'date'];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
