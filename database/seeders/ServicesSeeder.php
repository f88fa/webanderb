<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing services
        Service::truncate();

        // Create default services
        Service::create([
            'title' => 'المساعدات الإنسانية',
            'description' => 'تقديم المساعدات الإنسانية للمحتاجين والأسر الفقيرة في مختلف المجالات',
            'icon' => 'fas fa-hand-holding-heart',
            'order' => 1,
            'is_active' => true,
        ]);

        Service::create([
            'title' => 'الرعاية الصحية',
            'description' => 'توفير الرعاية الصحية والعلاج للمرضى والمحتاجين',
            'icon' => 'fas fa-heartbeat',
            'order' => 2,
            'is_active' => true,
        ]);

        Service::create([
            'title' => 'التعليم والتدريب',
            'description' => 'دعم التعليم والتدريب المهني للأطفال والشباب',
            'icon' => 'fas fa-graduation-cap',
            'order' => 3,
            'is_active' => true,
        ]);

        Service::create([
            'title' => 'المشاريع التنموية',
            'description' => 'تنفيذ مشاريع تنموية مستدامة لتحسين حياة المجتمع',
            'icon' => 'fas fa-project-diagram',
            'order' => 4,
            'is_active' => true,
        ]);

        Service::create([
            'title' => 'دعم الأيتام',
            'description' => 'رعاية ودعم الأيتام والأطفال المحتاجين',
            'icon' => 'fas fa-child',
            'order' => 5,
            'is_active' => true,
        ]);

        Service::create([
            'title' => 'التطوع والمشاركة',
            'description' => 'تنظيم برامج التطوع والمشاركة المجتمعية',
            'icon' => 'fas fa-users',
            'order' => 6,
            'is_active' => true,
        ]);

        $this->command->info('تم إنشاء الخدمات الافتراضية بنجاح!');
    }
}
