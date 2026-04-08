<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Regulation extends Model
{
    protected $table = 'regulations';

    protected $fillable = [
        'category_id',
        'title',
        'file',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get category for this regulation
     */
    public function category()
    {
        return $this->belongsTo(RegulationCategory::class, 'category_id');
    }
}
