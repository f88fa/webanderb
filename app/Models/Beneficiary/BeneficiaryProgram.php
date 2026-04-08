<?php

namespace App\Models\Beneficiary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeneficiaryProgram extends Model
{
    protected $table = 'ben_beneficiary_programs';

    protected $fillable = ['beneficiary_id', 'program_id', 'joined_at', 'notes'];

    protected $casts = ['joined_at' => 'date'];

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class, 'beneficiary_id');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }
}
