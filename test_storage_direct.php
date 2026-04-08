<?php
/**
 * اختبار مباشر لـ storage/index.php
 * 
 * ارفع هذا الملف إلى: public_html/test_storage_direct.php
 * ثم افتحه في المتصفح
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>اختبار مباشر لـ storage/index.php</title>
    <style>
        body { font-family: Arial; padding: 20px; direction: rtl; }
        .test { margin: 20px 0; padding: 15px; background: #f5f5f5; border-radius: 5px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        code { background: #eee; padding: 2px 5px; }
    </style>
</head>
<body>
    <h1>اختبار مباشر لـ storage/index.php</h1>
    
    <?php
    $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
    $storageIndexPath = $docRoot . '/storage/index.php';
    
    echo "<div class='test'>";
    echo "<h2>1. فحص وجود storage/index.php</h2>";
    echo "المسار: <code>$storageIndexPath</code><br>";
    
    if (file_exists($storageIndexPath)) {
        echo "<span class='success'>✅ الملف موجود</span><br>";
        echo "الصلاحيات: <code>" . substr(sprintf('%o', fileperms($storageIndexPath)), -4) . "</code><br>";
        
        // اختبار ملف صورة موجود
        $testImage = 'about_1769957414.jpg'; // من الاختبار السابق
        $testUrl = '/storage/uploads/' . $testImage;
        
        echo "<h2>2. اختبار الوصول للصورة</h2>";
        echo "URL: <code>$testUrl</code><br>";
        
        // محاولة الوصول مباشرة
        $fullTestUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $testUrl;
        echo "الرابط الكامل: <a href='$testUrl' target='_blank'>$fullTestUrl</a><br><br>";
        
        // اختبار curl
        echo "<h3>اختبار cURL:</h3>";
        $ch = curl_init($fullTestUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);
        
        echo "رمز HTTP: <code>$httpCode</code><br>";
        echo "نوع المحتوى: <code>" . ($contentType ?: 'غير محدد') . "</code><br>";
        
        if ($httpCode == 200) {
            echo "<span class='success'>✅ الصورة متاحة!</span>";
        } else {
            echo "<span class='error'>❌ الصورة غير متاحة (رمز: $httpCode)</span><br>";
            
            // اختبار المسار المباشر
            echo "<h3>فحص المسار المباشر:</h3>";
            $storagePath = dirname($docRoot) . '/storage/app/public';
            $imagePath = $storagePath . '/uploads/' . $testImage;
            
            echo "المسار المتوقع: <code>$imagePath</code><br>";
            echo "الملف موجود: " . (file_exists($imagePath) ? "<span class='success'>✅ نعم</span>" : "<span class='error'>❌ لا</span>") . "<br>";
            
            if (!file_exists($imagePath)) {
                // البحث عن الملف
                $uploadsPath = $storagePath . '/uploads';
                if (is_dir($uploadsPath)) {
                    $files = scandir($uploadsPath);
                    $files = array_filter($files, function($f) use ($uploadsPath) {
                        return $f !== '.' && $f !== '..' && is_file($uploadsPath . '/' . $f);
                    });
                    if (count($files) > 0) {
                        echo "<br>الملفات الموجودة في uploads:<br>";
                        foreach (array_slice($files, 0, 5) as $file) {
                            echo "- <code>$file</code><br>";
                        }
                    }
                }
            }
        }
        
        // اختبار storage/index.php مباشرة
        echo "<h3>اختبار storage/index.php مباشرة:</h3>";
        $_SERVER['REQUEST_URI'] = $testUrl;
        ob_start();
        try {
            include $storageIndexPath;
            $output = ob_get_clean();
            if (strlen($output) > 0) {
                echo "<span class='success'>✅ الملف يعمل ويخرج محتوى</span><br>";
                echo "حجم المخرجات: " . strlen($output) . " بايت";
            } else {
                echo "<span class='error'>❌ الملف لا يخرج محتوى</span>";
            }
        } catch (\Exception $e) {
            ob_end_clean();
            echo "<span class='error'>❌ خطأ: " . htmlspecialchars($e->getMessage()) . "</span>";
        }
        
    } else {
        echo "<span class='error'>❌ الملف غير موجود!</span><br>";
        echo "يجب أن يكون في: <code>$storageIndexPath</code>";
    }
    echo "</div>";
    
    // اختبار .htaccess
    echo "<div class='test'>";
    echo "<h2>3. فحص .htaccess</h2>";
    $htaccessPath = $docRoot . '/.htaccess';
    
    if (file_exists($htaccessPath)) {
        echo "<span class='success'>✅ .htaccess موجود</span><br>";
        $htaccessContent = file_get_contents($htaccessPath);
        
        // البحث عن قواعد storage
        if (strpos($htaccessContent, 'storage/index.php') !== false) {
            echo "<span class='success'>✅ يحتوي على قواعد storage/index.php</span><br>";
        } else {
            echo "<span class='error'>❌ لا يحتوي على قواعد storage/index.php</span><br>";
        }
        
        if (strpos($htaccessContent, 'RewriteRule.*storage') !== false || preg_match('/RewriteRule.*storage/i', $htaccessContent)) {
            echo "<span class='success'>✅ يحتوي على قواعد RewriteRule لـ storage</span><br>";
        } else {
            echo "<span class='error'>❌ لا يحتوي على قواعد RewriteRule لـ storage</span><br>";
        }
    } else {
        echo "<span class='error'>❌ .htaccess غير موجود!</span>";
    }
    echo "</div>";
    ?>
    
    <div class="test">
        <h2>4. اختبار مباشر</h2>
        <p>جرب فتح هذه الروابط مباشرة:</p>
        <ul>
            <li><a href="/storage/uploads/about_1769957414.jpg" target="_blank">/storage/uploads/about_1769957414.jpg</a></li>
            <li><a href="/storage/index.php?file=uploads/about_1769957414.jpg" target="_blank">/storage/index.php?file=uploads/about_1769957414.jpg</a></li>
        </ul>
    </div>
</body>
</html>

