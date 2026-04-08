<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InternalMessage extends Model
{
    protected $table = 'internal_messages';

    protected $fillable = [
        'from_user_id',
        'parent_id',
        'subject',
        'body',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(InternalMessage::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(InternalMessage::class, 'parent_id')->orderBy('created_at');
    }

    /** الرسالة الجذر في سلسلة الردود (الأصلية) */
    public function getRootMessage(): self
    {
        $current = $this;
        while ($current->parent_id) {
            $current = $current->parent ?? InternalMessage::find($current->parent_id);
            if (!$current) {
                return $this;
            }
        }
        return $current;
    }

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(InternalMessageRecipient::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(InternalMessageAttachment::class);
    }

    public function toRecipients(): HasMany
    {
        return $this->recipients()->where('type', 'to');
    }

    public function ccRecipients(): HasMany
    {
        return $this->recipients()->where('type', 'cc');
    }
}
