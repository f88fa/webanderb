<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisionMission extends Model
{
    protected $table = 'vision_mission';

    protected $fillable = [
        'section_title',
        'vision',
        'mission',
        'vision_icon',
        'mission_icon',
    ];

    /**
     * Get the latest vision and mission entry
     */
    public static function getLatest()
    {
        return self::latest('id')->first();
    }
}
