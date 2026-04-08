<?php

namespace App\Models;

use App\Models\HR\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentRequestApproval extends Model
{
    protected $table = 'payment_request_approvals';

    protected $fillable = [
        'payment_request_id',
        'step',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'step' => 'integer',
        'approved_at' => 'datetime',
    ];

    public function paymentRequest(): BelongsTo
    {
        return $this->belongsTo(PaymentRequest::class);
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    /** الموظف الموافق (للاسم والمنصب والتوقيع) */
    public function approverEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approved_by', 'user_id');
    }

    /** للتوافق مع الكود الذي يستدعي approver_employee */
    public function getApproverEmployeeAttribute(): ?Employee
    {
        if ($this->relationLoaded('approverEmployee')) {
            return $this->getRelation('approverEmployee');
        }
        return Employee::where('user_id', $this->approved_by)->first();
    }
}
