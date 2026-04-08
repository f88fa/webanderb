<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'journal_entry_id',
        'account_id',
        'cost_center_id',
        'fund_id',
        'debit',
        'credit',
        'description',
        'reference',
        'line_order',
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'line_order' => 'integer',
    ];

    /**
     * العلاقة مع القيد
     */
    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    /**
     * العلاقة مع الحساب
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartAccount::class, 'account_id');
    }

    /**
     * العلاقة مع مركز التكلفة
     */
    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class, 'cost_center_id');
    }

    public function fund(): BelongsTo
    {
        return $this->belongsTo(Fund::class, 'fund_id');
    }

    /**
     * التحقق من صحة السطر
     */
    public function isValid(): bool
    {
        // يجب أن يكون إما debit أو credit فقط
        if ($this->debit > 0 && $this->credit > 0) {
            return false;
        }

        if ($this->debit == 0 && $this->credit == 0) {
            return false;
        }

        // التحقق من أن الحساب قابل للترحيل
        if (!$this->account->is_postable) {
            return false;
        }

        return true;
    }
}
