<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegulationCategory extends Model
{
    protected $table = 'regulations_categories';

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
     * Get regulations for this category
     */
    public function regulations()
    {
        return $this->hasMany(Regulation::class, 'category_id')->orderBy('order');
    }

    /**
     * Get active regulations for this category
     */
    public function activeRegulations()
    {
        return $this->hasMany(Regulation::class, 'category_id')
            ->where('is_active', true)
            ->orderBy('order');
    }

    /**
     * Get all active categories with their regulations
     */
    public static function getActiveWithRegulations()
    {
        return self::where('is_active', true)
            ->with(['activeRegulations' => function($query) {
                $query->orderBy('order');
            }])
            ->orderBy('order')
            ->get();
    }

    /**
     * Get all categories ordered (for admin)
     */
    public static function getAllOrdered()
    {
        return self::with('regulations')->orderBy('order')->get();
    }
}
