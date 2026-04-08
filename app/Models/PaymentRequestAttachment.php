<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PaymentRequestAttachment extends Model
{
    protected $table = 'payment_request_attachments';

    protected $fillable = [
        'payment_request_id',
        'file_path',
        'original_name',
        'file_size',
        'mime_type',
    ];

    public function paymentRequest(): BelongsTo
    {
        return $this->belongsTo(PaymentRequest::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }
}
