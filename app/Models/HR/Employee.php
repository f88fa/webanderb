<?php

namespace App\Models\HR;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $table = 'hr_employees';

    protected $fillable = [
        'department_id', 'direct_manager_id', 'user_id', 'employee_no', 'name_ar', 'name_en', 'email', 'phone',
        'national_id', 'hire_date', 'job_title', 'base_salary', 'status', 'signature_path',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'base_salary' => 'decimal:2',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** المدير المباشر (مرتبط بملف الموظف) */
    public function directManager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'direct_manager_id');
    }

    /** الموظفون الذين يتبعون له كمدير مباشر */
    public function directReports(): HasMany
    {
        return $this->hasMany(Employee::class, 'direct_manager_id');
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'employee_id');
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'employee_id');
    }

    public function leaveBalances(): HasMany
    {
        return $this->hasMany(LeaveBalance::class, 'employee_id');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'employee_id');
    }

    public function decisions(): HasMany
    {
        return $this->hasMany(Decision::class, 'employee_id');
    }

    public function letters(): HasMany
    {
        return $this->hasMany(Letter::class, 'employee_id');
    }

    public function advances(): HasMany
    {
        return $this->hasMany(Advance::class, 'employee_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /** رابط التوقيع الإلكتروني للعرض */
    public function getSignatureUrlAttribute(): ?string
    {
        if (empty($this->signature_path)) {
            return null;
        }
        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->signature_path);
    }
}
