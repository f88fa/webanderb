<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteSetting;

/**
 * SiteSettingsSeeder
 * Migrated from Plain PHP: config.php createTables() default_settings
 * Seeds default site settings
 */
class SiteSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Replaces: config.php foreach ($default_settings as $setting) { INSERT IGNORE... }
     */
    public function run(): void
    {
        $defaultSettings = [
            ['site_title', 'لوحة التحكم'],
            ['site_description', 'لوحة تحكم احترافية'],
            ['site_logo', ''],
            ['site_icon', 'fas fa-rocket'],
            ['contact_email', 'info@example.com'],
            ['contact_phone', '+966500000000'],
        ];

        foreach ($defaultSettings as $setting) {
            SiteSetting::updateOrCreate(
                ['setting_key' => $setting[0]],
                ['setting_value' => $setting[1]]
            );
        }
    }
}
