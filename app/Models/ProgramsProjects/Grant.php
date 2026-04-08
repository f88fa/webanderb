<?php

namespace App\Models\ProgramsProjects;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grant extends Model
{
    protected $table = 'pp_grants';

    protected $fillable = ['donor_id', 'project_id', 'agreement_id', 'amount', 'grant_date', 'notes'];

    protected $casts = ['grant_date' => 'date', 'amount' => 'decimal:2'];

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function agreement(): BelongsTo
    {
        return $this->belongsTo(Agreement::class);
    }
}
