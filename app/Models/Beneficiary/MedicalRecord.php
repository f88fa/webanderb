<?php

namespace App\Models\Beneficiary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecord extends Model
{
    protected $table = 'ben_medical_records';

    protected $fillable = ['beneficiary_id', 'record_date', 'diagnosis', 'treatment', 'notes'];

    protected $casts = ['record_date' => 'date'];

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class, 'beneficiary_id');
    }
}
