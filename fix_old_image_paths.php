<?php
/**
 * إصلاح مسارات الصور القديمة في قاعدة البيانات
 * 
 * ارفع هذا الملف إلى: public_html/fix_old_image_paths.php
 * ثم افتحه في المتصفح
 * 
 * هذا الملف يفحص ويصلح المسارات القديمة في قاعدة البيانات
 */

header('Content-Type: text/html; charset=utf-8');

// تحميل Laravel
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$db = \Illuminate\Support\Facades\DB::connection();

// إذا كان هناك طلب إصلاح
$fixRequested = isset($_GET['fix']) && $_GET['fix'] === 'yes';

$results = [];

// الجداول التي تحتوي على صور
$tables = [
    'about_us' => ['image'],
    'news' => ['image'],
    'staff' => ['image'],
    'board_members' => ['image'],
    'partners' => ['logo'],
    'projects' => ['image'],
    'testimonials' => ['image'],
    'banner_sections' => ['image'],
    'media_videos' => ['thumbnail'],
    'media_slides' => ['image'],
    'policies' => ['file'],
    'regulations' => ['file'],
    'site_settings' => ['site_logo', 'site_icon_file', 'license_image', 'hero_background_image', 'section_about_bg_image', 'section_vision_mission_bg_image', 'section_services_bg_image', 'section_projects_bg_image', 'section_media_bg_image', 'section_testimonials_bg_image', 'section_partners_bg_image', 'section_news_bg_image', 'section_banner_sections_bg_image', 'section_staff_bg_image'],
];

?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إصلاح مسارات الصور القديمة</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            direction: rtl;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
            font-size: 2em;
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
        }
        .section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border-right: 4px solid #667eea;
        }
        .section h2 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 1.5em;
        }
        .test-item {
            margin: 10px 0;
            padding: 15px;
            background: white;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .test-item strong {
            color: #333;
            display: block;
            margin-bottom: 5px;
        }
        .success {
            color: #28a745;
            font-weight: bold;
        }
        .error {
            color: #dc3545;
            font-weight: bold;
        }
        .warning {
            color: #ffc107;
            font-weight: bold;
        }
        .info {
            color: #17a2b8;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            color: #e83e8c;
        }
        .fix-button {
            display: inline-block;
            margin-top: 20px;
            padding: 15px 30px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1.1em;
            transition: background 0.3s;
        }
        .fix-button:hover {
            background: #c82333;
        }
        .summary {
            background: #d4edda;
            border: 2px solid #28a745;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .summary h3 {
            color: #155724;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 إصلاح مسارات الصور القديمة</h1>

        <?php
        $totalFixed = 0;
        $totalChecked = 0;
        
        foreach ($tables as $tableName => $columns) {
            echo "<div class='section'>";
            echo "<h2>📋 جدول: $tableName</h2>";
            
            try {
                // التحقق من وجود الجدول
                if (!$db->getSchemaBuilder()->hasTable($tableName)) {
                    echo "<div class='test-item warning'>";
                    echo "⚠️ الجدول غير موجود";
                    echo "</div>";
                    echo "</div>";
                    continue;
                }
                
                foreach ($columns as $column) {
                    // التحقق من وجود العمود
                    if (!$db->getSchemaBuilder()->hasColumn($tableName, $column)) {
                        continue;
                    }
                    
                    echo "<div class='test-item'>";
                    echo "<strong>العمود: $column</strong><br>";
                    
                    // جلب جميع السجلات
                    $records = $db->table($tableName)->whereNotNull($column)->where($column, '!=', '')->get();
                    $totalChecked += $records->count();
                    
                    echo "عدد السجلات: <span class='info'>" . $records->count() . "</span><br>";
                    
                    $needsFix = [];
                    foreach ($records as $record) {
                        $oldPath = $record->$column;
                        
                        // التحقق من الصيغ المختلفة
                        $needsUpdate = false;
                        $newPath = $oldPath;
                        
                        // إذا كان يبدأ بـ storage/
                        if (strpos($oldPath, 'storage/') === 0) {
                            $newPath = str_replace('storage/', '', $oldPath);
                            $newPath = ltrim($newPath, '/');
                            $needsUpdate = true;
                        }
                        // إذا كان يبدأ بـ /storage/
                        elseif (strpos($oldPath, '/storage/') === 0) {
                            $newPath = str_replace('/storage/', '', $oldPath);
                            $newPath = ltrim($newPath, '/');
                            $needsUpdate = true;
                        }
                        // إذا كان مسار كامل (http/https)
                        elseif (strpos($oldPath, 'http://') === 0 || strpos($oldPath, 'https://') === 0) {
                            // استخراج اسم الملف فقط
                            $fileName = basename(parse_url($oldPath, PHP_URL_PATH));
                            if ($fileName && strpos($fileName, '.') !== false) {
                                $newPath = 'uploads/' . $fileName;
                                $needsUpdate = true;
                            }
                        }
                        
                        if ($needsUpdate && $newPath !== $oldPath) {
                            $needsFix[] = [
                                'id' => $record->id ?? null,
                                'old' => $oldPath,
                                'new' => $newPath,
                            ];
                        }
                    }
                    
                    if (count($needsFix) > 0) {
                        echo "<span class='warning'>⚠️ يحتاج إلى إصلاح: " . count($needsFix) . " سجل</span><br>";
                        echo "<div style='max-height: 200px; overflow-y: auto; background: #f8f9fa; padding: 10px; border-radius: 5px; margin-top: 10px;'>";
                        echo "<strong>أمثلة:</strong><ul style='margin-right: 20px;'>";
                        foreach (array_slice($needsFix, 0, 5) as $fix) {
                            echo "<li>";
                            echo "<code>" . htmlspecialchars($fix['old']) . "</code> → ";
                            echo "<code class='success'>" . htmlspecialchars($fix['new']) . "</code>";
                            echo "</li>";
                        }
                        if (count($needsFix) > 5) {
                            echo "<li>... و " . (count($needsFix) - 5) . " سجل آخر</li>";
                        }
                        echo "</ul></div>";
                        
                        // إذا طُلب الإصلاح
                        if ($fixRequested) {
                            $fixed = 0;
                            foreach ($needsFix as $fix) {
                                try {
                                    $db->table($tableName)
                                        ->where('id', $fix['id'])
                                        ->update([$column => $fix['new']]);
                                    $fixed++;
                                    $totalFixed++;
                                } catch (\Exception $e) {
                                    echo "<span class='error'>❌ خطأ في تحديث السجل ID: " . $fix['id'] . "</span><br>";
                                }
                            }
                            echo "<span class='success'>✅ تم إصلاح $fixed سجل</span>";
                        }
                    } else {
                        echo "<span class='success'>✅ لا يحتاج إلى إصلاح</span>";
                    }
                    
                    echo "</div>";
                }
            } catch (\Exception $e) {
                echo "<div class='test-item error'>";
                echo "❌ خطأ: " . htmlspecialchars($e->getMessage());
                echo "</div>";
            }
            
            echo "</div>";
        }
        
        if ($fixRequested && $totalFixed > 0) {
            echo "<div class='summary'>";
            echo "<h3>✅ تم الإصلاح بنجاح!</h3>";
            echo "<p>تم إصلاح <strong>$totalFixed</strong> سجل من أصل <strong>$totalChecked</strong> سجل تم فحصه.</p>";
            echo "<p>جميع المسارات الآن بصيغة صحيحة: <code>uploads/filename.jpg</code></p>";
            echo "</div>";
        } elseif (!$fixRequested) {
            echo "<div class='section'>";
            echo "<h2>🚀 الإصلاح</h2>";
            echo "<div class='test-item'>";
            echo "<strong>تم فحص $totalChecked سجل</strong><br>";
            if ($totalFixed > 0) {
                echo "<span class='warning'>⚠️ يوجد $totalFixed سجل يحتاج إلى إصلاح</span><br>";
                echo "<a href='?fix=yes' class='fix-button'>إصلاح جميع المسارات</a>";
            } else {
                echo "<span class='success'>✅ لا يوجد مسارات تحتاج إلى إصلاح</span>";
            }
            echo "</div>";
            echo "</div>";
        }
        ?>
        
        <div class="section">
            <h2>💡 ملاحظات</h2>
            <div class="test-item">
                <ul style="margin-right: 20px;">
                    <li>✅ هذا الملف يفحص جميع الجداول التي تحتوي على صور</li>
                    <li>✅ يحول المسارات من <code>storage/uploads/file.jpg</code> إلى <code>uploads/file.jpg</code></li>
                    <li>✅ يحول المسارات من <code>/storage/uploads/file.jpg</code> إلى <code>uploads/file.jpg</code></li>
                    <li>⚠️ <strong>احذر:</strong> الإصلاح دائم - تأكد من عمل نسخة احتياطية أولاً</li>
                    <li>✅ بعد الإصلاح، جميع الصور ستظهر بشكل صحيح</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>

