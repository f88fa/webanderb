<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * AboutUs Model
 * Migrated from Plain PHP: pages/about.php mysqli queries
 */
class AboutUs extends Model
{
    protected $table = 'about_us';
    
    protected $fillable = [
        'section_title',
        'title',
        'subtitle',
        'content',
        'image',
        'cta_text',
        'cta_link',
    ];

    /**
     * Get stats related to this about us
     */
    public function stats()
    {
        return $this->hasMany(AboutStat::class);
    }

    /**
     * Get the latest about us entry
     * Replaces: SELECT * FROM about_us ORDER BY id DESC LIMIT 1
     */
    public static function getLatest()
    {
        return self::latest('id')->first();
    }
}
