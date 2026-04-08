<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaSlide extends Model
{
    protected $table = 'media_slides';

    protected $fillable = [
        'type',
        'title',
        'description',
        'image',
        'video_url',
        'link',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get all active slides ordered
     */
    public static function getActiveOrdered()
    {
        return self::where('is_active', true)
            ->orderBy('order')
            ->get();
    }

    /**
     * Get all slides ordered (for admin)
     */
    public static function getAllOrdered()
    {
        return self::orderBy('order')->get();
    }

    /**
     * Extract YouTube video ID from URL
     */
    public function getYoutubeIdAttribute()
    {
        if (!$this->video_url) {
            return null;
        }
        
        $url = $this->video_url;
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches);
        return $matches[1] ?? null;
    }

    /**
     * Check if video is YouTube
     */
    public function isYouTube()
    {
        return $this->type === 'video' && $this->youtube_id !== null;
    }
}
