<?php
/**
 * اختبار شامل لمشكلة التخزين
 * 
 * ارفع هذا الملف إلى: public_html/test_storage_complete.php
 * ثم افتحه في المتصفح
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار شامل للتخزين</title>
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
        .file-list {
            max-height: 200px;
            overflow-y: auto;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }
        .file-list li {
            padding: 5px;
            border-bottom: 1px solid #ddd;
        }
        .test-link {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .test-link:hover {
            background: #5568d3;
        }
        .recommendation {
            background: #fff3cd;
            border: 2px solid #ffc107;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .recommendation h3 {
            color: #856404;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 اختبار شامل لمشكلة التخزين</h1>

        <?php
        $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
        $scriptFile = $_SERVER['SCRIPT_FILENAME'] ?? '';
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        $basePath = dirname(__FILE__);
        
        // تحديد نوع الاستضافة
        $isSharedHosting = (basename($docRoot) === 'public_html' || strpos($docRoot, 'public_html') !== false);
        $hostingType = $isSharedHosting ? 'shared hosting (public_html)' : 'standard hosting (public)';
        
        echo "<div class='section'>";
        echo "<h2>📋 معلومات البيئة</h2>";
        echo "<div class='test-item'>";
        echo "<strong>نوع الاستضافة:</strong> <span class='info'>$hostingType</span>";
        echo "</div>";
        echo "<div class='test-item'>";
        echo "<strong>DOCUMENT_ROOT:</strong> <code>$docRoot</code>";
        echo "</div>";
        echo "<div class='test-item'>";
        echo "<strong>SCRIPT_FILENAME:</strong> <code>$scriptFile</code>";
        echo "</div>";
        echo "<div class='test-item'>";
        echo "<strong>الملف الحالي:</strong> <code>$basePath</code>";
        echo "</div>";
        echo "</div>";
        
        // اختبار 1: البحث عن مسار التخزين
        echo "<div class='section'>";
        echo "<h2>1️⃣ البحث عن مسار التخزين</h2>";
        
        $possiblePaths = [];
        
        if ($isSharedHosting) {
            // Shared hosting paths
            $projectRoot = dirname($docRoot);
            $possiblePaths[] = $projectRoot . '/storage/app/public';
            $possiblePaths[] = $docRoot . '/../storage/app/public';
        } else {
            // Standard Laravel paths
            $projectRoot = dirname($basePath);
            $possiblePaths[] = $projectRoot . '/storage/app/public';
        }
        
        $storagePath = null;
        foreach ($possiblePaths as $index => $path) {
            $realPath = realpath($path);
            $exists = $realPath && is_dir($realPath);
            $status = $exists ? "<span class='success'>✅ موجود</span>" : "<span class='error'>❌ غير موجود</span>";
            
            echo "<div class='test-item'>";
            echo "<strong>المسار " . ($index + 1) . ":</strong> <code>$path</code><br>";
            echo "<strong>Realpath:</strong> " . ($realPath ?: '<span class="error">غير موجود</span>') . "<br>";
            echo "<strong>الحالة:</strong> $status";
            
            if ($exists && !$storagePath) {
                $storagePath = $realPath;
                echo "<br><span class='success'>✅ هذا هو المسار الصحيح!</span>";
            }
            echo "</div>";
        }
        
        if (!$storagePath) {
            echo "<div class='test-item error'>";
            echo "<strong>❌ لم يتم العثور على مسار التخزين!</strong>";
            echo "</div>";
        }
        echo "</div>";
        
        // اختبار 2: فحص مجلد uploads
        if ($storagePath) {
            echo "<div class='section'>";
            echo "<h2>2️⃣ فحص مجلد uploads</h2>";
            
            $uploadsPath = $storagePath . '/uploads';
            $uploadsExists = is_dir($uploadsPath);
            $uploadsWritable = $uploadsExists && is_writable($uploadsPath);
            
            echo "<div class='test-item'>";
            echo "<strong>مسار uploads:</strong> <code>$uploadsPath</code><br>";
            echo "<strong>الوجود:</strong> " . ($uploadsExists ? "<span class='success'>✅ موجود</span>" : "<span class='error'>❌ غير موجود</span>") . "<br>";
            echo "<strong>قابل للكتابة:</strong> " . ($uploadsWritable ? "<span class='success'>✅ نعم</span>" : "<span class='error'>❌ لا</span>");
            echo "</div>";
            
            if ($uploadsExists) {
                $files = scandir($uploadsPath);
                $files = array_filter($files, function($file) use ($uploadsPath) {
                    return $file !== '.' && $file !== '..' && is_file($uploadsPath . '/' . $file);
                });
                $fileCount = count($files);
                
                echo "<div class='test-item'>";
                echo "<strong>عدد الملفات:</strong> <span class='info'>$fileCount</span>";
                
                if ($fileCount > 0) {
                    echo "<div class='file-list'>";
                    echo "<strong>أمثلة على الملفات:</strong><ul>";
                    foreach (array_slice($files, 0, 10) as $file) {
                        $filePath = $uploadsPath . '/' . $file;
                        $fileSize = filesize($filePath);
                        $fileSizeFormatted = number_format($fileSize / 1024, 2) . ' KB';
                        echo "<li><code>$file</code> ($fileSizeFormatted)</li>";
                    }
                    if ($fileCount > 10) {
                        echo "<li>... و " . ($fileCount - 10) . " ملف آخر</li>";
                    }
                    echo "</ul></div>";
                } else {
                    echo "<br><span class='warning'>⚠️ المجلد فارغ - لا توجد صور مرفوعة</span>";
                }
                echo "</div>";
            }
            echo "</div>";
        }
        
        // اختبار 3: فحص storage/index.php
        echo "<div class='section'>";
        echo "<h2>3️⃣ فحص storage/index.php</h2>";
        
        $storageIndexPath = $docRoot . '/storage/index.php';
        $storageIndexExists = file_exists($storageIndexPath);
        $storageIndexReadable = $storageIndexExists && is_readable($storageIndexPath);
        
        echo "<div class='test-item'>";
        echo "<strong>مسار الملف:</strong> <code>$storageIndexPath</code><br>";
        echo "<strong>الوجود:</strong> " . ($storageIndexExists ? "<span class='success'>✅ موجود</span>" : "<span class='error'>❌ غير موجود</span>") . "<br>";
        echo "<strong>قابل للقراءة:</strong> " . ($storageIndexReadable ? "<span class='success'>✅ نعم</span>" : "<span class='error'>❌ لا</span>");
        
        if ($storageIndexExists) {
            $perms = substr(sprintf('%o', fileperms($storageIndexPath)), -4);
            echo "<br><strong>الصلاحيات:</strong> <code>$perms</code>";
            if ($perms !== '0755' && $perms !== '0644') {
                echo " <span class='warning'>⚠️ قد تحتاج إلى: chmod 755</span>";
            }
        }
        echo "</div>";
        echo "</div>";
        
        // اختبار 4: فحص .htaccess
        echo "<div class='section'>";
        echo "<h2>4️⃣ فحص .htaccess</h2>";
        
        $htaccessPath = $docRoot . '/.htaccess';
        $htaccessExists = file_exists($htaccessPath);
        
        echo "<div class='test-item'>";
        echo "<strong>مسار الملف:</strong> <code>$htaccessPath</code><br>";
        echo "<strong>الوجود:</strong> " . ($htaccessExists ? "<span class='success'>✅ موجود</span>" : "<span class='error'>❌ غير موجود</span>");
        
        if ($htaccessExists) {
            $htaccessContent = file_get_contents($htaccessPath);
            $hasStorageRule = strpos($htaccessContent, 'storage') !== false || strpos($htaccessContent, '/storage/') !== false;
            
            echo "<br><strong>يحتوي على قواعد storage:</strong> " . ($hasStorageRule ? "<span class='success'>✅ نعم</span>" : "<span class='warning'>⚠️ لا</span>");
        }
        echo "</div>";
        echo "</div>";
        
        // اختبار 5: اختبار الوصول الفعلي للصور
        if ($storagePath && $uploadsExists) {
            echo "<div class='section'>";
            echo "<h2>5️⃣ اختبار الوصول للصور</h2>";
            
            $files = scandir($storagePath . '/uploads');
            $imageFiles = array_filter($files, function($file) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
            });
            
            if (count($imageFiles) > 0) {
                $testFile = reset($imageFiles);
                $testUrl = '/storage/uploads/' . $testFile;
                $fullUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $testUrl;
                
                echo "<div class='test-item'>";
                echo "<strong>ملف الاختبار:</strong> <code>$testFile</code><br>";
                echo "<strong>URL:</strong> <code>$testUrl</code><br>";
                echo "<strong>الرابط الكامل:</strong> <a href='$testUrl' target='_blank' class='test-link'>افتح الصورة</a><br>";
                
                // محاولة الوصول للملف
                $fileExists = file_exists($storagePath . '/uploads/' . $testFile);
                echo "<strong>الملف موجود في النظام:</strong> " . ($fileExists ? "<span class='success'>✅ نعم</span>" : "<span class='error'>❌ لا</span>");
                echo "</div>";
                
                // اختبار الوصول عبر HTTP
                echo "<div class='test-item'>";
                echo "<strong>اختبار الوصول عبر HTTP:</strong><br>";
                $ch = curl_init($fullUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_NOBODY, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                if ($httpCode == 200) {
                    echo "<span class='success'>✅ الصورة متاحة عبر HTTP (200 OK)</span>";
                } elseif ($httpCode == 404) {
                    echo "<span class='error'>❌ الصورة غير متاحة (404 Not Found)</span>";
                } elseif ($httpCode == 403) {
                    echo "<span class='error'>❌ الوصول ممنوع (403 Forbidden)</span>";
                } else {
                    echo "<span class='warning'>⚠️ رمز الاستجابة: $httpCode</span>";
                }
                echo "</div>";
            } else {
                echo "<div class='test-item warning'>";
                echo "<strong>⚠️ لا توجد صور للاختبار</strong>";
                echo "</div>";
            }
            echo "</div>";
        }
        
        // التوصيات
        echo "<div class='recommendation'>";
        echo "<h3>💡 التوصيات</h3>";
        echo "<ul style='margin-right: 20px;'>";
        
        if (!$storagePath) {
            echo "<li>❌ <strong>مشكلة حرجة:</strong> لم يتم العثور على مسار التخزين. تأكد من وجود مجلد <code>storage/app/public</code></li>";
        }
        
        if ($storagePath && !file_exists($storagePath . '/uploads')) {
            echo "<li>⚠️ <strong>إنشاء المجلد:</strong> قم بإنشاء مجلد <code>uploads</code> داخل <code>storage/app/public</code></li>";
        }
        
        if (!$storageIndexExists) {
            echo "<li>❌ <strong>مفقود:</strong> ارفع ملف <code>public/storage/index.php</code> إلى <code>public_html/storage/index.php</code></li>";
        }
        
        if ($storageIndexExists && !$storageIndexReadable) {
            echo "<li>⚠️ <strong>الصلاحيات:</strong> قم بتشغيل: <code>chmod 755 public_html/storage/index.php</code></li>";
        }
        
        if (!$htaccessExists) {
            echo "<li>❌ <strong>مفقود:</strong> ارفع ملف <code>public/.htaccess</code> إلى <code>public_html/.htaccess</code></li>";
        }
        
        if ($storagePath && $storageIndexExists && $uploadsExists) {
            echo "<li>✅ <strong>كل شيء جاهز:</strong> إذا كانت الصور لا تظهر، تحقق من إعدادات الـ .htaccess</li>";
        }
        
        echo "</ul>";
        echo "</div>";
        ?>
        
        <div class="section">
            <h2>📝 ملخص</h2>
            <div class="test-item">
                <strong>المسار الصحيح للتخزين:</strong> 
                <?php if ($storagePath): ?>
                    <code><?php echo $storagePath; ?></code> ✅
                <?php else: ?>
                    <span class="error">❌ غير محدد</span>
                <?php endif; ?>
            </div>
            <div class="test-item">
                <strong>حالة النظام:</strong>
                <?php
                $allGood = $storagePath && $storageIndexExists && $uploadsExists;
                if ($allGood) {
                    echo "<span class='success'>✅ النظام جاهز</span>";
                } else {
                    echo "<span class='error'>❌ يحتاج إلى إصلاح</span>";
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>

