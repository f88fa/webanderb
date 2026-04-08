<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payment_requests';

    protected $fillable = [
        'request_no',
        'request_date',
        'amount',
        'beneficiary_type',
        'beneficiary_employee_id',
        'beneficiary_id',
        'beneficiary',
        'description',
        'status',
        'approval_type',
        'period_id',
        'journal_entry_id',
        'created_by',
        'rejection_notes',
        'approved_by',
        'approved_at',
    ];

    public const BENEFICIARY_EMPLOYEE = 'employee';
    public const BENEFICIARY_ENTITY = 'entity';

    protected $casts = [
        'request_date' => 'date',
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_PAID = 'paid';

    public function period(): BelongsTo
    {
        return $this->belongsTo(AccountingPeriod::class, 'period_id');
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /** الموظف الذي وافق (إن رُبط حسابه بالمستخدم الموافق) لعرض الاسم والمسمى والتوقيع */
    public function getApproverEmployeeAttribute(): ?\App\Models\HR\Employee
    {
        if (!$this->approved_by) {
            return null;
        }
        return \App\Models\HR\Employee::where('user_id', $this->approved_by)->first();
    }

    public function beneficiaryEmployee(): BelongsTo
    {
        return $this->belongsTo(\App\Models\HR\Employee::class, 'beneficiary_employee_id');
    }

    /** مستفيد من قسم المستفيدين (ben_beneficiaries) عند الصرف لصالح مستفيد */
    public function beneficiaryBeneficiary(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Beneficiary\Beneficiary::class, 'beneficiary_id');
    }

    /** سجلات الدعم المرتبطة بهذا الطلب (دعم جماعي: عدة مستفيدين في طلب واحد) */
    public function beneficiaryServices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Beneficiary\BeneficiaryService::class, 'payment_request_id');
    }

    /**
     * اسم المستفيد للعرض: من نموذج التسجيل إن وُجد beneficiary_id، وإلا النص المخزّن (جهة، مجموعة، إلخ).
     */
    public function displayBeneficiaryName(): string
    {
        if ($this->beneficiary_id) {
            $b = $this->relationLoaded('beneficiaryBeneficiary')
                ? $this->beneficiaryBeneficiary
                : $this->beneficiaryBeneficiary()->first();
            if ($b) {
                return $b->displayNameForPortal();
            }
        }

        return (string) ($this->beneficiary ?? '');
    }

    public function attachments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PaymentRequestAttachment::class)->orderBy('id');
    }

    /** تسلسل الموافقات (خطوة بخطوة) للمخرج النهائي */
    public function approvals(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PaymentRequestApproval::class)->orderBy('step');
    }

    /**
     * قائمة الموافقين للعرض في المخرج النهائي: اسم، منصب، توقيع (أو فارغ).
     * إن وُجدت موافقات مسجلة تُستخدم، وإلا الموافق الوحيد (approved_by) إن وُجد.
     */
    public function getApproversForDisplayAttribute(): array
    {
        $list = [];
        $approvals = $this->approvals()->with(['approvedByUser', 'approverEmployee'])->get();
        if ($approvals->isNotEmpty()) {
            foreach ($approvals as $a) {
                $emp = $a->approver_employee;
                $list[] = [
                    'step' => $a->step,
                    'name_ar' => $emp?->name_ar ?? $a->approvedByUser?->name ?? '—',
                    'job_title' => $emp?->job_title ?? '',
                    'signature_path' => $emp?->signature_path,
                    'signature_url' => $emp ? $emp->signature_url : null,
                ];
            }
            return $list;
        }
        $single = $this->approver_employee;
        if ($single || $this->approvedByUser) {
            $list[] = [
                'step' => 1,
                'name_ar' => $single?->name_ar ?? $this->approvedByUser?->name ?? '—',
                'job_title' => $single?->job_title ?? '',
                'signature_path' => $single?->signature_path,
                'signature_url' => $single ? $single->signature_url : null,
            ];
        }
        return $list;
    }

    public static function generateRequestNo(): string
    {
        $year = date('Y');
        $last = static::withTrashed()
            ->where('request_no', 'like', "PR-{$year}-%")
            ->orderBy('request_no', 'desc')
            ->first();
        $num = $last ? (int) substr($last->request_no, -6) + 1 : 1;
        return sprintf('PR-%s-%06d', $year, $num);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'قيد الانتظار',
            self::STATUS_APPROVED => 'موافق عليه',
            self::STATUS_REJECTED => 'مرفوض',
            self::STATUS_PAID => 'تم الصرف',
            default => $this->status,
        };
    }
}
