<?php

namespace App\Models\Meetings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeetingType extends Model
{
    protected $table = 'mt_meeting_types';

    protected $fillable = ['name_ar', 'name_en', 'description', 'sort_order'];

    public function staffMeetings(): HasMany
    {
        return $this->hasMany(StaffMeeting::class, 'meeting_type_id');
    }
}
