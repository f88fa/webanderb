<?php

namespace App\Models\ProgramsProjects;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StageUpdateAttachment extends Model
{
    protected $table = 'pp_stage_update_attachments';

    protected $fillable = ['stage_update_id', 'file_path', 'original_name', 'uploaded_by'];

    public function stageUpdate(): BelongsTo
    {
        return $this->belongsTo(StageUpdate::class, 'stage_update_id');
    }

    public function uploadedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }
}
