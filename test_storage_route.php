<?php
/**
 * Test Storage Route
 * 
 * This script tests if the Laravel storage route is working correctly
 * Upload to public_html and run
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>اختبار Storage Route</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .success {
            color: #4CAF50;
            background: #e8f5e9;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .error {
            color: #f44336;
            background: #ffebee;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .info {
            color: #2196F3;
            background: #e3f2fd;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
        .test-link {
            display: inline-block;
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 10px 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 اختبار Storage Route</h1>
        
        <?php
        $basePath = dirname(__FILE__);
        $uploadsPath = $basePath . '/storage/app/public/uploads';
        
        echo '<div class="info">';
        echo '<strong>المسار:</strong> ' . htmlspecialchars($uploadsPath) . '<br>';
        echo '</div>';
        
        // List files
        $files = [];
        if (file_exists($uploadsPath) && is_dir($uploadsPath)) {
            $files = glob($uploadsPath . '/*');
            $files = array_filter($files, 'is_file');
        }
        
        if (empty($files)) {
            echo '<div class="error">';
            echo '<strong>✗ لا توجد ملفات في uploads</strong><br>';
            echo 'ارفع صورة جديدة من لوحة التحكم أولاً.';
            echo '</div>';
        } else {
            echo '<div class="success">';
            echo '<strong>✓ تم العثور على ' . count($files) . ' ملف</strong>';
            echo '</div>';
            
            echo '<h2>اختبار الروابط:</h2>';
            echo '<div class="info">';
            echo 'انقر على الروابط أدناه لاختبار الوصول للملفات:<br>';
            echo '</div>';
            
            foreach (array_slice($files, 0, 10) as $file) {
                $fileName = basename($file);
                $fileUrl = '/storage/uploads/' . $fileName;
                $fileSize = filesize($file);
                
                echo '<div style="margin: 10px 0; padding: 10px; background: #f5f5f5; border-radius: 4px;">';
                echo '<strong>' . htmlspecialchars($fileName) . '</strong> (' . number_format($fileSize / 1024, 2) . ' KB)<br>';
                echo '<a href="' . htmlspecialchars($fileUrl) . '" target="_blank" class="test-link">اختبار الرابط</a>';
                echo '<code>' . htmlspecialchars($fileUrl) . '</code>';
                echo '</div>';
            }
            
            if (count($files) > 10) {
                echo '<div class="info">... و ' . (count($files) - 10) . ' ملفات أخرى</div>';
            }
        }
        
        // Test .htaccess
        echo '<h2>فحص .htaccess</h2>';
        $htaccessPath = $basePath . '/.htaccess';
        if (file_exists($htaccessPath)) {
            $htaccessContent = file_get_contents($htaccessPath);
            if (strpos($htaccessContent, '/storage/') !== false) {
                echo '<div class="success">✓ .htaccess يحتوي على قواعد لـ /storage/</div>';
            } else {
                echo '<div class="error">✗ .htaccess لا يحتوي على قواعد لـ /storage/</div>';
            }
        } else {
            echo '<div class="error">✗ ملف .htaccess غير موجود</div>';
        }
        
        // Test Laravel route
        echo '<h2>اختبار Laravel Route</h2>';
        echo '<div class="info">';
        echo 'تأكد من أن Route موجود في <code>routes/web.php</code><br>';
        echo 'يجب أن يحتوي على: <code>Route::get(\'/storage/{path}\', ...)</code>';
        echo '</div>';
        
        // Instructions
        echo '<h2>التعليمات</h2>';
        echo '<div class="info">';
        echo '<strong>إذا كانت الروابط لا تعمل:</strong><br>';
        echo '1. تأكد من رفع ملف <code>.htaccess</code> المحدث إلى الجذر<br>';
        echo '2. تأكد من رفع ملف <code>routes/web.php</code> المحدث<br>';
        echo '3. امسح cache Laravel: <code>php artisan cache:clear</code><br>';
        echo '4. امسح route cache: <code>php artisan route:clear</code><br>';
        echo '5. جرب الروابط مرة أخرى<br>';
        echo '</div>';
        ?>
        
        <div class="error" style="margin-top: 30px;">
            <strong>⚠️ تحذير أمني:</strong> احذف هذا الملف (<code>test_storage_route.php</code>) بعد الانتهاء!
        </div>
    </div>
</body>
</html>

