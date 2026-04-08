<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sentInternalMessages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InternalMessage::class, 'from_user_id');
    }

    public function internalMessageRecipients(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InternalMessageRecipient::class);
    }

    public function beneficiary(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\Beneficiary\Beneficiary::class);
    }

    public function registrationRequests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Beneficiary\RegistrationRequest::class);
    }

    /** الانتقال من المستخدم إلى الموظف المرتبط به */
    public function employee(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\HR\Employee::class, 'user_id');
    }

    /** الجهة المانحة المرتبطة بهذا المستخدم (عرض مشاريع الجهة فقط) */
    public function donor(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\ProgramsProjects\Donor::class, 'user_id');
    }

    public function isDonor(): bool
    {
        return $this->donor()->exists();
    }

    public function isBeneficiary(): bool
    {
        if (! Schema::hasTable('ben_beneficiaries') && ! Schema::hasTable('ben_registration_requests')) {
            return false;
        }

        return $this->beneficiary()->exists() || $this->registrationRequests()->whereIn('status', ['pending', 'rejected'])->exists();
    }

    /**
     * يمكنه الدخول إلى ويسال أو لوحة تحكم الموقع (موظف، مدير، جهة مانحة بصلاحيات، إلخ).
     */
    public function canAccessWesalStaff(): bool
    {
        try {
            if ($this->hasRole('SuperAdmin')) {
                return true;
            }
        } catch (\Throwable $e) {
            // قبل تشغيل جداول الأدوار
        }
        if ($this->employee()->exists()) {
            return true;
        }
        if ($this->donor()->exists()) {
            return true;
        }
        try {
            return $this->getAllPermissions()->isNotEmpty();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * مستفيد عبر البوابة فقط: ليس لديه أي مسار دخول للموظفين.
     */
    public function mustUseBeneficiaryPortalExclusively(): bool
    {
        if (! $this->isBeneficiary()) {
            return false;
        }

        return ! $this->canAccessWesalStaff();
    }
}
