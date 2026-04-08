<?php
/**
 * Verify Storage Structure
 * 
 * This script verifies the storage directory structure is correct
 * and creates .gitkeep files if needed
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>التحقق من هيكل التخزين</title>
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
            border-right: 4px solid #4CAF50;
        }
        .error {
            color: #f44336;
            background: #ffebee;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
            border-right: 4px solid #f44336;
        }
        .warning {
            color: #ff9800;
            background: #fff3e0;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
            border-right: 4px solid #ff9800;
        }
        .info {
            color: #2196F3;
            background: #e3f2fd;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
            border-right: 4px solid #2196F3;
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        h2 {
            color: #666;
            margin-top: 25px;
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
        code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
        .btn {
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
        <h1>✅ التحقق من هيكل التخزين</h1>
        
        <?php
        $basePath = dirname(__FILE__);
        $checks = [];
        $errors = [];
        $warnings = [];
        
        // Required directories
        $requiredDirs = [
            'storage' => $basePath . '/storage',
            'storage/app' => $basePath . '/storage/app',
            'storage/app/public' => $basePath . '/storage/app/public',
            'storage/app/public/uploads' => $basePath . '/storage/app/public/uploads',
        ];
        
        echo '<h2>1. فحص المجلدات المطلوبة</h2>';
        echo '<table>';
        echo '<tr><th>المجلد</th><th>الحالة</th><th>الصلاحيات</th><th>قابل للكتابة</th></tr>';
        
        foreach ($requiredDirs as $name => $path) {
            $exists = file_exists($path);
            $isDir = is_dir($path);
            $isLink = is_link($path);
            $writable = is_writable($path);
            
            if ($exists && $isDir && !$isLink) {
                $perms = substr(sprintf('%o', fileperms($path)), -4);
                $status = '<span style="color: green;">✓ موجود</span>';
                $writableStatus = $writable ? '<span style="color: green;">✓</span>' : '<span style="color: red;">✗</span>';
                
                $checks[] = $name;
                
                // Try to set permissions if not writable
                if (!$writable) {
                    @chmod($path, 0755);
                    $writable = is_writable($path);
                    if ($writable) {
                        $writableStatus = '<span style="color: green;">✓ (تم الإصلاح)</span>';
                        $warnings[] = "تم إصلاح صلاحيات: $name";
                    }
                }
            } else {
                $status = '<span style="color: red;">✗ غير موجود</span>';
                $perms = '-';
                $writableStatus = '-';
                $errors[] = "المجلد $name غير موجود";
            }
            
            echo '<tr>';
            echo '<td><code>' . htmlspecialchars($name) . '</code></td>';
            echo '<td>' . $status . '</td>';
            echo '<td>' . $perms . '</td>';
            echo '<td>' . $writableStatus . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        
        // Create .gitkeep files
        echo '<h2>2. إنشاء ملفات .gitkeep</h2>';
        $gitkeepCreated = [];
        foreach ($requiredDirs as $name => $path) {
            if (file_exists($path) && is_dir($path)) {
                $gitkeepPath = $path . '/.gitkeep';
                if (!file_exists($gitkeepPath)) {
                    if (@file_put_contents($gitkeepPath, '')) {
                        echo '<div class="success">✓ تم إنشاء .gitkeep في ' . htmlspecialchars($name) . '</div>';
                        $gitkeepCreated[] = $name;
                    } else {
                        echo '<div class="warning">⚠ فشل إنشاء .gitkeep في ' . htmlspecialchars($name) . '</div>';
                    }
                } else {
                    echo '<div class="info">- .gitkeep موجود في ' . htmlspecialchars($name) . '</div>';
                }
            }
        }
        
        // Check for old storage location
        echo '<h2>3. البحث عن الصور القديمة</h2>';
        $oldStoragePath = $basePath . '/storage/app/private/public/uploads';
        if (file_exists($oldStoragePath) && is_dir($oldStoragePath)) {
            $oldFiles = glob($oldStoragePath . '/*');
            if ($oldFiles) {
                echo '<div class="warning">';
                echo '<strong>⚠ تم العثور على ' . count($oldFiles) . ' ملف في المكان القديم</strong><br>';
                echo 'المسار: <code>' . htmlspecialchars($oldStoragePath) . '</code><br>';
                echo 'يمكنك نسخ هذه الملفات إلى <code>storage/app/public/uploads</code>';
                echo '</div>';
                
                echo '<div class="info">';
                echo '<strong>الملفات الموجودة:</strong><br>';
                echo '<pre style="max-height: 200px; overflow-y: auto;">';
                foreach (array_slice($oldFiles, 0, 20) as $file) {
                    if (is_file($file)) {
                        echo basename($file) . ' (' . number_format(filesize($file) / 1024, 2) . ' KB)' . "\n";
                    }
                }
                if (count($oldFiles) > 20) {
                    echo '... و ' . (count($oldFiles) - 20) . ' ملفات أخرى';
                }
                echo '</pre>';
                echo '</div>';
            } else {
                echo '<div class="info">- لا توجد ملفات في المكان القديم</div>';
            }
        } else {
            echo '<div class="info">- المكان القديم غير موجود (هذا طبيعي)</div>';
        }
        
        // Test write permission
        echo '<h2>4. اختبار الكتابة</h2>';
        $testFile = $basePath . '/storage/app/public/uploads/test_' . time() . '.txt';
        if (@file_put_contents($testFile, 'test')) {
            echo '<div class="success">✓ تم إنشاء ملف اختبار بنجاح</div>';
            @unlink($testFile);
            echo '<div class="info">- تم حذف ملف الاختبار</div>';
        } else {
            echo '<div class="error">✗ فشل إنشاء ملف اختبار - تحقق من الصلاحيات</div>';
            $errors[] = 'Cannot write to uploads directory';
        }
        
        // Summary
        echo '<h2>5. الملخص</h2>';
        if (count($checks) === count($requiredDirs)) {
            echo '<div class="success">';
            echo '<strong>✓ جميع المجلدات موجودة وصحيحة!</strong><br>';
            echo 'الهيكل جاهز لاستقبال الملفات المرفوعة.';
            echo '</div>';
        } else {
            echo '<div class="error">';
            echo '<strong>✗ بعض المجلدات مفقودة</strong><br>';
            echo 'يرجى إنشاء المجلدات المفقودة.';
            echo '</div>';
        }
        
        if (!empty($warnings)) {
            echo '<div class="warning">';
            echo '<strong>تحذيرات:</strong><br>';
            foreach ($warnings as $warning) {
                echo '- ' . htmlspecialchars($warning) . '<br>';
            }
            echo '</div>';
        }
        
        if (!empty($errors)) {
            echo '<div class="error">';
            echo '<strong>أخطاء:</strong><br>';
            foreach ($errors as $error) {
                echo '- ' . htmlspecialchars($error) . '<br>';
            }
            echo '</div>';
        }
        
        // Next steps
        echo '<h2>6. الخطوات التالية</h2>';
        echo '<div class="info">';
        echo '<strong>الآن يمكنك:</strong><br>';
        echo '1. رفع صورة جديدة من لوحة التحكم<br>';
        echo '2. يجب أن تُحفظ في: <code>storage/app/public/uploads/</code><br>';
        echo '3. يجب أن تكون قابلة للوصول عبر: <code>/storage/uploads/filename.jpg</code><br>';
        echo '4. إذا كانت هناك صور قديمة في المكان القديم، انسخها إلى المكان الجديد<br>';
        echo '</div>';
        
        // Test Laravel route
        echo '<h2>7. اختبار Laravel Route</h2>';
        echo '<div class="info">';
        echo 'تأكد من أن Laravel route موجود في <code>routes/web.php</code><br>';
        echo 'يجب أن يحتوي على route لتقديم الملفات من <code>storage/app/public</code><br>';
        echo '</div>';
        ?>
        
        <div class="warning" style="margin-top: 30px;">
            <strong>⚠️ تحذير أمني:</strong> احذف هذا الملف (<code>verify_storage.php</code>) بعد الانتهاء!
        </div>
    </div>
</body>
</html>

