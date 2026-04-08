<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FiscalYear extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'year_name',
        'start_date',
        'end_date',
        'status',
        'closed_at',
        'closed_by',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'closed_at' => 'datetime',
    ];

    /**
     * العلاقة مع المستخدم الذي أغلقه
     */
    public function closedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    /**
     * العلاقة مع الفترات المحاسبية
     */
    public function periods(): HasMany
    {
        return $this->hasMany(AccountingPeriod::class, 'fiscal_year_id')->orderBy('start_date');
    }

    /**
     * Scope للسنوات المفتوحة
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope للسنوات المغلقة
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    /**
     * التحقق من إمكانية الإقفال: تعتبر الفترة مغلقة إذا status = closed أو إذا الترحيل مغلق (allow_posting = false)
     */
    public function canBeClosed(): bool
    {
        $openCount = $this->periods()
            ->where('status', '!=', 'closed')
            ->where(function ($q) {
                $q->where('allow_posting', true);
            })
            ->count();

        return $openCount === 0;
    }

    /**
     * الحصول على الفترة الحالية (التي يقع فيها تاريخ اليوم)
     */
    public function getCurrentPeriodAttribute(): ?AccountingPeriod
    {
        return $this->periods()
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
    }

    /**
     * السنة المالية الحالية: التي يقع فيها تاريخ اليوم، أو آخر سنة مفتوحة
     */
    public static function getCurrent(): ?self
    {
        $today = now()->startOfDay();
        $current = static::where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->first();
        if ($current) {
            return $current;
        }
        return static::open()->orderBy('start_date', 'desc')->first();
    }
}
