<?php
/**
 * إصلاح مسارات صور الخلفية الخاطئة
 * 
 * هذا الملف يحذف جميع المسارات المؤقتة الخاطئة من قاعدة البيانات
 * ويستبدلها بقيم فارغة
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SiteSetting;
use Illuminate\Support\Facades\DB;

echo "=== إصلاح مسارات صور الخلفية ===\n\n";

// قائمة مفاتيح صور الخلفية
$bgImageKeys = [
    'section_about_bg_image',
    'section_vision_mission_bg_image',
    'section_services_bg_image',
    'section_projects_bg_image',
    'section_media_bg_image',
    'section_testimonials_bg_image',
    'section_partners_bg_image',
    'section_news_bg_image',
    'section_banner_sections_bg_image',
    'section_staff_bg_image',
];

$fixedCount = 0;
$totalChecked = 0;

foreach ($bgImageKeys as $key) {
    $value = SiteSetting::getValue($key);
    $totalChecked++;
    
    if (empty($value)) {
        continue;
    }
    
    // التحقق من المسارات المؤقتة الخاطئة
    if (strpos($value, '/private/') !== false || 
        strpos($value, '/tmp/') !== false || 
        strpos($value, '/var/folders/') !== false ||
        strpos($value, 'php') === 0 ||
        (strpos($value, 'http://') === false && strpos($value, 'https://') === false && strpos($value, 'uploads/') !== 0)) {
        
        echo "❌ مسار خاطئ موجود: {$key}\n";
        echo "   المسار الحالي: {$value}\n";
        
        // حذف القيمة الخاطئة
        SiteSetting::setValue($key, '');
        
        echo "   ✅ تم حذف القيمة الخاطئة\n\n";
        $fixedCount++;
    } else {
        echo "✅ مسار صحيح: {$key} = {$value}\n";
    }
}

echo "\n=== النتيجة ===\n";
echo "تم فحص: {$totalChecked} إعداد\n";
echo "تم إصلاح: {$fixedCount} مسار خاطئ\n";
echo "\nالآن يمكنك رفع صور خلفية جديدة من صفحة الإعدادات.\n";

