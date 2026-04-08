<?php

namespace Database\Seeders;

use App\Models\AboutFeature;
use Illuminate\Database\Seeder;

class AboutFeaturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if features already exist
        if (AboutFeature::count() > 0) {
            $this->command->info('المميزات موجودة بالفعل!');
            return;
        }

        $features = [
            [
                'icon' => 'fas fa-check-circle',
                'title' => 'خبرة طويلة',
                'text' => 'أكثر من 15 عاماً من الخبرة في العمل الخيري',
                'order' => 0,
            ],
            [
                'icon' => 'fas fa-certificate',
                'title' => 'مسجلة رسمياً',
                'text' => 'مسجلة رسمياً لدى وزارة الموارد البشرية',
                'order' => 1,
            ],
            [
                'icon' => 'fas fa-eye',
                'title' => 'شفافية تامة',
                'text' => 'شفافية تامة في جميع التعاملات المالية',
                'order' => 2,
            ],
            [
                'icon' => 'fas fa-user-graduate',
                'title' => 'فريق متخصص',
                'text' => 'فريق متخصص ومؤهل من المتطوعين',
                'order' => 3,
            ],
        ];

        foreach ($features as $feature) {
            AboutFeature::create($feature);
        }

        $this->command->info('تم إنشاء المميزات الافتراضية بنجاح!');
    }
}
