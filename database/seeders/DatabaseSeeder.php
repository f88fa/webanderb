<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed default site settings
        $this->call(SiteSettingsSeeder::class);
        
        // Seed admin user
        $this->call(AdminUserSeeder::class);
        
        // Seed about stats
        $this->call(AboutStatsSeeder::class);
        
        // Seed about features
        $this->call(AboutFeaturesSeeder::class);
        
        // Seed vision and mission
        $this->call(VisionMissionSeeder::class);
        
        // Seed services
        $this->call(ServicesSeeder::class);

        // HR leave types (إجازة سنوية، مرضية، طارئة)
        $this->call(HrLeaveTypesSeeder::class);

        // صلاحيات Wesal والأدوار
        $this->call(WesalPermissionsSeeder::class);
    }
}
