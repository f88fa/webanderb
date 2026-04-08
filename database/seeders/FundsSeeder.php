<?php

namespace Database\Seeders;

use App\Models\Fund;
use Illuminate\Database\Seeder;

class FundsSeeder extends Seeder
{
    /**
     * أصناف أموال افتراضية للقطاع غير الربحي
     */
    public function run(): void
    {
        $funds = [
            [
                'code' => 'GUN',
                'name_ar' => 'مال عام غير مقيد',
                'name_en' => 'General Unrestricted',
                'restriction_type' => 'unrestricted',
                'description' => 'أموال للعمليات العامة دون قيود جهة مانحة',
                'status' => 'active',
            ],
            [
                'code' => 'GRS',
                'name_ar' => 'أموال مقيدة',
                'name_en' => 'Restricted',
                'restriction_type' => 'restricted',
                'description' => 'منح أو تبرعات مخصصة لبرنامج أو غرض محدد',
                'status' => 'active',
            ],
        ];

        foreach ($funds as $item) {
            Fund::firstOrCreate(
                ['code' => $item['code']],
                $item
            );
        }
    }
}
