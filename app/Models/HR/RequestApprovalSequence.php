<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestApprovalSequence extends Model
{
    protected $table = 'hr_request_approval_sequences';

    protected $fillable = [
        'request_type',
        'step',
        'approver_type',
        'role_name',
        'employee_id',
    ];

    protected $casts = [
        'step' => 'integer',
    ];

    public const TYPES = [
        'leave' => 'طلب إجازة',
        'permission' => 'طلب إذن',
        'financial' => 'طلب مالي',
        'beneficiary_support' => 'طلب دعم المستفيدين (الخدمات والمساندة)',
        'general' => 'طلب عام',
    ];

    public const APPROVER_DIRECT_MANAGER = 'direct_manager';
    public const APPROVER_ROLE = 'role';
    public const APPROVER_EMPLOYEE = 'employee';

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /** الحصول على تسلسل الموافقات لنوع طلب معيّن */
    public static function getForType(string $requestType): \Illuminate\Database\Eloquent\Collection
    {
        return static::with('employee')->where('request_type', $requestType)->orderBy('step')->get();
    }

    /** تسمية الموافق للعرض (مدير مباشر / دور / اسم موظف) */
    public function getApproverDisplayAttribute(): string
    {
        if ($this->approver_type === self::APPROVER_DIRECT_MANAGER) {
            return 'المدير المباشر';
        }
        if ($this->approver_type === self::APPROVER_ROLE && $this->role_name) {
            return \App\Services\PermissionsRegistry::getRoleLabelAr($this->role_name);
        }
        if ($this->approver_type === self::APPROVER_EMPLOYEE && $this->relationLoaded('employee') && $this->employee) {
            return $this->employee->name_ar ?? ('موظف #' . $this->employee_id);
        }
        if ($this->approver_type === self::APPROVER_EMPLOYEE && $this->employee_id) {
            $emp = Employee::find($this->employee_id);
            return $emp ? $emp->name_ar : ('موظف #' . $this->employee_id);
        }
        return '—';
    }

    /** بناء التسلسل الافتراضي: الخطوة 1 مدير مباشر فقط */
    public static function defaultSteps(): array
    {
        return [
            ['step' => 1, 'approver_type' => self::APPROVER_DIRECT_MANAGER, 'role_name' => null, 'employee_id' => null],
        ];
    }

    /**
     * التحقق من أن المستخدم الحالي هو الموافق المطلوب لهذه الخطوة (لطلب صرف).
     * يُستخدم لتسلسل الموافقات على الطلب المالي.
     * إذا تعذّر تحديد المدير المباشر (منشئ الطلب غير مرتبط بموظف أو بلا مدير) يُسمح لأي مستخدم لديه صلاحية الاعتماد.
     */
    public function isApprovedByUser(\App\Models\PaymentRequest $paymentRequest, \App\Models\User $user): bool
    {
        if ($this->approver_type === self::APPROVER_DIRECT_MANAGER) {
            $creatorEmployee = \App\Models\HR\Employee::where('user_id', $paymentRequest->created_by)->first();
            $manager = $creatorEmployee?->directManager;
            if ($manager && $manager->user_id && (int) $manager->user_id === (int) $user->id) {
                return true;
            }
            // إذا لم يُعثر على مدير مباشر (منشئ الطلب غير موظف أو بلا مدير) نسمح لأي مستخدم لديه صلاحية اعتماد طلبات الصرف
            if (!$manager || !$manager->user_id) {
                return $user->can('finance.payment_requests.approve');
            }
            return false;
        }
        if ($this->approver_type === self::APPROVER_ROLE && $this->role_name) {
            return $user->hasRole($this->role_name);
        }
        if ($this->approver_type === self::APPROVER_EMPLOYEE && $this->employee_id) {
            $emp = $this->relationLoaded('employee') ? $this->employee : Employee::find($this->employee_id);
            return $emp && $emp->user_id && (int) $emp->user_id === (int) $user->id;
        }
        return false;
    }
}
