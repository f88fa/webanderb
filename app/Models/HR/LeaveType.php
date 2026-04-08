<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    protected $table = 'hr_leave_types';

    protected $fillable = ['name_ar', 'code', 'days_per_year', 'is_paid', 'is_active'];

    protected $casts = ['is_paid' => 'boolean', 'is_active' => 'boolean', 'days_per_year' => 'integer'];

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'leave_type_id');
    }

    public function leaveBalances(): HasMany
    {
        return $this->hasMany(LeaveBalance::class, 'leave_type_id');
    }

    public static function activeList()
    {
        return self::where('is_active', true)->orderBy('code')->get();
    }
}
