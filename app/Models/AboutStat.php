<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutStat extends Model
{
    protected $table = 'about_stats';
    
    protected $fillable = [
        'icon',
        'number',
        'label',
        'order',
    ];

    /**
     * Get all stats ordered by order field
     */
    public static function getAllOrdered()
    {
        return self::orderBy('order', 'asc')->get();
    }
}
