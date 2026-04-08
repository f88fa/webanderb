<?php

namespace App\Models\ProgramsProjects;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StageUpdate extends Model
{
    protected $table = 'pp_stage_updates';

    protected $fillable = [
        'stage_id', 'update_date', 'title', 'description', 'progress_percentage', 'updated_by',
    ];

    protected $casts = [
        'update_date' => 'date',
    ];

    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }

    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(StageUpdateAttachment::class, 'stage_update_id');
    }
}
