<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannerSection extends Model
{
    protected $table = 'banner_sections';

    protected static function booted(): void
    {
        static::created(function (BannerSection $banner) {
            SectionOrder::addBannerSection($banner->id, SectionOrder::bannerSectionName($banner));
        });
        static::deleted(function (BannerSection $banner) {
            SectionOrder::removeBannerSection($banner->id);
        });
    }

    protected $fillable = [
        'title',
        'image',
        'video',
        'video_url',
        'link',
        'order',
        'is_active',
        'background_type',
    ];

    /**
     * Extract YouTube video ID from URL (youtube.com/watch?v=ID, youtu.be/ID, youtube.com/embed/ID)
     */
    public function getYoutubeVideoIdAttribute(): ?string
    {
        $url = $this->video_url;
        if (empty($url)) {
            return null;
        }
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $m)) {
            return $m[1];
        }
        if (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $url, $m)) {
            return $m[1];
        }
        if (preg_match('/youtube\.com\/watch\?.*v=([a-zA-Z0-9_-]+)/', $url, $m)) {
            return $m[1];
        }
        return null;
    }

    /** Check if this banner uses a YouTube video */
    public function hasYoutubeVideo(): bool
    {
        return !empty($this->youtube_video_id);
    }

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get all active banner sections ordered
     */
    public static function getActiveOrdered()
    {
        return self::where('is_active', true)
            ->orderBy('order')
            ->get();
    }

    /**
     * Get all banner sections ordered (for admin)
     */
    public static function getAllOrdered()
    {
        return self::orderBy('order')->get();
    }
}
