<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdminLetter extends Model
{
    use SoftDeletes;

    protected $table = 'admin_letters';

    protected $fillable = [
        'direction',
        'letter_no',
        'subject',
        'letter_date',
        'from_party',
        'to_party',
        'body',
        'reference_no',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'letter_date' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeOutgoing($query)
    {
        return $query->where('direction', 'outgoing');
    }

    public function scopeIncoming($query)
    {
        return $query->where('direction', 'incoming');
    }
}
