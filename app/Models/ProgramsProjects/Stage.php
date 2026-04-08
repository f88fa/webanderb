<?php

namespace App\Models\ProgramsProjects;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stage extends Model
{
    protected $table = 'pp_stages';

    protected $fillable = [
        'project_id', 'name_ar', 'name_en', 'order', 'start_date', 'end_date', 'status', 'notes',
        'closed_at', 'closed_by', 'closure_reason',
    ];

    protected $casts = ['start_date' => 'date', 'end_date' => 'date', 'closed_at' => 'datetime'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class, 'stage_id');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(StageUpdate::class, 'stage_id')->orderBy('update_date')->orderBy('id');
    }

    public function closedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function isClosed(): bool
    {
        return $this->status === 'completed';
    }
}
