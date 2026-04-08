<?php

namespace Database\Seeders;

use App\Models\VisionMission;
use Illuminate\Database\Seeder;

class VisionMissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if record already exists
        $existing = VisionMission::getLatest();
        
        if (!$existing) {
            VisionMission::create([
                'vision' => 'أن نكون المؤسسة الخيرية الرائدة في المنطقة، ونموذجاً يحتذى به في العمل الخيري والتطوعي، ونكون السباقين في تقديم المساعدات الإنسانية والتنموية للمحتاجين.',
                'mission' => 'نسعى إلى تقديم أفضل الخدمات الخيرية والتطوعية للمجتمع، من خلال برامج ومشاريع تنموية مستدامة، تعزز قيم التكافل الاجتماعي والتضامن الإنساني، وتساهم في بناء مجتمع أفضل.',
                'vision_icon' => 'fas fa-eye',
                'mission_icon' => 'fas fa-bullseye',
            ]);
            
            $this->command->info('تم إنشاء الرؤية والرسالة الافتراضية بنجاح!');
        } else {
            $this->command->info('الرؤية والرسالة موجودة بالفعل.');
        }
    }
}
