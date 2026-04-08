<?php
/**
 * Storage Test Script for Shared Hosting (Hostinger)
 * 
 * Upload this file to your public_html root directory
 * Access it via: https://yourdomain.com/storage_test.php
 * 
 * This script will help diagnose storage issues
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار التخزين - Storage Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        h2 {
            color: #666;
            margin-top: 30px;
            border-left: 4px solid #4CAF50;
            padding-left: 10px;
        }
        .success {
            color: #4CAF50;
            background: #e8f5e9;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .error {
            color: #f44336;
            background: #ffebee;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .warning {
            color: #ff9800;
            background: #fff3e0;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .info {
            color: #2196F3;
            background: #e3f2fd;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        pre {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            border-left: 4px solid #2196F3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table th, table td {
            padding: 12px;
            text-align: right;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background: #f5f5f5;
            font-weight: bold;
        }
        .file-list {
            max-height: 300px;
            overflow-y: auto;
        }
        .test-image {
            max-width: 200px;
            max-height: 200px;
            border: 2px solid #ddd;
            margin: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 اختبار نظام التخزين - Storage Diagnostic Tool</h1>
        
        <?php
        // Get base paths
        $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
        $scriptPath = __FILE__;
        $basePath = dirname($scriptPath);
        
        // Detect if we're in public_html (shared hosting)
        $isSharedHosting = (strpos($basePath, 'public_html') !== false || 
                           strpos($documentRoot, 'public_html') !== false ||
                           basename($documentRoot) === 'public_html');
        
        // Storage paths
        $storagePublicPath = $basePath . '/storage/app/public';
        $storageUploadsPath = $storagePublicPath . '/uploads';
        $publicStoragePath = $basePath . '/public/storage';
        $publicStorageUploadsPath = $publicStoragePath . '/uploads';
        
        // Check if Laravel structure exists
        $hasLaravelStructure = file_exists($basePath . '/artisan');
        
        echo '<div class="info">';
        echo '<strong>📁 معلومات البيئة:</strong><br>';
        echo 'Document Root: ' . htmlspecialchars($documentRoot) . '<br>';
        echo 'Script Path: ' . htmlspecialchars($scriptPath) . '<br>';
        echo 'Base Path: ' . htmlspecialchars($basePath) . '<br>';
        echo 'نوع الاستضافة: ' . ($isSharedHosting ? '<span class="success">استضافة مشتركة (Shared Hosting)</span>' : '<span class="info">استضافة عادية (Standard)</span>') . '<br>';
        echo 'هيكل Laravel: ' . ($hasLaravelStructure ? '<span class="success">✓ موجود</span>' : '<span class="error">✗ غير موجود</span>');
        echo '</div>';
        
        // Test 1: Check storage/app/public directory
        echo '<h2>1. فحص مجلد storage/app/public</h2>';
        if (file_exists($storagePublicPath)) {
            echo '<div class="success">✓ المجلد موجود: ' . htmlspecialchars($storagePublicPath) . '</div>';
            
            // Check permissions
            $perms = substr(sprintf('%o', fileperms($storagePublicPath)), -4);
            echo '<div class="info">الصلاحيات: ' . $perms . '</div>';
            
            if (is_readable($storagePublicPath)) {
                echo '<div class="success">✓ قابل للقراءة</div>';
            } else {
                echo '<div class="error">✗ غير قابل للقراءة</div>';
            }
            
            if (is_writable($storagePublicPath)) {
                echo '<div class="success">✓ قابل للكتابة</div>';
            } else {
                echo '<div class="error">✗ غير قابل للكتابة</div>';
            }
        } else {
            echo '<div class="error">✗ المجلد غير موجود: ' . htmlspecialchars($storagePublicPath) . '</div>';
        }
        
        // Test 2: Check storage/app/public/uploads directory
        echo '<h2>2. فحص مجلد storage/app/public/uploads</h2>';
        if (file_exists($storageUploadsPath)) {
            echo '<div class="success">✓ المجلد موجود: ' . htmlspecialchars($storageUploadsPath) . '</div>';
            
            // List files
            $files = glob($storageUploadsPath . '/*');
            if ($files) {
                echo '<div class="info"><strong>الملفات الموجودة (' . count($files) . '):</strong></div>';
                echo '<div class="file-list">';
                echo '<table>';
                echo '<tr><th>اسم الملف</th><th>الحجم</th><th>التاريخ</th><th>الصلاحيات</th><th>الرابط</th></tr>';
                foreach (array_slice($files, 0, 20) as $file) {
                    if (is_file($file)) {
                        $fileName = basename($file);
                        $fileSize = filesize($file);
                        $fileDate = date('Y-m-d H:i:s', filemtime($file));
                        $filePerms = substr(sprintf('%o', fileperms($file)), -4);
                        $fileUrl = '/storage/uploads/' . $fileName;
                        
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($fileName) . '</td>';
                        echo '<td>' . number_format($fileSize / 1024, 2) . ' KB</td>';
                        echo '<td>' . $fileDate . '</td>';
                        echo '<td>' . $filePerms . '</td>';
                        echo '<td><a href="' . htmlspecialchars($fileUrl) . '" target="_blank">اختبار</a></td>';
                        echo '</tr>';
                    }
                }
                echo '</table>';
                echo '</div>';
            } else {
                echo '<div class="warning">⚠ المجلد فارغ</div>';
            }
        } else {
            echo '<div class="error">✗ المجلد غير موجود: ' . htmlspecialchars($storageUploadsPath) . '</div>';
            echo '<div class="info">محاولة إنشاء المجلد...</div>';
            if (mkdir($storageUploadsPath, 0755, true)) {
                echo '<div class="success">✓ تم إنشاء المجلد بنجاح</div>';
            } else {
                echo '<div class="error">✗ فشل إنشاء المجلد</div>';
            }
        }
        
        // Test 3: Check public/storage symlink
        echo '<h2>3. فحص symlink في public/storage</h2>';
        if (file_exists($publicStoragePath)) {
            echo '<div class="success">✓ موجود: ' . htmlspecialchars($publicStoragePath) . '</div>';
            
            if (is_link($publicStoragePath)) {
                $linkTarget = readlink($publicStoragePath);
                echo '<div class="info">نوع: Symlink</div>';
                echo '<div class="info">الهدف: ' . htmlspecialchars($linkTarget) . '</div>';
                
                if (file_exists($linkTarget)) {
                    echo '<div class="success">✓ الهدف موجود ويعمل</div>';
                } else {
                    echo '<div class="error">✗ الهدف غير موجود</div>';
                }
            } else {
                echo '<div class="warning">⚠ ليس symlink (قد يكون مجلد عادي)</div>';
            }
            
            // Check if accessible via web
            $testUrl = '/storage/uploads/';
            echo '<div class="info">اختبار الوصول عبر الويب: <a href="' . $testUrl . '" target="_blank">' . $testUrl . '</a></div>';
        } else {
            echo '<div class="error">✗ غير موجود: ' . htmlspecialchars($publicStoragePath) . '</div>';
            echo '<div class="warning">⚠ يجب إنشاء symlink أو استخدام Laravel route</div>';
        }
        
        // Test 4: Test specific image file
        echo '<h2>4. اختبار ملف الصورة المحدد</h2>';
        $testImageName = 'about_1769959120.jpg';
        $testImagePath = $storageUploadsPath . '/' . $testImageName;
        
        if (file_exists($testImagePath)) {
            echo '<div class="success">✓ الملف موجود: ' . htmlspecialchars($testImagePath) . '</div>';
            
            $fileSize = filesize($testImagePath);
            $filePerms = substr(sprintf('%o', fileperms($testImagePath)), -4);
            $isReadable = is_readable($testImagePath);
            
            echo '<div class="info">الحجم: ' . number_format($fileSize / 1024, 2) . ' KB</div>';
            echo '<div class="info">الصلاحيات: ' . $filePerms . '</div>';
            echo '<div class="' . ($isReadable ? 'success' : 'error') . '">' . ($isReadable ? '✓' : '✗') . ' قابل للقراءة</div>';
            
            // Test URLs
            $urls = [
                '/storage/uploads/' . $testImageName,
                '/public/storage/uploads/' . $testImageName,
            ];
            
            echo '<div class="info"><strong>روابط الاختبار:</strong></div>';
            foreach ($urls as $url) {
                echo '<div><a href="' . htmlspecialchars($url) . '" target="_blank">' . htmlspecialchars($url) . '</a></div>';
            }
            
            // Try to display image
            if ($isReadable && in_array(strtolower(pathinfo($testImagePath, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'])) {
                echo '<div class="info"><strong>معاينة الصورة:</strong></div>';
                echo '<img src="' . htmlspecialchars('/storage/uploads/' . $testImageName) . '" alt="Test Image" class="test-image" onerror="this.style.border=\'2px solid red\'; this.alt=\'فشل تحميل الصورة\';" />';
            }
        } else {
            echo '<div class="error">✗ الملف غير موجود: ' . htmlspecialchars($testImagePath) . '</div>';
            
            // List similar files
            $similarFiles = glob($storageUploadsPath . '/about_*.jpg');
            if ($similarFiles) {
                echo '<div class="info"><strong>ملفات مشابهة موجودة:</strong></div>';
                foreach ($similarFiles as $file) {
                    echo '<div>' . htmlspecialchars(basename($file)) . '</div>';
                }
            }
        }
        
        // Test 5: Check .htaccess
        echo '<h2>5. فحص ملف .htaccess</h2>';
        $htaccessPath = $basePath . '/public/.htaccess';
        if (file_exists($htaccessPath)) {
            echo '<div class="success">✓ موجود: ' . htmlspecialchars($htaccessPath) . '</div>';
            $htaccessContent = file_get_contents($htaccessPath);
            if (strpos($htaccessContent, '/storage/') !== false) {
                echo '<div class="success">✓ يحتوي على قواعد لـ /storage/</div>';
            } else {
                echo '<div class="warning">⚠ لا يحتوي على قواعد لـ /storage/</div>';
            }
        } else {
            echo '<div class="error">✗ غير موجود: ' . htmlspecialchars($htaccessPath) . '</div>';
        }
        
        // Test 6: Server information
        echo '<h2>6. معلومات السيرفر</h2>';
        echo '<table>';
        echo '<tr><th>المعلومة</th><th>القيمة</th></tr>';
        echo '<tr><td>PHP Version</td><td>' . phpversion() . '</td></tr>';
        echo '<tr><td>Server Software</td><td>' . ($_SERVER['SERVER_SOFTWARE'] ?? 'غير معروف') . '</td></tr>';
        echo '<tr><td>Document Root</td><td>' . htmlspecialchars($documentRoot) . '</td></tr>';
        echo '<tr><td>Script Filename</td><td>' . htmlspecialchars($_SERVER['SCRIPT_FILENAME'] ?? '') . '</td></tr>';
        echo '<tr><td>Request URI</td><td>' . htmlspecialchars($_SERVER['REQUEST_URI'] ?? '') . '</td></tr>';
        echo '</table>';
        
        // Recommendations
        echo '<h2>7. التوصيات</h2>';
        echo '<div class="info">';
        echo '<strong>إذا كانت الصور لا تظهر:</strong><br>';
        echo '1. تأكد من أن الملفات موجودة في: storage/app/public/uploads/<br>';
        echo '2. تأكد من الصلاحيات (755 للمجلدات، 644 للملفات)<br>';
        echo '3. إذا كان symlink لا يعمل، تأكد من أن Laravel route موجود في routes/web.php<br>';
        echo '4. تأكد من أن .htaccess يحتوي على قواعد لتوجيه /storage/* إلى Laravel<br>';
        echo '5. جرب الوصول مباشرة: /storage/uploads/filename.jpg<br>';
        echo '</div>';
        ?>
        
        <div style="margin-top: 30px; padding: 15px; background: #f5f5f5; border-radius: 4px;">
            <strong>ملاحظة:</strong> بعد الانتهاء من الاختبار، احذف هذا الملف لأسباب أمنية.
        </div>
    </div>
</body>
</html>

