<?php

namespace App\Models\Beneficiary;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrationRequest extends Model
{
    protected $table = 'ben_registration_requests';

    protected $fillable = [
        'user_id', 'beneficiary_form_id', 'name_ar', 'name_en', 'national_id', 'phone', 'email', 'password',
        'address', 'birth_date', 'gender', 'notes', 'form_data', 'status', 'beneficiary_id',
        'reviewed_by', 'reviewed_at', 'rejection_reason',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'birth_date' => 'date',
        'reviewed_at' => 'datetime',
        'form_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function beneficiaryForm(): BelongsTo
    {
        return $this->belongsTo(BeneficiaryForm::class, 'beneficiary_form_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
