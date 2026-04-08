<?php

namespace App\Models\Meetings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BoardMember extends Model
{
    protected $table = 'mt_board_members';

    protected $fillable = [
        'name_ar', 'name_en', 'position_ar', 'position_en',
        'phone', 'email', 'is_active', 'notes',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function boardMeetings(): BelongsToMany
    {
        return $this->belongsToMany(BoardMeeting::class, 'mt_board_meeting_attendees', 'board_member_id', 'board_meeting_id')
            ->withPivot('attended')
            ->withTimestamps();
    }
}
