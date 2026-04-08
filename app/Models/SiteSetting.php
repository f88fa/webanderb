<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * SiteSetting Model
 * Migrated from Plain PHP: config.php mysqli queries
 */
class SiteSetting extends Model
{
    protected $table = 'site_settings';
    
    protected $fillable = [
        'setting_key',
        'setting_value',
    ];

    /**
     * Get setting value by key
     * Replaces: $conn->query("SELECT setting_key, setting_value FROM site_settings")
     */
    public static function getValue($key, $default = null)
    {
        $setting = self::where('setting_key', $key)->first();
        return $setting ? $setting->setting_value : $default;
    }

    /**
     * Set setting value by key
     * Replaces: UPDATE site_settings SET setting_value = ? WHERE setting_key = ?
     */
    public static function setValue($key, $value)
    {
        return self::updateOrCreate(
            ['setting_key' => $key],
            ['setting_value' => $value]
        );
    }

    /**
     * Get all settings as array
     * Replaces: while ($row = $result->fetch_assoc()) { $settings[$row['setting_key']] = $row['setting_value']; }
     */
    public static function getAllAsArray()
    {
        $settings = self::pluck('setting_value', 'setting_key')->toArray();
        // Convert null values to empty strings for consistency
        return array_map(function($value) {
            return $value === null ? '' : $value;
        }, $settings);
    }
}
