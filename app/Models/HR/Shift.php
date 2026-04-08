<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    protected $table = 'hr_shifts';

    protected $fillable = ['name_ar', 'start_time', 'end_time', 'break_minutes', 'is_active'];

    protected $casts = ['is_active' => 'boolean', 'break_minutes' => 'integer'];

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'shift_id');
    }

    public static function activeList()
    {
        return self::where('is_active', true)->orderBy('start_time')->get();
    }
}
