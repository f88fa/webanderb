<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    protected $fillable = [
        'subject',
        'description',
        'due_at',
        'created_by',
        'status',
        'closed_at',
        'closed_by',
        'evidence_path',
        'evidence_original_name',
    ];

    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function closedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_assignees')->withPivot('seen_at')->withTimestamps();
    }

    public function updates(): HasMany
    {
        return $this->hasMany(TaskUpdate::class, 'task_id')->orderByDesc('created_at');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    /** المستخدمون المعنيون بالمهمة: المنشئ + المسند إليهم */
    public function scopeConcernedByUser($query, $userId)
    {
        return $query->where('created_by', $userId)
            ->orWhereHas('assignees', fn ($q) => $q->where('users.id', $userId));
    }
}
