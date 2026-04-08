<?php

namespace App\Models\Beneficiary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    protected $table = 'ben_programs';

    protected $fillable = ['name_ar', 'description', 'start_date', 'end_date', 'is_active'];

    protected $casts = ['start_date' => 'date', 'end_date' => 'date', 'is_active' => 'boolean'];

    public function enrollments(): HasMany
    {
        return $this->hasMany(BeneficiaryProgram::class, 'program_id');
    }
}
