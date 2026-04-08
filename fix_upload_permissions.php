<?php
/**
 * إصلاح صلاحيات مجلد الرفع
 * 
 * ارفع هذا الملف إلى: public_html/fix_upload_permissions.php
 * ثم افتحه في المتصفح
 */

header('Content-Type: text/html; charset=utf-8');

// تحميل Laravel
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
$projectRoot = dirname($docRoot);
$storagePath = $projectRoot . '/storage/app/public';
$uploadsPath = $storagePath . '/uploads';

?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إصلاح صلاحيات مجلد الرفع</title>
    <style>
        body { font-family: Arial; padding: 20px; direction: rtl; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #667eea; padding-bottom: 10px; }
        .section { margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 5px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        code { background: #eee; padding: 2px 5px; border-radius: 3px; }
        button { background: #667eea; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin-top: 10px; }
        button:hover { background: #5568d3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 إصلاح صلاحيات مجلد الرفع</h1>
        
        <?php
        $fixRequested = isset($_GET['fix']) && $_GET['fix'] === 'yes';
        
        echo "<div class='section'>";
        echo "<h2>1. فحص المجلدات</h2>";
        
        // Check storage/app/public
        $storageExists = is_dir($storagePath);
        $storageWritable = $storageExists && is_writable($storagePath);
        
        echo "<p><strong>storage/app/public:</strong> ";
        echo $storageExists ? "<span class='success'>✅ موجود</span>" : "<span class='error'>❌ غير موجود</span>";
        echo " | ";
        echo $storageWritable ? "<span class='success'>✅ قابل للكتابة</span>" : "<span class='error'>❌ غير قابل للكتابة</span>";
        echo "</p>";
        echo "<p>المسار: <code>$storagePath</code></p>";
        
        // Check uploads
        $uploadsExists = is_dir($uploadsPath);
        $uploadsWritable = $uploadsExists && is_writable($uploadsPath);
        
        echo "<p><strong>storage/app/public/uploads:</strong> ";
        echo $uploadsExists ? "<span class='success'>✅ موجود</span>" : "<span class='error'>❌ غير موجود</span>";
        echo " | ";
        echo $uploadsWritable ? "<span class='success'>✅ قابل للكتابة</span>" : "<span class='error'>❌ غير قابل للكتابة</span>";
        echo "</p>";
        echo "<p>المسار: <code>$uploadsPath</code></p>";
        
        echo "</div>";
        
        // Fix if requested
        if ($fixRequested) {
            echo "<div class='section'>";
            echo "<h2>2. محاولة الإصلاح</h2>";
            
            $fixed = [];
            $errors = [];
            
            // Create storage/app/public if not exists
            if (!$storageExists) {
                if (@mkdir($storagePath, 0755, true)) {
                    $fixed[] = "تم إنشاء storage/app/public";
                } else {
                    $errors[] = "فشل إنشاء storage/app/public";
                }
            }
            
            // Set permissions for storage/app/public
            if ($storageExists || is_dir($storagePath)) {
                if (@chmod($storagePath, 0755)) {
                    $fixed[] = "تم تعديل صلاحيات storage/app/public إلى 755";
                } else {
                    $errors[] = "فشل تعديل صلاحيات storage/app/public";
                }
            }
            
            // Create uploads if not exists
            if (!$uploadsExists) {
                if (@mkdir($uploadsPath, 0755, true)) {
                    $fixed[] = "تم إنشاء storage/app/public/uploads";
                } else {
                    $errors[] = "فشل إنشاء storage/app/public/uploads";
                }
            }
            
            // Set permissions for uploads
            if ($uploadsExists || is_dir($uploadsPath)) {
                if (@chmod($uploadsPath, 0755)) {
                    $fixed[] = "تم تعديل صلاحيات storage/app/public/uploads إلى 755";
                } else {
                    $errors[] = "فشل تعديل صلاحيات storage/app/public/uploads";
                }
            }
            
            if (!empty($fixed)) {
                echo "<h3 class='success'>✅ تم الإصلاح:</h3><ul>";
                foreach ($fixed as $msg) {
                    echo "<li>$msg</li>";
                }
                echo "</ul>";
            }
            
            if (!empty($errors)) {
                echo "<h3 class='error'>❌ أخطاء:</h3><ul>";
                foreach ($errors as $msg) {
                    echo "<li>$msg</li>";
                }
                echo "</ul>";
                echo "<p class='warning'>⚠️ إذا فشل الإصلاح التلقائي، قم بتشغيل هذه الأوامر يدوياً:</p>";
                echo "<code>chmod 755 storage/app/public</code><br>";
                echo "<code>chmod 755 storage/app/public/uploads</code>";
            }
            
            echo "</div>";
        } else {
            // Show fix button
            if (!$storageExists || !$storageWritable || !$uploadsExists || !$uploadsWritable) {
                echo "<div class='section'>";
                echo "<h2>2. الإصلاح</h2>";
                echo "<p class='warning'>⚠️ يوجد مشاكل في الصلاحيات. اضغط على الزر لإصلاحها:</p>";
                echo "<a href='?fix=yes'><button>إصلاح الصلاحيات</button></a>";
                echo "</div>";
            } else {
                echo "<div class='section'>";
                echo "<h2 class='success'>✅ كل شيء جاهز!</h2>";
                echo "<p>المجلدات موجودة وقابلة للكتابة. يمكنك رفع الصور الآن.</p>";
                echo "</div>";
            }
        }
        ?>
        
        <div class="section">
            <h2>💡 ملاحظات</h2>
            <ul>
                <li>✅ تأكد من أن المجلدات موجودة وقابلة للكتابة</li>
                <li>✅ الصلاحيات المطلوبة: 755 للمجلدات</li>
                <li>✅ إذا فشل الإصلاح التلقائي، استخدم cPanel File Manager أو SSH</li>
            </ul>
        </div>
    </div>
</body>
</html>

