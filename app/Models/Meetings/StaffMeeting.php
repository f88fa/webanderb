<?php

namespace App\Models\Meetings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StaffMeeting extends Model
{
    protected $table = 'mt_staff_meetings';

    protected $fillable = [
        'meeting_no', 'title', 'meeting_date', 'location', 'meeting_type_id',
        'agenda', 'minutes', 'status', 'notes',
    ];

    protected $casts = ['meeting_date' => 'date'];

    public function meetingType(): BelongsTo
    {
        return $this->belongsTo(MeetingType::class);
    }

    /** الموظفون المدعوون للاجتماع */
    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\HR\Employee::class, 'mt_staff_meeting_attendees', 'staff_meeting_id', 'employee_id')
            ->withPivot('attended')
            ->withTimestamps();
    }
}
