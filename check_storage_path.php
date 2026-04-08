<?php
/**
 * Storage Path Checker
 * 
 * ارفع هذا الملف إلى public_html/check_storage_path.php
 * ثم افتحه في المتصفح لمعرفة المسار الصحيح
 */

echo "<h1>فحص مسار التخزين</h1>";
echo "<pre>";

echo "=== معلومات السيرفر ===\n";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'غير محدد') . "\n";
echo "SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'غير محدد') . "\n";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'غير محدد') . "\n\n";

$basePath = dirname(__FILE__);
$docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';

echo "=== المسارات المحتملة ===\n";

$possiblePaths = [];

// Standard Laravel
$projectRoot = dirname($basePath);
$possiblePaths[] = $projectRoot . '/storage/app/public';

// Shared hosting
if ($docRoot) {
    if (basename($docRoot) === 'public_html') {
        $projectRoot = dirname($docRoot);
        $possiblePaths[] = $projectRoot . '/storage/app/public';
    }
    $possiblePaths[] = $docRoot . '/../storage/app/public';
    $possiblePaths[] = dirname($docRoot) . '/storage/app/public';
}

foreach ($possiblePaths as $index => $path) {
    $realPath = realpath($path);
    $exists = $realPath && is_dir($realPath);
    $status = $exists ? "✅ موجود" : "❌ غير موجود";
    
    echo ($index + 1) . ". $path\n";
    echo "   Realpath: " . ($realPath ?: 'غير موجود') . "\n";
    echo "   Status: $status\n";
    
    if ($exists) {
        // Check if uploads directory exists
        $uploadsPath = $realPath . '/uploads';
        $uploadsExists = is_dir($uploadsPath);
        echo "   uploads/: " . ($uploadsExists ? "✅ موجود" : "❌ غير موجود") . "\n";
        
        if ($uploadsExists) {
            $files = scandir($uploadsPath);
            $files = array_filter($files, function($file) {
                return $file !== '.' && $file !== '..';
            });
            echo "   عدد الملفات: " . count($files) . "\n";
            if (count($files) > 0) {
                echo "   أمثلة على الملفات:\n";
                foreach (array_slice($files, 0, 5) as $file) {
                    echo "      - $file\n";
                }
            }
        }
    }
    echo "\n";
}

echo "=== المسار الموصى به ===\n";
$storagePath = null;
foreach ($possiblePaths as $path) {
    $realPath = realpath($path);
    if ($realPath && is_dir($realPath)) {
        $storagePath = $realPath;
        echo "✅ المسار الصحيح: $realPath\n";
        break;
    }
}

if (!$storagePath) {
    echo "❌ لم يتم العثور على مسار التخزين!\n";
    echo "\nيرجى التحقق من:\n";
    echo "1. وجود مجلد storage/app/public/\n";
    echo "2. الصلاحيات على المجلد (755)\n";
    echo "3. المسار الصحيح في السيرفر\n";
}

echo "</pre>";

