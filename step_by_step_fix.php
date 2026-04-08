<?php
/**
 * Step by Step Storage Fix
 * 
 * This file will guide you through fixing storage step by step
 * Upload to public_html and follow the instructions
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إصلاح خطوة بخطوة</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .step {
            background: white;
            padding: 25px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-right: 5px solid #667eea;
        }
        .step-number {
            background: #667eea;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5em;
            font-weight: bold;
            margin-left: 15px;
        }
        .step h2 {
            color: #333;
            margin: 0;
            display: inline-block;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-right: 4px solid #28a745;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-right: 4px solid #dc3545;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-right: 4px solid #ffc107;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-right: 4px solid #17a2b8;
        }
        .code-block {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            direction: ltr;
            text-align: left;
        }
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background: #218838;
        }
        .btn-primary {
            background: #667eea;
        }
        .btn-primary:hover {
            background: #5568d3;
        }
        code {
            background: #f4f4f4;
            padding: 2px 8px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            color: #e83e8c;
        }
        .instructions {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .instructions ol {
            margin: 10px 0;
            padding-right: 25px;
        }
        .instructions li {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="color: #667eea;">🔧 إصلاح نظام التخزين - خطوة بخطوة</h1>
        <p style="color: #666;">اتبع الخطوات بالترتيب</p>
    </div>

    <?php
    $basePath = dirname(__FILE__);
    $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
    
    // ========== STEP 1 ==========
    echo '<div class="step">';
    echo '<h2><span class="step-number">1</span> فحص البيئة الحالية</h2>';
    
    echo '<div class="info">';
    echo '<strong>المعلومات الحالية:</strong><br>';
    echo 'Document Root: <code>' . htmlspecialchars($documentRoot) . '</code><br>';
    echo 'Base Path: <code>' . htmlspecialchars($basePath) . '</code><br>';
    echo '</div>';
    
    // Check directories
    $dirs = [
        'storage' => $basePath . '/storage',
        'storage/app' => $basePath . '/storage/app',
        'storage/app/public' => $basePath . '/storage/app/public',
        'storage/app/public/uploads' => $basePath . '/storage/app/public/uploads',
    ];
    
    $allDirsExist = true;
    foreach ($dirs as $name => $path) {
        if (file_exists($path) && is_dir($path)) {
            echo '<div class="success">✓ ' . htmlspecialchars($name) . ' موجود</div>';
        } else {
            echo '<div class="error">✗ ' . htmlspecialchars($name) . ' غير موجود</div>';
            $allDirsExist = false;
        }
    }
    
    if (!$allDirsExist) {
        echo '<div class="warning">';
        echo '<strong>⚠ يجب إنشاء المجلدات المفقودة أولاً</strong><br>';
        echo 'انتقل إلى الخطوة 2';
        echo '</div>';
    } else {
        echo '<div class="success">';
        echo '<strong>✅ جميع المجلدات موجودة - يمكنك الانتقال للخطوة 2</strong>';
        echo '</div>';
    }
    
    echo '</div>';
    
    // ========== STEP 2 ==========
    echo '<div class="step">';
    echo '<h2><span class="step-number">2</span> إنشاء المجلدات المطلوبة</h2>';
    
    if (!$allDirsExist) {
        echo '<div class="instructions">';
        echo '<strong>الطريقة 1: عبر cPanel File Manager (موصى به)</strong>';
        echo '<ol>';
        echo '<li>افتح <strong>cPanel → File Manager</strong></li>';
        echo '<li>اذهب إلى <code>public_html</code></li>';
        echo '<li>أنشئ المجلدات التالية بالترتيب:</li>';
        echo '<ul>';
        echo '<li><code>storage</code></li>';
        echo '<li>داخل <code>storage</code>، أنشئ <code>app</code></li>';
        echo '<li>داخل <code>storage/app</code>، أنشئ <code>public</code></li>';
        echo '<li>داخل <code>storage/app/public</code>، أنشئ <code>uploads</code></li>';
        echo '</ul>';
        echo '<li>عيّن الصلاحيات <code>755</code> لجميع المجلدات</li>';
        echo '</ol>';
        echo '</div>';
        
        echo '<div class="info">';
        echo '<strong>الطريقة 2: محاولة إنشاء تلقائي</strong><br>';
        echo '<form method="POST" style="margin-top: 15px;">';
        echo '<input type="hidden" name="action" value="create_dirs">';
        echo '<button type="submit" class="btn btn-primary">🔨 إنشاء المجلدات تلقائياً</button>';
        echo '</form>';
        echo '</div>';
        
        if ($_POST['action'] ?? '' === 'create_dirs') {
            $created = [];
            foreach ($dirs as $name => $path) {
                if (!file_exists($path)) {
                    if (@mkdir($path, 0755, true)) {
                        $created[] = $name;
                        echo '<div class="success">✓ تم إنشاء ' . htmlspecialchars($name) . '</div>';
                    } else {
                        echo '<div class="error">✗ فشل إنشاء ' . htmlspecialchars($name) . '</div>';
                    }
                }
            }
            if (!empty($created)) {
                echo '<div class="success" style="margin-top: 15px;">';
                echo '<strong>✅ تم إنشاء ' . count($created) . ' مجلد</strong><br>';
                echo 'حدّث الصفحة للتحقق';
                echo '</div>';
            }
        }
    } else {
        echo '<div class="success">';
        echo '<strong>✅ جميع المجلدات موجودة - لا حاجة لإنشاء</strong>';
        echo '</div>';
    }
    
    echo '</div>';
    
    // ========== STEP 3 ==========
    echo '<div class="step">';
    echo '<h2><span class="step-number">3</span> إنشاء storage/index.php</h2>';
    
    $storageIndexPath = $basePath . '/storage/index.php';
    $storageIndexExists = file_exists($storageIndexPath);
    
    if ($storageIndexExists) {
        echo '<div class="success">';
        echo '<strong>✓ storage/index.php موجود</strong><br>';
        echo 'المسار: <code>' . htmlspecialchars($storageIndexPath) . '</code>';
        echo '</div>';
    } else {
        echo '<div class="error">';
        echo '<strong>✗ storage/index.php غير موجود</strong><br>';
        echo 'يجب إنشاؤه الآن';
        echo '</div>';
        
        echo '<div class="instructions">';
        echo '<strong>الطريقة 1: إنشاء تلقائي (موصى به)</strong><br>';
        echo '<form method="POST" style="margin-top: 15px;">';
        echo '<input type="hidden" name="action" value="create_index">';
        echo '<button type="submit" class="btn btn-primary">🔨 إنشاء storage/index.php تلقائياً</button>';
        echo '</form>';
        echo '</div>';
        
        echo '<div class="instructions">';
        echo '<strong>الطريقة 2: إنشاء يدوي</strong>';
        echo '<ol>';
        echo '<li>افتح <strong>cPanel → File Manager</strong></li>';
        echo '<li>اذهب إلى <code>public_html/storage</code></li>';
        echo '<li>أنشئ ملف جديد باسم <code>index.php</code></li>';
        echo '<li>انسخ المحتوى من المربع أدناه والصقه في الملف</li>';
        echo '</ol>';
        echo '</div>';
        
        // Show the content
        $indexContent = file_get_contents(__DIR__ . '/public_html_storage_index.php');
        if ($indexContent) {
            echo '<div class="info">';
            echo '<strong>محتوى الملف المطلوب:</strong>';
            echo '<div class="code-block">' . htmlspecialchars($indexContent) . '</div>';
            echo '</div>';
        }
        
        if ($_POST['action'] ?? '' === 'create_index') {
            // Ensure storage directory exists
            $storageDir = dirname($storageIndexPath);
            if (!file_exists($storageDir)) {
                @mkdir($storageDir, 0755, true);
            }
            
            // Get content
            $sourceFile = __DIR__ . '/public_html_storage_index.php';
            if (file_exists($sourceFile)) {
                $content = file_get_contents($sourceFile);
            } else {
                // Fallback content
                $content = '<?php
$requestUri = $_SERVER[\'REQUEST_URI\'] ?? \'\';
$parsedUrl = parse_url($requestUri);
$path = $parsedUrl[\'path\'] ?? \'\';
$path = preg_replace(\'#^/storage/#\', \'\', $path);
$path = preg_replace(\'#^storage/#\', \'\', $path);
$path = ltrim($path, \'/\');

if (empty($path) || strpos($path, \'..\') !== false || strpos($path, \'./\') !== false) {
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
            
            if (@file_put_contents($storageIndexPath, $content)) {
                @chmod($storageIndexPath, 0644);
                echo '<div class="success" style="margin-top: 15px;">';
                echo '<strong>✅ تم إنشاء storage/index.php بنجاح!</strong><br>';
                echo 'حدّث الصفحة للتحقق';
                echo '</div>';
            } else {
                echo '<div class="error" style="margin-top: 15px;">';
                echo '<strong>✗ فشل إنشاء الملف</strong><br>';
                echo 'استخدم الطريقة اليدوية أعلاه';
                echo '</div>';
            }
        }
    }
    
    echo '</div>';
    
    // ========== STEP 4 ==========
    echo '<div class="step">';
    echo '<h2><span class="step-number">4</span> اختبار النظام</h2>';
    
    // Check if we can test
    $uploadsPath = $basePath . '/storage/app/public/uploads';
    $testFiles = [];
    if (file_exists($uploadsPath)) {
        $testFiles = glob($uploadsPath . '/*');
        $testFiles = array_filter($testFiles, 'is_file');
    }
    
    if (empty($testFiles)) {
        echo '<div class="warning">';
        echo '<strong>⚠ لا توجد ملفات للاختبار</strong><br>';
        echo 'ارفع صورة من لوحة التحكم أولاً، ثم جرب الرابط';
        echo '</div>';
    } else {
        echo '<div class="info">';
        echo '<strong>الملفات الموجودة (' . count($testFiles) . '):</strong><br>';
        echo '</div>';
        
        foreach (array_slice($testFiles, 0, 5) as $file) {
            $fileName = basename($file);
            $fileUrl = '/storage/uploads/' . $fileName;
            
            echo '<div style="padding: 10px; margin: 10px 0; background: #f8f9fa; border-radius: 5px;">';
            echo '<strong>' . htmlspecialchars($fileName) . '</strong><br>';
            echo 'الرابط: <code>' . htmlspecialchars($fileUrl) . '</code><br>';
            echo '<a href="' . htmlspecialchars($fileUrl) . '" target="_blank" class="btn">🧪 اختبار الرابط</a>';
            echo '</div>';
        }
    }
    
    echo '</div>';
    
    // ========== STEP 5 ==========
    echo '<div class="step">';
    echo '<h2><span class="step-number">5</span> الملخص النهائي</h2>';
    
    $allGood = $allDirsExist && $storageIndexExists;
    
    if ($allGood) {
        echo '<div class="success" style="font-size: 1.2em; text-align: center; padding: 30px;">';
        echo '<strong>✅ النظام جاهز للعمل!</strong><br><br>';
        echo 'جميع المتطلبات موجودة:<br>';
        echo '✓ المجلدات موجودة<br>';
        echo '✓ storage/index.php موجود<br>';
        echo '<br>';
        echo 'يمكنك الآن رفع الصور من لوحة التحكم وستعمل تلقائياً!';
        echo '</div>';
    } else {
        echo '<div class="warning">';
        echo '<strong>⚠ يجب إكمال الخطوات أعلاه</strong><br>';
        if (!$allDirsExist) {
            echo '✗ بعض المجلدات مفقودة - أكمل الخطوة 2<br>';
        }
        if (!$storageIndexExists) {
            echo '✗ storage/index.php مفقود - أكمل الخطوة 3<br>';
        }
        echo '</div>';
    }
    
    echo '</div>';
    ?>
    
    <div style="text-align: center; margin-top: 40px; padding: 20px; background: #fff3cd; border-radius: 8px;">
        <strong>⚠️ تحذير أمني:</strong> احذف هذا الملف (<code>step_by_step_fix.php</code>) بعد الانتهاء من الإصلاح!
    </div>
</body>
</html>

