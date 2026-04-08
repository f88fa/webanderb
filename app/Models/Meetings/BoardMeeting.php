<?php

namespace App\Models\Meetings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BoardMeeting extends Model
{
    protected $table = 'mt_board_meetings';

    protected $fillable = [
        'meeting_no', 'title', 'meeting_date', 'location',
        'agenda', 'minutes', 'status', 'notes',
    ];

    protected $casts = ['meeting_date' => 'date'];

    public function boardMembers(): BelongsToMany
    {
        return $this->belongsToMany(BoardMember::class, 'mt_board_meeting_attendees', 'board_meeting_id', 'board_member_id')
            ->withPivot('attended')
            ->withTimestamps();
    }

    public function decisions(): HasMany
    {
        return $this->hasMany(BoardDecision::class, 'board_meeting_id');
    }
}
