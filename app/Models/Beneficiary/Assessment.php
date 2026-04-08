<?php

namespace App\Models\Beneficiary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assessment extends Model
{
    protected $table = 'ben_assessments';

    protected $fillable = ['beneficiary_id', 'assessment_date', 'eligibility_score', 'notes'];

    protected $casts = ['assessment_date' => 'date', 'eligibility_score' => 'decimal:2'];

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class, 'beneficiary_id');
    }
}
