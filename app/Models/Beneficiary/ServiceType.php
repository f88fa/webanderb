<?php

namespace App\Models\Beneficiary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceType extends Model
{
    protected $table = 'ben_service_types';

    protected $fillable = ['name_ar', 'description', 'is_active', 'is_financial', 'order'];

    protected $casts = ['is_active' => 'boolean', 'is_financial' => 'boolean', 'order' => 'integer'];

    public function beneficiaryServices(): HasMany
    {
        return $this->hasMany(BeneficiaryService::class, 'service_type_id');
    }

    public static function activeList()
    {
        return self::where('is_active', true)->orderBy('order')->get();
    }
}
