<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PolicyCategory extends Model
{
    protected $table = 'policies_categories';

    protected $fillable = [
        'name',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get policies for this category
     */
    public function policies()
    {
        return $this->hasMany(Policy::class, 'category_id')->orderBy('order');
    }

    /**
     * Get active policies for this category
     */
    public function activePolicies()
    {
        return $this->hasMany(Policy::class, 'category_id')
            ->where('is_active', true)
            ->orderBy('order');
    }

    /**
     * Get all active categories with their policies ordered
     */
    public static function getActiveWithPolicies()
    {
        return self::where('is_active', true)
            ->with(['activePolicies'])
            ->orderBy('order')
            ->get();
    }

    /**
     * Get all categories ordered (for admin)
     */
    public static function getAllOrdered()
    {
        return self::orderBy('order')->get();
    }
}
