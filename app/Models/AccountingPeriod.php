<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountingPeriod extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fiscal_year_id',
        'period_name',
        'start_date',
        'end_date',
        'status',
        'allow_posting',
        'allow_adjustments',
        'closed_at',
        'closed_by',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'allow_posting' => 'boolean',
        'allow_adjustments' => 'boolean',
        'closed_at' => 'datetime',
    ];

    /**
     * العلاقة مع السنة المالية
     */
    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id');
    }

    /**
     * العلاقة مع المستخدم الذي أغلقه
     */
    public function closedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    /**
     * العلاقة مع القيود
     */
    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'period_id');
    }

    /**
     * Scope للفترات المفتوحة
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope للفترات المغلقة
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    /**
     * Scope للفترات التي تسمح بالترحيل
     */
    public function scopeAllowPosting($query)
    {
        return $query->where('allow_posting', true);
    }

    /**
     * Scope للفترات التي تسمح بالتسويات
     */
    public function scopeAllowAdjustments($query)
    {
        return $query->where('allow_adjustments', true);
    }

    /**
     * التحقق من إمكانية الترحيل العادي
     */
    public function canPost(): bool
    {
        return $this->status === 'open' && $this->allow_posting;
    }

    /**
     * التحقق من إمكانية ترحيل قيود التسوية
     */
    public function canPostAdjustments(): bool
    {
        return $this->status === 'open' && $this->allow_adjustments;
    }

    /**
     * التحقق من أن التاريخ ضمن الفترة
     */
    public function containsDate($date): bool
    {
        $date = is_string($date) ? \Carbon\Carbon::parse($date) : $date;
        return $date->between($this->start_date, $this->end_date);
    }

    /**
     * الحصول على الفترة حسب التاريخ
     */
    public static function findByDate($date): ?self
    {
        $date = is_string($date) ? \Carbon\Carbon::parse($date) : $date;
        return static::where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first();
    }

    /**
     * الفترة المحاسبية الحالية (التي يقع فيها تاريخ اليوم)
     */
    public static function getCurrent(): ?self
    {
        return static::findByDate(now());
    }
}
