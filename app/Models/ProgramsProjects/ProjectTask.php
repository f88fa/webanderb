<?php

namespace App\Models\ProgramsProjects;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectTask extends Model
{
    protected $table = 'pp_project_tasks';

    protected $fillable = [
        'project_id', 'stage_id', 'name_ar', 'name_en', 'assignee_id',
        'due_date', 'status', 'priority', 'notes',
    ];

    protected $casts = ['due_date' => 'date'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }
}
