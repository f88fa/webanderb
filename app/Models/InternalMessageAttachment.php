<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class InternalMessageAttachment extends Model
{
    protected $fillable = [
        'internal_message_id',
        'path',
        'original_name',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(InternalMessage::class, 'internal_message_id');
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }
}
