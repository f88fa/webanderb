<?php

namespace App\Models\ProgramsProjects;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $table = 'pp_expenses';

    protected $fillable = ['project_id', 'description', 'amount', 'expense_date', 'category', 'notes'];

    protected $casts = ['expense_date' => 'date', 'amount' => 'decimal:2'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
