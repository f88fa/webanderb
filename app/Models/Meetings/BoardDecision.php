<?php

namespace App\Models\Meetings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoardDecision extends Model
{
    protected $table = 'mt_board_decisions';

    protected $fillable = [
        'decision_no', 'title', 'decision_date', 'board_meeting_id',
        'description', 'notes',
    ];

    protected $casts = ['decision_date' => 'date'];

    public function boardMeeting(): BelongsTo
    {
        return $this->belongsTo(BoardMeeting::class);
    }
}
