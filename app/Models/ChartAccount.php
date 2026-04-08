<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChartAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'chart_accounts';

    protected $fillable = [
        'code',
        'name_ar',
        'name_en',
        'parent_id',
        'level',
        'type',
        'nature',
        'is_postable',
        'is_fixed',
        'status',
        'description',
    ];

    protected $casts = [
        'level' => 'integer',
        'is_postable' => 'boolean',
        'is_fixed' => 'boolean',
    ];

    /**
     * العلاقة مع الحساب الأب
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ChartAccount::class, 'parent_id');
    }

    /**
     * العلاقة مع الحسابات الفرعية
     */
    public function children(): HasMany
    {
        return $this->hasMany(ChartAccount::class, 'parent_id')->orderBy('code');
    }

    /**
     * العلاقة مع سطور القيود
     */
    public function journalLines(): HasMany
    {
        return $this->hasMany(JournalLine::class, 'account_id');
    }

    /**
     * Scope للحسابات النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope للحسابات القابلة للترحيل
     */
    public function scopePostable($query)
    {
        return $query->where('is_postable', true);
    }

    /**
     * Scope للحسابات الثابتة
     */
    public function scopeFixed($query)
    {
        return $query->where('is_fixed', true);
    }

    /**
     * Scope للحسابات حسب النوع
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * التحقق من وجود حسابات فرعية
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    /**
     * الحصول على المسار الكامل للحساب
     */
    public function getFullPathAttribute(): string
    {
        $path = [$this->name_ar];
        $parent = $this->parent;
        
        while ($parent) {
            array_unshift($path, $parent->name_ar);
            $parent = $parent->parent;
        }
        
        return implode(' > ', $path);
    }

    /**
     * الحصول على الكود الكامل مع الأب
     */
    public function getFullCodeAttribute(): string
    {
        $codes = [$this->code];
        $parent = $this->parent;
        
        while ($parent) {
            array_unshift($codes, $parent->code);
            $parent = $parent->parent;
        }
        
        return implode('.', $codes);
    }
}
