<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Letter extends Model
{
    protected $table = 'hr_letters';

    protected $fillable = ['employee_id', 'subject', 'letter_date', 'file_path', 'notes'];

    protected $casts = ['letter_date' => 'date'];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
