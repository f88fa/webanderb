<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * News Model
 * Migrated from Plain PHP: pages/news.php mysqli queries
 */
class News extends Model
{
    protected $table = 'news';
    
    protected $fillable = [
        'title',
        'content',
        'image',
        'status',
    ];

    /**
     * Get active news
     * Replaces: SELECT * FROM news WHERE status = 'active' ORDER BY created_at DESC LIMIT 6
     */
    public static function getActive($limit = 6)
    {
        return self::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get all news ordered by date
     * Replaces: SELECT * FROM news ORDER BY created_at DESC
     */
    public static function getAllOrdered()
    {
        return self::orderBy('created_at', 'desc')->get();
    }
}
