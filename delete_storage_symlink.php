<?php
/**
 * Delete Broken Storage Symlink
 * 
 * This script will forcefully delete the broken storage symlink
 * Upload to public_html and run once, then delete this file
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>حذف symlink storage المعطوب</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
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
            margin: 15px 0;
            border-right: 4px solid #4CAF50;
        }
        .error {
            color: #f44336;
            background: #ffebee;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
            border-right: 4px solid #f44336;
        }
        .warning {
            color: #ff9800;
            background: #fff3e0;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
            border-right: 4px solid #ff9800;
        }
        .info {
            color: #2196F3;
            background: #e3f2fd;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
            border-right: 4px solid #2196F3;
        }
        .steps {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .steps ol {
            margin: 10px 0;
            padding-right: 25px;
        }
        .steps li {
            margin: 10px 0;
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
        <h1>🗑️ حذف symlink storage المعطوب</h1>
        
        <?php
        $basePath = dirname(__FILE__);
        $storagePath = $basePath . '/storage';
        
        echo '<div class="info">';
        echo '<strong>المسار:</strong> ' . htmlspecialchars($storagePath) . '<br>';
        echo '</div>';
        
        // Check if storage exists
        if (!file_exists($storagePath)) {
            echo '<div class="success">';
            echo '<strong>✓ مجلد storage غير موجود</strong><br>';
            echo 'يمكنك الآن إنشاء المجلدات المطلوبة.';
            echo '</div>';
            
            echo '<h2>الخطوات التالية:</h2>';
            echo '<div class="steps">';
            echo '<ol>';
            echo '<li>افتح <strong>cPanel → File Manager</strong></li>';
            echo '<li>اذهب إلى <code>public_html</code></li>';
            echo '<li>أنشئ مجلد جديد باسم <code>storage</code></li>';
            echo '<li>داخل <code>storage</code>، أنشئ مجلد <code>app</code></li>';
            echo '<li>داخل <code>storage/app</code>، أنشئ مجلد <code>public</code></li>';
            echo '<li>داخل <code>storage/app/public</code>، أنشئ مجلد <code>uploads</code></li>';
            echo '<li>عيّن الصلاحيات: <code>755</code> لجميع المجلدات</li>';
            echo '</ol>';
            echo '</div>';
            
        } else {
            // Check what it is
            $isLink = is_link($storagePath);
            $isDir = is_dir($storagePath);
            $realPath = @realpath($storagePath);
            
            echo '<div class="info">';
            echo '<strong>معلومات storage:</strong><br>';
            echo 'موجود: ✓<br>';
            echo 'نوع: ' . ($isLink ? 'Symlink' : ($isDir ? 'مجلد عادي' : 'ملف')) . '<br>';
            if ($realPath) {
                echo 'المسار الحقيقي: ' . htmlspecialchars($realPath) . '<br>';
            } else {
                echo 'المسار الحقيقي: <span style="color: red;">غير صالح (symlink معطوب)</span><br>';
            }
            echo '</div>';
            
            if ($isLink || !$realPath) {
                echo '<div class="warning">';
                echo '<strong>⚠ تم اكتشاف symlink معطوب</strong><br>';
                echo 'سيتم محاولة حذفه...';
                echo '</div>';
                
                // Try multiple methods to delete
                $deleted = false;
                
                // Method 1: unlink
                if (@unlink($storagePath)) {
                    echo '<div class="success">✓ تم حذف symlink بنجاح (طريقة 1: unlink)</div>';
                    $deleted = true;
                }
                // Method 2: rmdir (sometimes works for broken symlinks)
                else if (@rmdir($storagePath)) {
                    echo '<div class="success">✓ تم حذف symlink بنجاح (طريقة 2: rmdir)</div>';
                    $deleted = true;
                }
                // Method 3: system command
                else if (function_exists('exec')) {
                    $command = "rm -f " . escapeshellarg($storagePath) . " 2>&1";
                    @exec($command, $output, $return);
                    if ($return === 0 || !file_exists($storagePath)) {
                        echo '<div class="success">✓ تم حذف symlink بنجاح (طريقة 3: system command)</div>';
                        $deleted = true;
                    }
                }
                
                if ($deleted) {
                    echo '<div class="success">';
                    echo '<strong>✓ تم حذف symlink بنجاح!</strong><br>';
                    echo 'يمكنك الآن إنشاء المجلدات المطلوبة.';
                    echo '</div>';
                    
                    echo '<h2>الخطوات التالية:</h2>';
                    echo '<div class="steps">';
                    echo '<ol>';
                    echo '<li>افتح <strong>cPanel → File Manager</strong></li>';
                    echo '<li>اذهب إلى <code>public_html</code></li>';
                    echo '<li>أنشئ مجلد جديد باسم <code>storage</code></li>';
                    echo '<li>داخل <code>storage</code>، أنشئ مجلد <code>app</code></li>';
                    echo '<li>داخل <code>storage/app</code>، أنشئ مجلد <code>public</code></li>';
                    echo '<li>داخل <code>storage/app/public</code>، أنشئ مجلد <code>uploads</code></li>';
                    echo '<li>عيّن الصلاحيات: <code>755</code> لجميع المجلدات</li>';
                    echo '</ol>';
                    echo '</div>';
                } else {
                    echo '<div class="error">';
                    echo '<strong>✗ فشل حذف symlink تلقائياً</strong><br>';
                    echo 'يجب حذفه يدوياً عبر cPanel أو FTP.';
                    echo '</div>';
                    
                    echo '<h2>الحل اليدوي (مطلوب):</h2>';
                    echo '<div class="steps">';
                    echo '<strong>طريقة 1: عبر cPanel File Manager</strong>';
                    echo '<ol>';
                    echo '<li>افتح <strong>cPanel → File Manager</strong></li>';
                    echo '<li>اذهب إلى <code>public_html</code></li>';
                    echo '<li>ابحث عن مجلد <code>storage</code></li>';
                    echo '<li>إذا كان يظهر كـ symlink (عادة بسهم أو أيقونة مختلفة)، انقر عليه بزر الماوس الأيمن</li>';
                    echo '<li>اختر <strong>Delete</strong> أو <strong>حذف</strong></li>';
                    echo '<li>تأكد من الحذف</li>';
                    echo '<li>بعد الحذف، أنشئ مجلد عادي جديد باسم <code>storage</code></li>';
                    echo '<li>أنشئ المجلدات الفرعية: <code>storage/app/public/uploads</code></li>';
                    echo '<li>عيّن الصلاحيات: <code>755</code> للمجلدات</li>';
                    echo '</ol>';
                    
                    echo '<strong>طريقة 2: عبر FTP</strong>';
                    echo '<ol>';
                    echo '<li>اتصل عبر FTP (FileZilla أو أي عميل FTP)</li>';
                    echo '<li>اذهب إلى <code>public_html</code></li>';
                    echo '<li>ابحث عن <code>storage</code></li>';
                    echo '<li>احذفه (Delete)</li>';
                    echo '<li>أنشئ مجلد جديد باسم <code>storage</code></li>';
                    echo '<li>أنشئ المجلدات الفرعية: <code>storage/app/public/uploads</code></li>';
                    echo '<li>عيّن الصلاحيات: <code>755</code> للمجلدات</li>';
                    echo '</ol>';
                    echo '</div>';
                }
            } else {
                echo '<div class="success">';
                echo '<strong>✓ مجلد storage موجود وهو مجلد عادي (ليس symlink)</strong><br>';
                echo 'المجلد يبدو صحيحاً.';
                echo '</div>';
            }
        }
        ?>
        
        <div class="warning" style="margin-top: 30px;">
            <strong>⚠️ تحذير أمني:</strong> احذف هذا الملف (<code>delete_storage_symlink.php</code>) بعد الانتهاء!
        </div>
    </div>
</body>
</html>

