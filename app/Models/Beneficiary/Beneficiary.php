<?php

namespace App\Models\Beneficiary;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Beneficiary extends Model
{
    protected $table = 'ben_beneficiaries';

    protected $fillable = [
        'user_id', 'beneficiary_form_id', 'beneficiary_no', 'name_ar', 'name_en', 'national_id', 'phone', 'email',
        'address', 'birth_date', 'gender', 'status', 'notes', 'form_data',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'form_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function beneficiaryForm(): BelongsTo
    {
        return $this->belongsTo(BeneficiaryForm::class, 'beneficiary_form_id');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(BeneficiaryRequest::class, 'beneficiary_id');
    }

    public function serviceRecords(): HasMany
    {
        return $this->hasMany(BeneficiaryService::class, 'beneficiary_id');
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class, 'beneficiary_id');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class, 'beneficiary_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(BeneficiaryDocument::class, 'beneficiary_id');
    }

    public function programEnrollments(): HasMany
    {
        return $this->hasMany(BeneficiaryProgram::class, 'beneficiary_id');
    }

    /** طلبات الصرف المرتبطة بهذا المستفيد (دعم مالي) */
    public function paymentRequests(): HasMany
    {
        return $this->hasMany(\App\Models\PaymentRequest::class, 'beneficiary_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    /**
     * اسم للترحيب في بوابة المستفيد: لا يعرض البريد إن وُجد اسم في الأعمدة أو form_data.
     */
    public function displayNameForPortal(): string
    {
        $emailNorm = strtolower(trim((string) ($this->email ?? '')));

        foreach ([trim((string) $this->name_ar), trim((string) $this->name_en)] as $candidate) {
            if ($candidate === '') {
                continue;
            }
            if (filter_var($candidate, FILTER_VALIDATE_EMAIL)) {
                continue;
            }
            if ($emailNorm !== '' && strtolower($candidate) === $emailNorm) {
                continue;
            }
            if (preg_match('/^\d{9,}$/', $candidate)) {
                continue;
            }

            return $candidate;
        }

        $formData = is_array($this->form_data) ? $this->form_data : [];
        foreach ($formData as $value) {
            if (! is_string($value)) {
                continue;
            }
            $t = trim($value);
            if ($t === '' || filter_var($t, FILTER_VALIDATE_EMAIL)) {
                continue;
            }
            if (preg_match('/^\d{9,}$/', $t)) {
                continue;
            }
            if (preg_match('#^storage/#i', $t) || preg_match('#^https?://#i', $t)) {
                continue;
            }
            if (preg_match('/\p{L}/u', $t)) {
                return $t;
            }
        }

        $userName = trim((string) ($this->user?->name ?? ''));
        if ($userName !== '' && ! filter_var($userName, FILTER_VALIDATE_EMAIL)) {
            if ($emailNorm === '' || strtolower($userName) !== $emailNorm) {
                return $userName;
            }
        }

        return $this->beneficiary_no
            ? 'مستفيد '.$this->beneficiary_no
            : 'مستفيد';
    }
}
