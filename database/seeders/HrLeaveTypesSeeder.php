<?php

namespace Database\Seeders;

use App\Models\HR\LeaveType;
use Illuminate\Database\Seeder;

class HrLeaveTypesSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name_ar' => 'إجازة سنوية', 'code' => 'ANNUAL', 'days_per_year' => 21, 'is_paid' => true],
            ['name_ar' => 'إجازة مرضية', 'code' => 'SICK', 'days_per_year' => 30, 'is_paid' => true],
            ['name_ar' => 'إجازة طارئة', 'code' => 'EMERGENCY', 'days_per_year' => 7, 'is_paid' => true],
        ];

        foreach ($types as $t) {
            LeaveType::firstOrCreate(['code' => $t['code']], $t);
        }
    }
}
