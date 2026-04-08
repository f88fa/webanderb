<?php

namespace App\Models\ProgramsProjects;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agreement extends Model
{
    protected $table = 'pp_agreements';

    protected $fillable = [
        'donor_id', 'project_id', 'agreement_no', 'title', 'amount',
        'start_date', 'end_date', 'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function grants(): HasMany
    {
        return $this->hasMany(Grant::class, 'agreement_id');
    }
}
