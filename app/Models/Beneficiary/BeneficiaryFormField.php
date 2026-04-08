<?php

namespace App\Models\Beneficiary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeneficiaryFormField extends Model
{
    protected $table = 'ben_beneficiary_form_fields';

    protected $fillable = [
        'beneficiary_form_id', 'field_key', 'label_ar', 'help_text', 'field_type',
        'is_required', 'options', 'sort_order',
        'depends_on_field_id', 'depends_on_value',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'options' => 'array',
        'sort_order' => 'integer',
    ];

    /** أنواع الحقول المعروضة في الواجهة */
    public const TYPES = [
        'text' => 'نص',
        'select' => 'خيار واحد',
        'multiselect' => 'خيار متعدد',
        'number' => 'رقم',
        'file' => 'مرفق',
    ];

    /** أنواع إضافية للتوافق مع النماذج القديمة */
    public const TYPES_LEGACY = [
        'email' => 'بريد إلكتروني',
        'date' => 'تاريخ',
        'radio' => 'خيار واحد (دوائر)',
        'textarea' => 'نص متعدد الأسطر',
    ];

    /** للحقل من نوع file: استخراج accept من options */
    public function getFileAcceptAttribute(): string
    {
        if ($this->field_type !== 'file' || ! is_array($this->options)) {
            return 'image/*,.pdf,.doc,.docx';
        }

        return $this->options['accept'] ?? 'image/*,.pdf,.doc,.docx';
    }

    /** للحقل من نوع file: الحد الأقصى بالميجابايت */
    public function getFileMaxMbAttribute(): int
    {
        if ($this->field_type !== 'file' || ! is_array($this->options)) {
            return 5;
        }

        return (int) ($this->options['max_mb'] ?? 5);
    }

    public function beneficiaryForm(): BelongsTo
    {
        return $this->belongsTo(BeneficiaryForm::class, 'beneficiary_form_id');
    }

    /** الحقل الذي يعتمد عليه هذا الحقل (خيار شرطي) */
    public function dependsOnField(): BelongsTo
    {
        return $this->belongsTo(BeneficiaryFormField::class, 'depends_on_field_id');
    }

    /** الحقول التي تظهر عندما يكون هذا الحقل = قيمة معينة */
    public function conditionalFields(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BeneficiaryFormField::class, 'depends_on_field_id');
    }

    public function isStandardKey(): bool
    {
        return in_array($this->field_key, BeneficiaryForm::STANDARD_KEYS, true);
    }

    /**
     * لحقول التاريخ: تقويم ميلادي (افتراضي) أو هجري — يُخزَّن في options['calendar'].
     */
    public function dateCalendar(): string
    {
        if ($this->field_type !== 'date' || ! is_array($this->options)) {
            return 'gregorian';
        }
        $c = $this->options['calendar'] ?? 'gregorian';

        return $c === 'hijri' ? 'hijri' : 'gregorian';
    }
}
