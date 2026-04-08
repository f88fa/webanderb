<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CostCenter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name_ar',
        'name_en',
        'description',
        'center_type',
        'status',
    ];

    /**
     * العلاقة مع سطور القيود
     */
    public function journalLines(): HasMany
    {
        return $this->hasMany(JournalLine::class, 'cost_center_id');
    }

    /**
     * Scope للمراكز النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /** برنامج، إداري، جمع تبرعات - معيار القطاع غير الربحي */
    public function scopeProgram($query)
    {
        return $query->where('center_type', 'program');
    }

    public function scopeAdministrative($query)
    {
        return $query->where('center_type', 'administrative');
    }

    public function scopeFundraising($query)
    {
        return $query->where('center_type', 'fundraising');
    }

    public function getCenterTypeNameArAttribute(): string
    {
        return match ($this->center_type ?? 'program') {
            'program' => 'برنامج',
            'administrative' => 'إداري',
            'fundraising' => 'جمع تبرعات',
            default => $this->center_type,
        };
    }
}
