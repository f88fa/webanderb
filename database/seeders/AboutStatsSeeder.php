<?php

namespace Database\Seeders;

use App\Models\AboutStat;
use Illuminate\Database\Seeder;

class AboutStatsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if stats already exist
        if (AboutStat::count() > 0) {
            $this->command->info('الإحصائيات موجودة بالفعل!');
            return;
        }

        $stats = [
            [
                'icon' => 'fas fa-calendar-alt',
                'number' => '+15',
                'label' => 'سنة من العطاء',
                'order' => 0,
            ],
            [
                'icon' => 'fas fa-users',
                'number' => '500+',
                'label' => 'متطوع نشط',
                'order' => 1,
            ],
            [
                'icon' => 'fas fa-hand-holding-heart',
                'number' => '1000+',
                'label' => 'مشروع منجز',
                'order' => 2,
            ],
        ];

        foreach ($stats as $stat) {
            AboutStat::create($stat);
        }

        $this->command->info('تم إنشاء الإحصائيات الافتراضية بنجاح!');
    }
}
