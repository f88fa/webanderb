<?php
/**
 * اختبار شامل لرفع الصور
 * 
 * ارفع هذا الملف إلى: public_html/test_upload_complete.php
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

// إذا كان هناك طلب رفع
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_image'])) {
    try {
        $file = $request->file('test_image');
        
        if ($file && $file->isValid()) {
            $imageName = 'test_' . time() . '.' . $file->getClientOriginalExtension();
            $imagePath = $file->storeAs('uploads', $imageName, 'public');
            
            $fullPath = storage_path('app/public/' . $imagePath);
            $exists = file_exists($fullPath);
            
            // Extract just the filename for URL
            $fileName = basename($imagePath);
            $urlPath = 'uploads/' . $fileName;
            
            $result = [
                'success' => $exists,
                'message' => $exists ? '✅ تم رفع الصورة بنجاح!' : '❌ فشل رفع الصورة',
                'path' => $imagePath,
                'url_path' => $urlPath,
                'full_path' => $fullPath,
                'file_exists' => $exists,
                'file_size' => $exists ? filesize($fullPath) : 0,
            ];
        } else {
            $result = [
                'success' => false,
                'message' => '❌ الملف غير صالح',
            ];
        }
    } catch (\Exception $e) {
        $result = [
            'success' => false,
            'message' => '❌ خطأ: ' . $e->getMessage(),
        ];
    }
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار رفع الصور</title>
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
        .upload-form {
            background: #fff3cd;
            border: 2px solid #ffc107;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .upload-form input[type="file"] {
            margin: 10px 0;
            padding: 10px;
            width: 100%;
            border: 2px dashed #667eea;
            border-radius: 5px;
        }
        .upload-form button {
            background: #667eea;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background 0.3s;
        }
        .upload-form button:hover {
            background: #5568d3;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
        }
        .result.success {
            background: #d4edda;
            border: 2px solid #28a745;
            color: #155724;
        }
        .result.error {
            background: #f8d7da;
            border: 2px solid #dc3545;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 اختبار رفع الصور</h1>

        <?php
        $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
        $storagePath = storage_path('app/public');
        $uploadsPath = $storagePath . '/uploads';
        
        // فحص البيئة
        echo "<div class='section'>";
        echo "<h2>1️⃣ فحص البيئة</h2>";
        echo "<div class='test-item'>";
        echo "<strong>DOCUMENT_ROOT:</strong> <code>$docRoot</code>";
        echo "</div>";
        echo "<div class='test-item'>";
        echo "<strong>Storage Path:</strong> <code>$storagePath</code>";
        echo "</div>";
        echo "<div class='test-item'>";
        echo "<strong>Uploads Path:</strong> <code>$uploadsPath</code>";
        echo "</div>";
        echo "</div>";
        
        // فحص المجلدات
        echo "<div class='section'>";
        echo "<h2>2️⃣ فحص المجلدات</h2>";
        
        $storageExists = is_dir($storagePath);
        $storageWritable = $storageExists && is_writable($storagePath);
        $uploadsExists = is_dir($uploadsPath);
        $uploadsWritable = $uploadsExists && is_writable($uploadsPath);
        
        echo "<div class='test-item'>";
        echo "<strong>storage/app/public:</strong> ";
        echo $storageExists ? "<span class='success'>✅ موجود</span>" : "<span class='error'>❌ غير موجود</span>";
        echo " | ";
        echo $storageWritable ? "<span class='success'>✅ قابل للكتابة</span>" : "<span class='error'>❌ غير قابل للكتابة</span>";
        echo "</div>";
        
        echo "<div class='test-item'>";
        echo "<strong>storage/app/public/uploads:</strong> ";
        echo $uploadsExists ? "<span class='success'>✅ موجود</span>" : "<span class='error'>❌ غير موجود</span>";
        echo " | ";
        echo $uploadsWritable ? "<span class='success'>✅ قابل للكتابة</span>" : "<span class='error'>❌ غير قابل للكتابة</span>";
        echo "</div>";
        
        // محاولة إنشاء المجلدات إذا لم تكن موجودة
        if (!$storageExists) {
            echo "<div class='test-item warning'>";
            echo "<strong>⚠️ محاولة إنشاء storage/app/public...</strong><br>";
            if (@mkdir($storagePath, 0755, true)) {
                echo "<span class='success'>✅ تم الإنشاء بنجاح</span>";
                $storageExists = true;
                $storageWritable = is_writable($storagePath);
            } else {
                echo "<span class='error'>❌ فشل الإنشاء</span>";
            }
            echo "</div>";
        }
        
        if (!$uploadsExists && $storageExists) {
            echo "<div class='test-item warning'>";
            echo "<strong>⚠️ محاولة إنشاء uploads...</strong><br>";
            if (@mkdir($uploadsPath, 0755, true)) {
                echo "<span class='success'>✅ تم الإنشاء بنجاح</span>";
                $uploadsExists = true;
                $uploadsWritable = is_writable($uploadsPath);
            } else {
                echo "<span class='error'>❌ فشل الإنشاء</span>";
            }
            echo "</div>";
        }
        
        echo "</div>";
        
        // فحص الصلاحيات
        echo "<div class='section'>";
        echo "<h2>3️⃣ فحص الصلاحيات</h2>";
        
        if ($storageExists) {
            $storagePerms = substr(sprintf('%o', fileperms($storagePath)), -4);
            echo "<div class='test-item'>";
            echo "<strong>storage/app/public:</strong> <code>$storagePerms</code>";
            if ($storagePerms !== '0755' && $storagePerms !== '0775') {
                echo " <span class='warning'>⚠️ قد تحتاج إلى: chmod 755</span>";
            }
            echo "</div>";
        }
        
        if ($uploadsExists) {
            $uploadsPerms = substr(sprintf('%o', fileperms($uploadsPath)), -4);
            echo "<div class='test-item'>";
            echo "<strong>storage/app/public/uploads:</strong> <code>$uploadsPerms</code>";
            if ($uploadsPerms !== '0755' && $uploadsPerms !== '0775') {
                echo " <span class='warning'>⚠️ قد تحتاج إلى: chmod 755</span>";
            }
            echo "</div>";
        }
        
        echo "</div>";
        
        // فحص الملفات الموجودة
        if ($uploadsExists) {
            echo "<div class='section'>";
            echo "<h2>4️⃣ الملفات الموجودة</h2>";
            
            $files = scandir($uploadsPath);
            $files = array_filter($files, function($file) use ($uploadsPath) {
                return $file !== '.' && $file !== '..' && is_file($uploadsPath . '/' . $file);
            });
            $fileCount = count($files);
            
            echo "<div class='test-item'>";
            echo "<strong>عدد الملفات:</strong> <span class='info'>$fileCount</span>";
            
            if ($fileCount > 0) {
                echo "<div style='max-height: 200px; overflow-y: auto; background: #f8f9fa; padding: 10px; border-radius: 5px; margin-top: 10px;'>";
                echo "<ul style='margin-right: 20px;'>";
                foreach (array_slice($files, 0, 20) as $file) {
                    $filePath = $uploadsPath . '/' . $file;
                    $fileSize = filesize($filePath);
                    $fileSizeFormatted = number_format($fileSize / 1024, 2) . ' KB';
                    echo "<li><code>$file</code> ($fileSizeFormatted)</li>";
                }
                if ($fileCount > 20) {
                    echo "<li>... و " . ($fileCount - 20) . " ملف آخر</li>";
                }
                echo "</ul></div>";
            } else {
                echo "<br><span class='warning'>⚠️ المجلد فارغ - لا توجد صور مرفوعة</span>";
            }
            echo "</div>";
            echo "</div>";
        }
        
        // عرض نتيجة الرفع
        if (isset($result)) {
            echo "<div class='section'>";
            echo "<h2>5️⃣ نتيجة الاختبار</h2>";
            echo "<div class='result " . ($result['success'] ? 'success' : 'error') . "'>";
            echo "<strong>" . $result['message'] . "</strong><br>";
            if (isset($result['path'])) {
                echo "<strong>المسار الداخلي:</strong> <code>" . $result['path'] . "</code><br>";
                echo "<strong>المسار الكامل:</strong> <code>" . $result['full_path'] . "</code><br>";
                echo "<strong>الملف موجود:</strong> " . ($result['file_exists'] ? "<span class='success'>✅ نعم</span>" : "<span class='error'>❌ لا</span>") . "<br>";
                if ($result['file_exists']) {
                    echo "<strong>حجم الملف:</strong> " . number_format($result['file_size'] / 1024, 2) . " KB<br>";
                    $testUrl = '/storage/' . (isset($result['url_path']) ? $result['url_path'] : 'uploads/' . basename($result['path']));
                    $fullTestUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $testUrl;
                    echo "<strong>رابط الاختبار:</strong> <a href='$testUrl' target='_blank' style='color: #667eea; font-weight: bold;'>افتح الصورة</a><br>";
                    echo "<strong>الرابط الكامل:</strong> <code>$fullTestUrl</code>";
                }
            }
            echo "</div>";
            echo "</div>";
        }
        
        // نموذج الرفع
        if ($storageWritable && $uploadsWritable) {
            echo "<div class='section'>";
            echo "<h2>6️⃣ اختبار الرفع</h2>";
            echo "<div class='upload-form'>";
            echo "<form method='POST' enctype='multipart/form-data'>";
            echo "<strong>اختر صورة للرفع:</strong><br>";
            echo "<input type='file' name='test_image' accept='image/*' required><br>";
            echo "<button type='submit'>رفع الصورة</button>";
            echo "</form>";
            echo "</div>";
            echo "</div>";
        } else {
            echo "<div class='section'>";
            echo "<h2>6️⃣ اختبار الرفع</h2>";
            echo "<div class='test-item error'>";
            echo "<strong>❌ لا يمكن رفع الصور - المجلدات غير قابلة للكتابة</strong><br>";
            echo "قم بتشغيل: <code>chmod 755 storage/app/public/uploads</code>";
            echo "</div>";
            echo "</div>";
        }
        ?>
        
        <div class="section">
            <h2>💡 التوصيات</h2>
            <div class="test-item">
                <ul style="margin-right: 20px;">
                    <?php
                    if (!$storageExists || !$storageWritable) {
                        echo "<li>❌ <strong>مشكلة حرجة:</strong> مجلد storage/app/public غير موجود أو غير قابل للكتابة</li>";
                        echo "<li>✅ <strong>الحل:</strong> قم بإنشاء المجلد وتعديل الصلاحيات: <code>chmod 755 storage/app/public</code></li>";
                    }
                    
                    if (!$uploadsExists || !$uploadsWritable) {
                        echo "<li>❌ <strong>مشكلة حرجة:</strong> مجلد uploads غير موجود أو غير قابل للكتابة</li>";
                        echo "<li>✅ <strong>الحل:</strong> قم بإنشاء المجلد وتعديل الصلاحيات: <code>chmod 755 storage/app/public/uploads</code></li>";
                    }
                    
                    if ($storageExists && $storageWritable && $uploadsExists && $uploadsWritable) {
                        echo "<li>✅ <strong>كل شيء جاهز:</strong> يمكنك رفع الصور الآن</li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>

