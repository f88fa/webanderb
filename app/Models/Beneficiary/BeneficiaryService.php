<?php

namespace App\Models\Beneficiary;

use App\Models\User;
use App\Models\PaymentRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeneficiaryService extends Model
{
    protected $table = 'ben_beneficiary_services';

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_EXECUTED = 'executed';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'beneficiary_id', 'service_type_id', 'request_id', 'program_id',
        'amount', 'service_date', 'notes', 'status',
        'payment_request_id', 'executed_at', 'executed_by',
    ];

    protected $casts = [
        'service_date' => 'date',
        'amount' => 'decimal:2',
        'executed_at' => 'datetime',
    ];

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class, 'beneficiary_id');
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(BeneficiaryRequest::class, 'request_id');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function paymentRequest(): BelongsTo
    {
        return $this->belongsTo(PaymentRequest::class, 'payment_request_id');
    }

    public function executedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'executed_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'قيد الانتظار',
            self::STATUS_APPROVED => 'معتمد',
            self::STATUS_EXECUTED => 'منفذ',
            self::STATUS_REJECTED => 'مرفوض',
            default => $this->status ?? '—',
        };
    }
}
