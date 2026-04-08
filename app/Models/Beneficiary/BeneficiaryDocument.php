<?php

namespace App\Models\Beneficiary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeneficiaryDocument extends Model
{
    protected $table = 'ben_documents';

    protected $fillable = ['beneficiary_id', 'document_type', 'file_path', 'document_date', 'notes'];

    protected $casts = ['document_date' => 'date'];

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class, 'beneficiary_id');
    }
}
