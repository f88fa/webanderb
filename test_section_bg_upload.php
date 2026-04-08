<?php
/**
 * اختبار رفع وعرض صور خلفية الأقسام
 * 
 * هذا الملف يفحص:
 * 1. أين يتم حفظ الصور
 * 2. ما هو المسار المحفوظ في قاعدة البيانات
 * 3. كيف يتم عرض الصور
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Storage;

echo "=== فحص صور خلفية الأقسام ===\n\n";

// 1. فحص مجلد الرفع
$uploadsPath = storage_path('app/public/uploads');
echo "1. مجلد الرفع:\n";
echo "   المسار: {$uploadsPath}\n";
echo "   موجود: " . (is_dir($uploadsPath) ? 'نعم ✅' : 'لا ❌') . "\n";
echo "   قابل للكتابة: " . (is_writable($uploadsPath) ? 'نعم ✅' : 'لا ❌') . "\n\n";

// 2. فحص الصور الموجودة
if (is_dir($uploadsPath)) {
    $files = glob($uploadsPath . '/section_*_bg_*');
    echo "2. صور الخلفية الموجودة:\n";
    if (count($files) > 0) {
        foreach ($files as $file) {
            $filename = basename($file);
            $size = filesize($file);
            echo "   ✅ {$filename} (" . number_format($size / 1024, 2) . " KB)\n";
        }
    } else {
        echo "   ⚠️  لا توجد صور خلفية\n";
    }
    echo "\n";
}

// 3. فحص القيم المحفوظة في قاعدة البيانات
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

echo "3. القيم المحفوظة في قاعدة البيانات:\n";
foreach ($bgImageKeys as $key) {
    $value = SiteSetting::getValue($key);
    if (!empty($value)) {
        echo "   {$key}:\n";
        echo "      القيمة: {$value}\n";
        
        // التحقق من نوع المسار
        if (strpos($value, '/private/') !== false || strpos($value, '/tmp/') !== false || strpos($value, '/var/folders/') !== false) {
            echo "      الحالة: ❌ مسار مؤقت خاطئ\n";
        } elseif (strpos($value, 'uploads/') === 0) {
            // فحص وجود الملف
            $fullPath = storage_path('app/public/' . $value);
            if (file_exists($fullPath)) {
                echo "      الحالة: ✅ مسار صحيح والملف موجود\n";
                echo "      المسار الكامل: {$fullPath}\n";
                echo "      URL المتوقع: " . asset('storage/' . $value) . "\n";
            } else {
                echo "      الحالة: ⚠️  مسار صحيح لكن الملف غير موجود\n";
                echo "      المسار الكامل: {$fullPath}\n";
            }
        } else {
            echo "      الحالة: ⚠️  مسار غير معروف\n";
        }
        echo "\n";
    }
}

// 4. اختبار دالة image_asset_url
echo "4. اختبار دالة image_asset_url:\n";
if (function_exists('image_asset_url')) {
    $testPaths = [
        'uploads/section_services_bg_1234567890_abc123.jpg',
        'section_services_bg_1234567890_abc123.jpg',
        '/private/var/folders/test',
        'storage/uploads/test.jpg',
    ];
    
    foreach ($testPaths as $testPath) {
        $url = image_asset_url($testPath);
        echo "   المدخل: {$testPath}\n";
        echo "   المخرج: {$url}\n";
        echo "\n";
    }
} else {
    echo "   ❌ الدالة image_asset_url غير موجودة\n";
}

// 5. فحص إعدادات Storage
echo "5. إعدادات Storage:\n";
$publicDisk = config('filesystems.disks.public');
echo "   Root: " . ($publicDisk['root'] ?? 'غير محدد') . "\n";
echo "   URL: " . ($publicDisk['url'] ?? 'غير محدد') . "\n";
echo "   Visibility: " . ($publicDisk['visibility'] ?? 'غير محدد') . "\n";

echo "\n=== انتهى الفحص ===\n";

