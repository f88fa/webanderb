<?php
/**
 * Create Storage Symlink/Directory Structure
 * 
 * This script creates the storage directory structure in public_html
 * Upload to public_html and run once
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إنشاء هيكل Storage</title>
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
        .steps {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 4px;
            margin: 20px 0;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 إنشاء هيكل Storage</h1>
        
        <?php
        $basePath = dirname(__FILE__);
        $storagePublicPath = $basePath . '/storage/app/public';
        $publicStoragePath = $basePath . '/storage';
        
        echo '<div class="info">';
        echo '<strong>المسار الأساسي:</strong> ' . htmlspecialchars($basePath) . '<br>';
        echo '<strong>مسار التخزين:</strong> ' . htmlspecialchars($storagePublicPath) . '<br>';
        echo '</div>';
        
        // Check if storage/app/public exists
        if (!file_exists($storagePublicPath) || !is_dir($storagePublicPath)) {
            echo '<div class="error">';
            echo '<strong>✗ مجلد storage/app/public غير موجود</strong><br>';
            echo 'يجب إنشاء المجلدات أولاً.';
            echo '</div>';
        } else {
            echo '<div class="success">';
            echo '<strong>✓ مجلد storage/app/public موجود</strong>';
            echo '</div>';
            
            // Try to create symlink
            if (file_exists($publicStoragePath)) {
                if (is_link($publicStoragePath)) {
                    echo '<div class="info">';
                    echo 'مجلد storage موجود وهو symlink<br>';
                    $linkTarget = readlink($publicStoragePath);
                    echo 'الهدف: ' . htmlspecialchars($linkTarget);
                    echo '</div>';
                } else {
                    echo '<div class="info">';
                    echo 'مجلد storage موجود وهو مجلد عادي';
                    echo '</div>';
                }
            } else {
                // Try to create symlink
                echo '<div class="info">';
                echo 'محاولة إنشاء symlink...<br>';
                echo '</div>';
                
                if (@symlink($storagePublicPath, $publicStoragePath)) {
                    echo '<div class="success">';
                    echo '<strong>✓ تم إنشاء symlink بنجاح!</strong><br>';
                    echo 'الآن يجب أن تعمل الصور عبر /storage/uploads/filename.jpg';
                    echo '</div>';
                } else {
                    echo '<div class="error">';
                    echo '<strong>✗ فشل إنشاء symlink</strong><br>';
                    echo 'سيتم استخدام الحل البديل: إنشاء مجلد storage مع index.php';
                    echo '</div>';
                    
                    // Alternative: Create storage directory with index.php
                    echo '<div class="info">';
                    echo '<strong>الحل البديل:</strong><br>';
                    echo '1. أنشئ مجلد <code>storage</code> في <code>public_html</code><br>';
                    echo '2. انسخ ملف <code>public_html_storage_index.php</code> إلى <code>storage/index.php</code><br>';
                    echo '3. يجب أن تعمل الصور الآن';
                    echo '</div>';
                }
            }
        }
        
        // Instructions
        echo '<h2>التعليمات اليدوية</h2>';
        echo '<div class="steps">';
        echo '<strong>إذا فشل الحل التلقائي:</strong><br><br>';
        echo '<strong>الطريقة 1: إنشاء symlink يدوياً</strong><br>';
        echo '1. افتح cPanel → File Manager<br>';
        echo '2. اذهب إلى <code>public_html</code><br>';
        echo '3. إذا كان مجلد <code>storage</code> موجود، احذفه<br>';
        echo '4. انقر بزر الماوس الأيمن → Create Symbolic Link<br>';
        echo '5. الهدف: <code>storage/app/public</code><br>';
        echo '6. الاسم: <code>storage</code><br><br>';
        
        echo '<strong>الطريقة 2: استخدام index.php (موصى به)</strong><br>';
        echo '1. أنشئ مجلد <code>storage</code> في <code>public_html</code><br>';
        echo '2. انسخ محتوى ملف <code>public_html_storage_index.php</code><br>';
        echo '3. أنشئ ملف جديد <code>storage/index.php</code> والصق المحتوى<br>';
        echo '4. يجب أن تعمل الصور الآن عبر /storage/uploads/filename.jpg';
        echo '</div>';
        ?>
        
        <div class="error" style="margin-top: 30px;">
            <strong>⚠️ تحذير أمني:</strong> احذف هذا الملف بعد الانتهاء!
        </div>
    </div>
</body>
</html>

