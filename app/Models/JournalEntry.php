<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'entry_no',
        'entry_date',
        'description',
        'entry_type',
        'period_id',
        'status',
        'posted_at',
        'posted_by',
        'reversed_by',
        'reversed_at',
        'reversal_notes',
        'total_debit',
        'total_credit',
        'notes',
        'cash_account_id',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
        'posted_at' => 'datetime',
        'reversed_at' => 'datetime',
    ];

    /**
     * العلاقة مع الفترة المحاسبية
     */
    public function period(): BelongsTo
    {
        return $this->belongsTo(AccountingPeriod::class, 'period_id');
    }

    /**
     * العلاقة مع المستخدم الذي رحل القيد
     */
    public function postedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    /**
     * العلاقة مع المستخدم الذي عكس القيد
     */
    public function reversedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reversed_by');
    }

    /**
     * الحساب المختار كـ "حساب الصندوق/البنك" (يُخفى من طباعة السند).
     */
    public function cashAccount(): BelongsTo
    {
        return $this->belongsTo(ChartAccount::class, 'cash_account_id');
    }

    /**
     * العلاقة مع طلب الصرف (عند إنشاء سند صرف من طلب)
     */
    public function paymentRequest(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PaymentRequest::class, 'journal_entry_id');
    }

    /**
     * العلاقة مع سطور القيد
     */
    public function lines(): HasMany
    {
        return $this->hasMany(JournalLine::class, 'journal_entry_id')->orderBy('line_order');
    }

    /**
     * Scope للقيود المرحلة
     */
    public function scopePosted($query)
    {
        return $query->where('status', 'posted');
    }

    /**
     * Scope للقيود المسودة
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope حسب نوع القيد
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('entry_type', $type);
    }

    /**
     * التحقق من الاتزان
     */
    public function isBalanced(): bool
    {
        $difference = abs($this->total_debit - $this->total_credit);
        return $difference < 0.01; // دقة 0.01
    }

    /**
     * ترحيل القيد
     */
    public function post(): bool
    {
        if ($this->status !== 'draft') {
            return false;
        }

        if (!$this->isBalanced()) {
            return false;
        }

        if (!$this->period->canPost() && $this->entry_type !== 'adjusting') {
            return false;
        }

        if ($this->entry_type === 'adjusting' && !$this->period->canPostAdjustments()) {
            return false;
        }

        $this->status = 'posted';
        $this->posted_at = now();
        $this->posted_by = auth()->id();
        return $this->save();
    }

    /**
     * عكس القيد
     */
    public function reverse(string $notes = null): bool
    {
        if ($this->status !== 'posted') {
            return false;
        }

        $this->status = 'reversed';
        $this->reversed_at = now();
        $this->reversed_by = auth()->id();
        $this->reversal_notes = $notes;
        return $this->save();
    }

    /**
     * توليد رقم قيد تلقائي
     */
    public static function generateEntryNo(string $prefix = 'JE'): string
    {
        $year = date('Y');
        $lastEntry = static::where('entry_no', 'like', "{$prefix}-{$year}-%")
            ->orderBy('entry_no', 'desc')
            ->first();

        if ($lastEntry) {
            $lastNumber = (int) substr($lastEntry->entry_no, -6);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('%s-%s-%06d', $prefix, $year, $newNumber);
    }
}
