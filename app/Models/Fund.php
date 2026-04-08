<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * الصنف المالي / المال - محاسبة الأموال في القطاع غير الربحي
 * (أموال غير مقيدة، مقيدة، أوقاف)
 */
class Fund extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name_ar',
        'name_en',
        'restriction_type',
        'description',
        'status',
    ];

    public function journalLines(): HasMany
    {
        return $this->hasMany(JournalLine::class, 'fund_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeUnrestricted($query)
    {
        return $query->where('restriction_type', 'unrestricted');
    }

    public function scopeRestricted($query)
    {
        return $query->where('restriction_type', 'restricted');
    }

    public function getRestrictionTypeNameArAttribute(): string
    {
        return match ($this->restriction_type) {
            'unrestricted' => 'غير مقيد',
            'restricted' => 'مقيد',
            'endowment' => 'وقف',
            default => $this->restriction_type,
        };
    }
}
