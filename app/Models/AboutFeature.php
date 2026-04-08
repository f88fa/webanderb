<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutFeature extends Model
{
    protected $table = 'about_features';
    
    protected $fillable = [
        'icon',
        'title',
        'text',
        'order',
    ];

    /**
     * Get all features ordered by order field
     */
    public static function getAllOrdered()
    {
        return self::orderBy('order', 'asc')->get();
    }
}
