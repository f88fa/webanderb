<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoardMember extends Model
{
    protected $table = 'board_members';

    protected $fillable = [
        'name',
        'position',
        'image',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get all active board members ordered
     */
    public static function getActiveOrdered()
    {
        return self::where('is_active', true)
            ->orderBy('order')
            ->get();
    }

    /**
     * Get all board members ordered (for admin)
     */
    public static function getAllOrdered()
    {
        return self::orderBy('order')->get();
    }
}
