<?php
/**
 * Complete Storage Fix & Test Tool
 * 
 * This comprehensive tool:
 * 1. Checks entire storage structure
 * 2. Fixes all issues automatically
 * 3. Creates storage/index.php if needed
 * 4. Provides upload test interface
 * 5. Verifies upload and access
 * 
 * Upload to public_html and run
 */

header('Content-Type: text/html; charset=utf-8');

// Handle file upload
$uploadResult = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_image'])) {
    $uploadResult = handleTestUpload();
}

function handleTestUpload() {
    $basePath = dirname(__FILE__);
    $uploadsPath = $basePath . '/storage/app/public/uploads';
    
    // Create directory if doesn't exist
    if (!file_exists($uploadsPath)) {
        @mkdir($uploadsPath, 0755, true);
    }
    
    if (!isset($_FILES['test_image']) || $_FILES['test_image']['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'فشل الرفع: ' . ($_FILES['test_image']['error'] ?? 'خطأ غير معروف')];
    }
    
    $file = $_FILES['test_image'];
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/svg', 'image/webp'];
    
    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'نوع الملف غير مدعوم. يرجى رفع صورة (JPG, PNG, GIF, SVG, WEBP)'];
    }
    
    $fileName = 'test_' . time() . '_' . uniqid() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
    $targetPath = $uploadsPath . '/' . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        @chmod($targetPath, 0644);
        return [
            'success' => true,
            'message' => 'تم رفع الصورة بنجاح!',
            'fileName' => $fileName,
            'filePath' => $targetPath,
            'fileUrl' => '/storage/uploads/' . $fileName,
            'fileSize' => filesize($targetPath)
        ];
    } else {
        return ['success' => false, 'message' => 'فشل حفظ الملف. تحقق من الصلاحيات.'];
    }
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إصلاح شامل لنظام التخزين</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 2em;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px;
        }
        .section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-right: 4px solid #667eea;
        }
        .section h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.5em;
        }
        .check-item {
            padding: 10px;
            margin: 8px 0;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border-right: 3px solid #28a745;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border-right: 3px solid #dc3545;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            border-right: 3px solid #ffc107;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border-right: 3px solid #17a2b8;
        }
        .fix-action {
            background: #e7f3ff;
            color: #004085;
            border-right: 3px solid #0056b3;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .upload-box {
            border: 2px dashed #667eea;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            background: #f8f9fa;
            margin: 20px 0;
        }
        .upload-box input[type="file"] {
            display: none;
        }
        .upload-label {
            display: inline-block;
            padding: 15px 30px;
            background: #667eea;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1em;
            transition: all 0.3s;
        }
        .upload-label:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 1em;
            transition: all 0.3s;
        }
        .btn:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }
        .btn-primary {
            background: #667eea;
        }
        .btn-primary:hover {
            background: #5568d3;
        }
        .test-image {
            max-width: 300px;
            max-height: 300px;
            border: 3px solid #667eea;
            border-radius: 8px;
            margin: 15px 0;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .summary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 8px;
            margin-top: 30px;
        }
        .summary h2 {
            color: white;
            margin-bottom: 15px;
        }
        code {
            background: #f4f4f4;
            padding: 2px 8px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            color: #e83e8c;
        }
        .progress {
            background: #e9ecef;
            border-radius: 10px;
            height: 25px;
            margin: 10px 0;
            overflow: hidden;
        }
        .progress-bar {
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            transition: width 0.3s;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table th, table td {
            padding: 12px;
            text-align: right;
            border-bottom: 1px solid #dee2e6;
        }
        table th {
            background: #667eea;
            color: white;
        }
        .icon {
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 إصلاح شامل لنظام التخزين</h1>
            <p>فحص وإصلاح تلقائي كامل للمشروع</p>
        </div>
        
        <div class="content">
            <?php
            $basePath = dirname(__FILE__);
            $checks = [];
            $fixes = [];
            $errors = [];
            $warnings = [];
            
            // ========== SECTION 1: Environment Check ==========
            echo '<div class="section">';
            echo '<h2>1. فحص البيئة</h2>';
            
            $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
            $scriptPath = __FILE__;
            $isSharedHosting = (strpos($basePath, 'public_html') !== false || 
                               strpos($documentRoot, 'public_html') !== false ||
                               basename($documentRoot) === 'public_html');
            
            echo '<div class="check-item info">';
            echo '<span class="icon">📁</span>';
            echo '<div>';
            echo '<strong>Document Root:</strong> ' . htmlspecialchars($documentRoot) . '<br>';
            echo '<strong>Base Path:</strong> ' . htmlspecialchars($basePath) . '<br>';
            echo '<strong>نوع الاستضافة:</strong> ' . ($isSharedHosting ? 'استضافة مشتركة (Shared Hosting)' : 'استضافة عادية');
            echo '</div>';
            echo '</div>';
            
            echo '</div>';
            
            // ========== SECTION 2: Directory Structure Check ==========
            echo '<div class="section">';
            echo '<h2>2. فحص هيكل المجلدات</h2>';
            
            $requiredDirs = [
                'storage' => $basePath . '/storage',
                'storage/app' => $basePath . '/storage/app',
                'storage/app/public' => $basePath . '/storage/app/public',
                'storage/app/public/uploads' => $basePath . '/storage/app/public/uploads',
            ];
            
            foreach ($requiredDirs as $name => $path) {
                $exists = file_exists($path);
                $isDir = is_dir($path);
                $isLink = is_link($path);
                $writable = is_writable($path);
                
                if ($exists && $isDir && !$isLink) {
                    $perms = substr(sprintf('%o', fileperms($path)), -4);
                    echo '<div class="check-item success">';
                    echo '<span class="icon">✓</span>';
                    echo '<div>';
                    echo '<strong>' . htmlspecialchars($name) . '</strong> موجود<br>';
                    echo 'الصلاحيات: ' . $perms . ' | قابل للكتابة: ' . ($writable ? '✓' : '✗');
                    echo '</div>';
                    echo '</div>';
                    $checks[] = $name;
                    
                    if (!$writable) {
                        @chmod($path, 0755);
                        if (is_writable($path)) {
                            $fixes[] = "تم إصلاح صلاحيات: $name";
                        }
                    }
                } else {
                    echo '<div class="check-item error">';
                    echo '<span class="icon">✗</span>';
                    echo '<div>';
                    echo '<strong>' . htmlspecialchars($name) . '</strong> غير موجود';
                    echo '</div>';
                    echo '</div>';
                    $errors[] = "المجلد $name غير موجود";
                    
                    // Try to create
                    echo '<div class="fix-action">';
                    echo '🔨 محاولة إنشاء المجلد...';
                    if (@mkdir($path, 0755, true)) {
                        echo '<div class="check-item success" style="margin-top: 10px;">✓ تم إنشاء المجلد بنجاح</div>';
                        $fixes[] = "تم إنشاء: $name";
                        $checks[] = $name;
                    } else {
                        echo '<div class="check-item error" style="margin-top: 10px;">✗ فشل إنشاء المجلد</div>';
                    }
                    echo '</div>';
                }
            }
            
            echo '</div>';
            
            // ========== SECTION 3: Storage Index.php Check ==========
            echo '<div class="section">';
            echo '<h2>3. فحص storage/index.php</h2>';
            
            $storageIndexPath = $basePath . '/storage/index.php';
            $storageIndexSource = dirname(__FILE__) . '/public_html_storage_index.php';
            
            if (file_exists($storageIndexPath)) {
                echo '<div class="check-item success">';
                echo '<span class="icon">✓</span>';
                echo '<div><strong>storage/index.php</strong> موجود</div>';
                echo '</div>';
                $checks[] = 'storage/index.php';
                
                // Test if it can access files
                $testUploadsPath = $basePath . '/storage/app/public/uploads';
                if (file_exists($testUploadsPath)) {
                    $testFiles = glob($testUploadsPath . '/*');
                    $testFiles = array_filter($testFiles, 'is_file');
                    if (!empty($testFiles)) {
                        $testFile = basename($testFiles[0]);
                        $testUrl = '/storage/uploads/' . $testFile;
                        echo '<div class="check-item info">';
                        echo '<span class="icon">🧪</span>';
                        echo '<div>';
                        echo '<strong>اختبار الوصول:</strong><br>';
                        echo 'ملف اختبار: <code>' . htmlspecialchars($testFile) . '</code><br>';
                        echo 'رابط الاختبار: <a href="' . htmlspecialchars($testUrl) . '" target="_blank" class="btn">اختبار الآن</a>';
                        echo '</div>';
                        echo '</div>';
                    }
                }
            } else {
                echo '<div class="check-item error">';
                echo '<span class="icon">✗</span>';
                echo '<div><strong>storage/index.php</strong> غير موجود</div>';
                echo '</div>';
                $errors[] = 'storage/index.php غير موجود';
                
                // Try to create
                echo '<div class="fix-action">';
                echo '🔨 محاولة إنشاء storage/index.php...';
                
                // Check if source file exists
                if (file_exists($storageIndexSource)) {
                    $indexContent = file_get_contents($storageIndexSource);
                } else {
                    // Create content inline with fixed paths
                    $indexContent = '<?php
/**
 * Direct Storage File Server
 */
$requestUri = $_SERVER[\'REQUEST_URI\'] ?? \'\';
$parsedUrl = parse_url($requestUri);
$path = $parsedUrl[\'path\'] ?? \'\';
$path = preg_replace(\'#^/storage/#\', \'\', $path);
$path = preg_replace(\'#^storage/#\', \'\', $path);
$path = ltrim($path, \'/\');

if (empty($path) || strpos($path, \'..\') !== false || strpos($path, \'./\') !== false || strpos($path, \'/\') === 0) {
    http_response_code(404);
    die(\'File not found\');
}

$currentFile = __FILE__;
$basePath = dirname($currentFile);
$rootPath = dirname($basePath);
$storagePath = $rootPath . \'/storage/app/public\';
$filePath = $storagePath . \'/\' . $path;

$realFilePath = realpath($filePath);
$realStoragePath = realpath($storagePath);

if (!$realFilePath || !$realStoragePath || strpos($realFilePath, $realStoragePath) !== 0) {
    http_response_code(404);
    die(\'File not found\');
}

if (!file_exists($realFilePath) || !is_file($realFilePath)) {
    http_response_code(404);
    die(\'File not found\');
}

$mimeType = mime_content_type($realFilePath);
if (!$mimeType) {
    $extension = strtolower(pathinfo($realFilePath, PATHINFO_EXTENSION));
    $mimeTypes = [\'jpg\' => \'image/jpeg\', \'jpeg\' => \'image/jpeg\', \'png\' => \'image/png\', \'gif\' => \'image/gif\', \'svg\' => \'image/svg+xml\', \'webp\' => \'image/webp\', \'pdf\' => \'application/pdf\'];
    $mimeType = $mimeTypes[$extension] ?? \'application/octet-stream\';
}

header(\'Content-Type: \' . $mimeType);
header(\'Content-Length: \' . filesize($realFilePath));
header(\'Content-Disposition: inline; filename="\' . basename($path) . \'"\');
header(\'Cache-Control: public, max-age=31536000\');
readfile($realFilePath);
exit;
';
                }
                
                // Ensure storage directory exists
                $storageDir = dirname($storageIndexPath);
                if (!file_exists($storageDir)) {
                    @mkdir($storageDir, 0755, true);
                }
                
                if (@file_put_contents($storageIndexPath, $indexContent)) {
                    @chmod($storageIndexPath, 0644);
                    echo '<div class="check-item success" style="margin-top: 10px;">✓ تم إنشاء storage/index.php بنجاح</div>';
                    $fixes[] = 'تم إنشاء storage/index.php';
                    $checks[] = 'storage/index.php';
                } else {
                    echo '<div class="check-item error" style="margin-top: 10px;">✗ فشل إنشاء storage/index.php</div>';
                    echo '<div class="check-item warning" style="margin-top: 10px;">';
                    echo '⚠ يجب إنشاء الملف يدوياً:<br>';
                    echo '1. أنشئ مجلد <code>storage</code> في <code>public_html</code><br>';
                    echo '2. أنشئ ملف <code>storage/index.php</code><br>';
                    echo '3. انسخ محتوى <code>public_html_storage_index.php</code>';
                    echo '</div>';
                }
                echo '</div>';
            }
            
            echo '</div>';
            
            // ========== SECTION 4: .htaccess Check ==========
            echo '<div class="section">';
            echo '<h2>4. فحص .htaccess</h2>';
            
            $htaccessPath = $basePath . '/.htaccess';
            if (file_exists($htaccessPath)) {
                $htaccessContent = file_get_contents($htaccessPath);
                if (strpos($htaccessContent, '/storage/') !== false) {
                    echo '<div class="check-item success">';
                    echo '<span class="icon">✓</span>';
                    echo '<div><strong>.htaccess</strong> يحتوي على قواعد لـ /storage/</div>';
                    echo '</div>';
                    $checks[] = '.htaccess';
                } else {
                    echo '<div class="check-item warning">';
                    echo '<span class="icon">⚠</span>';
                    echo '<div><strong>.htaccess</strong> موجود لكن لا يحتوي على قواعد لـ /storage/</div>';
                    echo '</div>';
                    $warnings[] = '.htaccess لا يحتوي على قواعد storage';
                }
            } else {
                echo '<div class="check-item error">';
                echo '<span class="icon">✗</span>';
                echo '<div><strong>.htaccess</strong> غير موجود</div>';
                echo '</div>';
                $errors[] = '.htaccess غير موجود';
            }
            
            echo '</div>';
            
            // ========== SECTION 5: Laravel Route Check ==========
            echo '<div class="section">';
            echo '<h2>5. فحص Laravel Route</h2>';
            
            $routesPath = $basePath . '/routes/web.php';
            if (file_exists($routesPath)) {
                $routesContent = file_get_contents($routesPath);
                if (strpos($routesContent, "Route::get('/storage/{path}'") !== false) {
                    echo '<div class="check-item success">';
                    echo '<span class="icon">✓</span>';
                    echo '<div><strong>routes/web.php</strong> يحتوي على Route لـ /storage/</div>';
                    echo '</div>';
                    $checks[] = 'Laravel Route';
                } else {
                    echo '<div class="check-item warning">';
                    echo '<span class="icon">⚠</span>';
                    echo '<div><strong>routes/web.php</strong> موجود لكن لا يحتوي على Route لـ /storage/</div>';
                    echo '</div>';
                    $warnings[] = 'Laravel Route غير موجود';
                }
            } else {
                echo '<div class="check-item error">';
                echo '<span class="icon">✗</span>';
                echo '<div><strong>routes/web.php</strong> غير موجود</div>';
                echo '</div>';
            }
            
            echo '</div>';
            
            // ========== SECTION 6: Upload Test Interface ==========
            echo '<div class="section">';
            echo '<h2>6. اختبار رفع الصور</h2>';
            
            if ($uploadResult) {
                if ($uploadResult['success']) {
                    echo '<div class="check-item success">';
                    echo '<span class="icon">✓</span>';
                    echo '<div>';
                    echo '<strong>' . $uploadResult['message'] . '</strong><br>';
                    echo 'اسم الملف: <code>' . htmlspecialchars($uploadResult['fileName']) . '</code><br>';
                    echo 'الحجم: ' . number_format($uploadResult['fileSize'] / 1024, 2) . ' KB<br>';
                    echo 'الرابط: <code>' . htmlspecialchars($uploadResult['fileUrl']) . '</code><br><br>';
                    
                    // Test image display
                    echo '<strong>معاينة الصورة:</strong><br>';
                    echo '<img src="' . htmlspecialchars($uploadResult['fileUrl']) . '" alt="Test Image" class="test-image" onerror="this.style.border=\'3px solid red\'; this.alt=\'فشل تحميل الصورة - تحقق من storage/index.php\';" /><br><br>';
                    
                    // Test link
                    echo '<a href="' . htmlspecialchars($uploadResult['fileUrl']) . '" target="_blank" class="btn btn-primary">اختبار الرابط في نافذة جديدة</a>';
                    echo '</div>';
                    echo '</div>';
                } else {
                    echo '<div class="check-item error">';
                    echo '<span class="icon">✗</span>';
                    echo '<div><strong>' . $uploadResult['message'] . '</strong></div>';
                    echo '</div>';
                }
            }
            
            echo '<div class="upload-box">';
            echo '<form method="POST" enctype="multipart/form-data">';
            echo '<label for="test_image" class="upload-label">';
            echo '📤 اختر صورة للرفع';
            echo '</label>';
            echo '<input type="file" id="test_image" name="test_image" accept="image/*" required onchange="this.form.submit()">';
            echo '</form>';
            echo '<p style="margin-top: 15px; color: #666;">اختر صورة (JPG, PNG, GIF, SVG, WEBP) للاختبار</p>';
            echo '</div>';
            
            echo '</div>';
            
            // ========== SECTION 7: Existing Files Check ==========
            echo '<div class="section">';
            echo '<h2>7. الملفات الموجودة</h2>';
            
            $uploadsPath = $basePath . '/storage/app/public/uploads';
            if (file_exists($uploadsPath) && is_dir($uploadsPath)) {
                $files = glob($uploadsPath . '/*');
                $files = array_filter($files, 'is_file');
                
                if ($files) {
                    echo '<div class="check-item info">';
                    echo '<span class="icon">📁</span>';
                    echo '<div>';
                    echo '<strong>تم العثور على ' . count($files) . ' ملف</strong>';
                    echo '</div>';
                    echo '</div>';
                    
                    echo '<table>';
                    echo '<tr><th>اسم الملف</th><th>الحجم</th><th>التاريخ</th><th>الرابط</th></tr>';
                    foreach (array_slice($files, 0, 10) as $file) {
                        $fileName = basename($file);
                        $fileSize = filesize($file);
                        $fileDate = date('Y-m-d H:i:s', filemtime($file));
                        $fileUrl = '/storage/uploads/' . $fileName;
                        
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($fileName) . '</td>';
                        echo '<td>' . number_format($fileSize / 1024, 2) . ' KB</td>';
                        echo '<td>' . $fileDate . '</td>';
                        echo '<td><a href="' . htmlspecialchars($fileUrl) . '" target="_blank" class="btn">اختبار</a></td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                    
                    if (count($files) > 10) {
                        echo '<div class="check-item info">... و ' . (count($files) - 10) . ' ملفات أخرى</div>';
                    }
                } else {
                    echo '<div class="check-item warning">';
                    echo '<span class="icon">⚠</span>';
                    echo '<div>المجلد فارغ - ارفع صورة للاختبار</div>';
                    echo '</div>';
                }
            }
            
            echo '</div>';
            
            // ========== SECTION 8: Summary ==========
            echo '<div class="summary">';
            echo '<h2>📊 الملخص النهائي</h2>';
            
            $totalChecks = count($requiredDirs) + 3; // dirs + storage/index.php + .htaccess + route
            $passedChecks = count($checks);
            $percentage = $totalChecks > 0 ? round(($passedChecks / $totalChecks) * 100) : 0;
            
            echo '<div class="progress">';
            echo '<div class="progress-bar" style="width: ' . $percentage . '%;">' . $percentage . '%</div>';
            echo '</div>';
            
            echo '<div style="margin-top: 20px;">';
            echo '<strong>الفحوصات المنجزة:</strong> ' . $passedChecks . ' / ' . $totalChecks . '<br>';
            
            if (!empty($fixes)) {
                echo '<div style="margin-top: 15px; background: rgba(255,255,255,0.2); padding: 15px; border-radius: 5px;">';
                echo '<strong>الإصلاحات المنفذة:</strong><br>';
                foreach ($fixes as $fix) {
                    echo '✓ ' . htmlspecialchars($fix) . '<br>';
                }
                echo '</div>';
            }
            
            if (!empty($warnings)) {
                echo '<div style="margin-top: 15px; background: rgba(255,193,7,0.3); padding: 15px; border-radius: 5px;">';
                echo '<strong>تحذيرات:</strong><br>';
                foreach ($warnings as $warning) {
                    echo '⚠ ' . htmlspecialchars($warning) . '<br>';
                }
                echo '</div>';
            }
            
            if (!empty($errors)) {
                echo '<div style="margin-top: 15px; background: rgba(220,53,69,0.3); padding: 15px; border-radius: 5px;">';
                echo '<strong>أخطاء:</strong><br>';
                foreach ($errors as $error) {
                    echo '✗ ' . htmlspecialchars($error) . '<br>';
                }
                echo '</div>';
            }
            
            if (empty($errors) && $percentage >= 80) {
                echo '<div style="margin-top: 20px; font-size: 1.2em; text-align: center;">';
                echo '✅ <strong>النظام جاهز للعمل!</strong>';
                echo '</div>';
            }
            
            echo '</div>';
            echo '</div>';
            ?>
        </div>
    </div>
    
    <script>
        // Auto-refresh after upload
        <?php if ($uploadResult && $uploadResult['success']): ?>
        setTimeout(function() {
            window.location.hash = 'upload-result';
        }, 500);
        <?php endif; ?>
    </script>
</body>
</html>

