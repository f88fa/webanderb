<?php

namespace App\Models\Beneficiary;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeneficiaryRequest extends Model
{
    protected $table = 'ben_requests';

    protected $fillable = [
        'beneficiary_id', 'request_type', 'description', 'status',
        'submitted_at', 'studied_at', 'approved_at', 'approved_by',
        'rejected_at', 'rejection_reason', 'notes',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'studied_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class, 'beneficiary_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
