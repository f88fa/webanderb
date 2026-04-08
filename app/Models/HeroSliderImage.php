<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroSliderImage extends Model
{
    protected $table = 'hero_slider_images';

    protected $fillable = [
        'image',
        'title',
        'description',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get all active slider images ordered by order
     */
    public static function getActiveOrdered()
    {
        return self::where('is_active', true)
            ->orderBy('order')
            ->get();
    }

    /**
     * Get all slider images ordered by order
     */
    public static function getAllOrdered()
    {
        return self::orderBy('order')->get();
    }
}
