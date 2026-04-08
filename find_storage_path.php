<?php
/**
 * العثور على المسار الصحيح لـ storage/app/public/uploads في Hostinger
 * 
 * ارفع هذا الملف إلى: public_html/find_storage_path.php
 * ثم افتحه في المتصفح
 */

header('Content-Type: text/html; charset=utf-8');

$docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
$basePath = dirname(__FILE__);

?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>العثور على مسار storage</title>
    <style>
        body { font-family: Arial; padding: 20px; direction: rtl; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #667eea; padding-bottom: 10px; }
        .section { margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 5px; }
        .success { color: green; font-weight: bold; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { color: red; font-weight: bold; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .info { color: #17a2b8; background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
        code { background: #eee; padding: 5px 10px; border-radius: 3px; display: block; margin: 5px 0; font-size: 14px; }
        .path-item { margin: 10px 0; padding: 10px; background: white; border-radius: 5px; border: 1px solid #ddd; }
        .path-item strong { color: #667eea; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 العثور على مسار storage/app/public/uploads</h1>
        
        <?php
        echo "<div class='section'>";
        echo "<h2>1. معلومات البيئة</h2>";
        echo "<p><strong>DOCUMENT_ROOT:</strong> <code>$docRoot</code></p>";
        echo "<p><strong>الملف الحالي:</strong> <code>$basePath</code></p>";
        echo "</div>";
        
        // جميع المسارات المحتملة
        $possiblePaths = [];
        
        // Method 1: من DOCUMENT_ROOT
        if ($docRoot) {
            // إذا DOCUMENT_ROOT هو public_html
            if (basename($docRoot) === 'public_html' || strpos($docRoot, 'public_html') !== false) {
                // الجذر الرئيسي للمشروع
                $projectRoot = dirname($docRoot);
                $possiblePaths[] = [
                    'name' => 'من الجذر الرئيسي (المشروع)',
                    'path' => $projectRoot . '/storage/app/public/uploads',
                    'base' => $projectRoot
                ];
            }
            
            // من DOCUMENT_ROOT مباشرة
            $possiblePaths[] = [
                'name' => 'من DOCUMENT_ROOT',
                'path' => $docRoot . '/../storage/app/public/uploads',
                'base' => dirname($docRoot)
            ];
            
            // من DOCUMENT_ROOT/storage (إذا كان موجود)
            $possiblePaths[] = [
                'name' => 'من DOCUMENT_ROOT/storage',
                'path' => $docRoot . '/storage/app/public/uploads',
                'base' => $docRoot
            ];
        }
        
        // Method 2: من موقع الملف الحالي
        $possiblePaths[] = [
            'name' => 'من موقع الملف الحالي (public_html)',
            'path' => dirname($basePath) . '/../storage/app/public/uploads',
            'base' => dirname(dirname($basePath))
        ];
        
        $possiblePaths[] = [
            'name' => 'من موقع الملف الحالي (public_html/storage)',
            'path' => dirname($basePath) . '/../app/public/uploads',
            'base' => dirname(dirname($basePath))
        ];
        
        // Method 3: نمط Hostinger المحدد
        if ($docRoot && preg_match('#/home/([^/]+)/domains/([^/]+)/public_html#', $docRoot, $matches)) {
            $possiblePaths[] = [
                'name' => 'نمط Hostinger المحدد',
                'path' => '/home/' . $matches[1] . '/domains/' . $matches[2] . '/storage/app/public/uploads',
                'base' => '/home/' . $matches[1] . '/domains/' . $matches[2]
            ];
        }
        
        echo "<div class='section'>";
        echo "<h2>2. فحص جميع المسارات المحتملة</h2>";
        
        $foundPaths = [];
        foreach ($possiblePaths as $index => $pathInfo) {
            $path = $pathInfo['path'];
            $realPath = realpath($path);
            $exists = $realPath && is_dir($realPath);
            $writable = $exists && is_writable($realPath);
            
            echo "<div class='path-item'>";
            echo "<strong>" . ($index + 1) . ". " . $pathInfo['name'] . "</strong><br>";
            echo "<code>$path</code><br>";
            
            if ($exists) {
                echo "<span class='success'>✅ موجود</span> ";
                if ($writable) {
                    echo "<span class='success'>✅ قابل للكتابة</span>";
                    $foundPaths[] = $pathInfo;
                } else {
                    echo "<span class='error'>❌ غير قابل للكتابة</span>";
                }
                echo "<br><strong>المسار الحقيقي:</strong> <code>$realPath</code>";
            } else {
                echo "<span class='error'>❌ غير موجود</span>";
            }
            
            // فحص المجلدات الأساسية
            $basePathCheck = $pathInfo['base'] . '/storage';
            $appPathCheck = $pathInfo['base'] . '/storage/app';
            $publicPathCheck = $pathInfo['base'] . '/storage/app/public';
            
            echo "<br><small>";
            echo "المجلدات الأساسية: ";
            echo (is_dir($basePathCheck) ? "✅ storage" : "❌ storage") . " | ";
            echo (is_dir($appPathCheck) ? "✅ app" : "❌ app") . " | ";
            echo (is_dir($publicPathCheck) ? "✅ public" : "❌ public");
            echo "</small>";
            
            echo "</div>";
        }
        
        echo "</div>";
        
        // النتيجة النهائية
        echo "<div class='section'>";
        echo "<h2>3. النتيجة</h2>";
        
        if (!empty($foundPaths)) {
            $bestPath = $foundPaths[0];
            echo "<div class='success'>";
            echo "<h3>✅ تم العثور على المسار الصحيح!</h3>";
            echo "<p><strong>المسار الكامل:</strong></p>";
            echo "<code>" . htmlspecialchars($bestPath['path']) . "</code>";
            echo "<p><strong>المسار الحقيقي:</strong></p>";
            echo "<code>" . htmlspecialchars(realpath($bestPath['path'])) . "</code>";
            echo "<p><strong>الجذر الرئيسي للمشروع:</strong></p>";
            echo "<code>" . htmlspecialchars($bestPath['base']) . "</code>";
            echo "</div>";
            
            // عرض كيفية استخدامه
            echo "<div class='info'>";
            echo "<h3>💡 كيفية الاستخدام:</h3>";
            echo "<p>في <code>storage/index.php</code>، استخدم:</p>";
            echo "<code>\$storagePath = '" . htmlspecialchars($bestPath['base']) . "/storage/app/public';</code>";
            echo "<p>أو</p>";
            echo "<code>\$storagePath = '" . htmlspecialchars(dirname(realpath($bestPath['path']))) . "';</code>";
            echo "</div>";
        } else {
            echo "<div class='error'>";
            echo "<h3>❌ لم يتم العثور على المسار الصحيح</h3>";
            echo "<p>تحقق من:</p>";
            echo "<ul>";
            echo "<li>المجلدات موجودة: storage/app/public/uploads</li>";
            echo "<li>الصلاحيات صحيحة: chmod 755</li>";
            echo "</ul>";
            echo "</div>";
        }
        
        echo "</div>";
        
        // فحص الملفات الموجودة
        if (!empty($foundPaths)) {
            $uploadsPath = realpath($foundPaths[0]['path']);
            $files = [];
            if (is_dir($uploadsPath)) {
                $files = array_filter(scandir($uploadsPath), function($file) use ($uploadsPath) {
                    return $file !== '.' && $file !== '..' && is_file($uploadsPath . '/' . $file);
                });
            }
            
            echo "<div class='section'>";
            echo "<h2>4. الملفات الموجودة في uploads</h2>";
            if (count($files) > 0) {
                echo "<p><strong>عدد الملفات:</strong> " . count($files) . "</p>";
                echo "<ul>";
                foreach (array_slice($files, 0, 10) as $file) {
                    $filePath = $uploadsPath . '/' . $file;
                    echo "<li><code>$file</code> (" . number_format(filesize($filePath) / 1024, 2) . " KB)</li>";
                }
                if (count($files) > 10) {
                    echo "<li>... و " . (count($files) - 10) . " ملف آخر</li>";
                }
                echo "</ul>";
            } else {
                echo "<p class='error'>❌ المجلد فارغ</p>";
            }
            echo "</div>";
        }
        ?>
    </div>
</body>
</html>

