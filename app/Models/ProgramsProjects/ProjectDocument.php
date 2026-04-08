<?php

namespace App\Models\ProgramsProjects;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectDocument extends Model
{
    protected $table = 'pp_documents';

    protected $fillable = ['project_id', 'title', 'file_path', 'document_type', 'document_date', 'notes'];

    protected $casts = ['document_date' => 'date'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
