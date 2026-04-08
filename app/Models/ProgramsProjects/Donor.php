<?php

namespace App\Models\ProgramsProjects;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Donor extends Model
{
    protected $table = 'pp_donors';

    protected $fillable = ['name_ar', 'name_en', 'contact_name', 'phone', 'email', 'address', 'notes', 'user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'donor_id');
    }

    public function agreements(): HasMany
    {
        return $this->hasMany(Agreement::class, 'donor_id');
    }

    public function grants(): HasMany
    {
        return $this->hasMany(Grant::class, 'donor_id');
    }
}
