<?php
/**
 * Storage Fix Script for Shared Hosting (Hostinger)
 * 
 * Upload this file to public_html root directory
 * Access it via: https://yourdomain.com/storage_fix.php
 * 
 * This script will:
 * 1. Check storage structure
 * 2. Fix broken symlinks
 * 3. Create missing directories
 * 4. Set correct permissions
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
    <title>إصلاح التخزين - Storage Fix Tool</title>
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
        .action {
            color: #9C27B0;
            background: #f3e5f5;
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
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 10px 5px;
        }
        .btn:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 إصلاح نظام التخزين - Storage Fix Tool</h1>
        
        <?php
        // Get base paths
        $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
        $scriptPath = __FILE__;
        $basePath = dirname($scriptPath);
        
        echo '<div class="info">';
        echo '<strong>📁 المسارات:</strong><br>';
        echo 'Document Root: ' . htmlspecialchars($documentRoot) . '<br>';
        echo 'Base Path: ' . htmlspecialchars($basePath) . '<br>';
        echo '</div>';
        
        $fixes = [];
        $errors = [];
        
        // Step 1: Check and fix storage directory
        echo '<h2>1. فحص وإصلاح مجلد storage</h2>';
        $storagePath = $basePath . '/storage';
        
        // First, check if storage exists and what it is
        if (file_exists($storagePath)) {
            if (is_link($storagePath)) {
                echo '<div class="warning">⚠ مجلد storage هو symlink معطوب - سيتم حذفه</div>';
                // Try to delete the symlink
                if (@unlink($storagePath)) {
                    echo '<div class="success">✓ تم حذف symlink المعطوب</div>';
                    $fixes[] = 'Removed broken storage symlink';
                } else {
                    echo '<div class="error">✗ فشل حذف symlink - سيتم المحاولة بطريقة أخرى</div>';
                    // Try using rmdir if it's a broken symlink
                    if (@rmdir($storagePath)) {
                        echo '<div class="success">✓ تم حذف symlink المعطوب (طريقة بديلة)</div>';
                        $fixes[] = 'Removed broken storage symlink (alternative method)';
                    } else {
                        echo '<div class="error">✗ فشل حذف symlink - يجب حذفه يدوياً عبر FTP/cPanel</div>';
                        $errors[] = 'Cannot delete broken storage symlink - manual deletion required';
                        echo '<div class="info"><strong>الحل اليدوي:</strong> احذف مجلد storage عبر File Manager في cPanel أو FTP</div>';
                    }
                }
            } else if (is_dir($storagePath)) {
                // Check if it's a real directory or broken
                $realPath = realpath($storagePath);
                if ($realPath && $realPath === $storagePath) {
                    echo '<div class="success">✓ مجلد storage موجود وهو مجلد عادي</div>';
                } else {
                    echo '<div class="warning">⚠ مجلد storage موجود لكن قد يكون معطوباً</div>';
                    // Try to remove it
                    if (@rmdir($storagePath)) {
                        echo '<div class="success">✓ تم حذف المجلد المعطوب</div>';
                    } else {
                        echo '<div class="error">✗ لا يمكن حذف المجلد - قد يحتوي على ملفات</div>';
                    }
                }
            } else {
                echo '<div class="warning">⚠ storage موجود لكنه ليس مجلد ولا symlink</div>';
                if (@unlink($storagePath)) {
                    echo '<div class="success">✓ تم حذف العنصر</div>';
                }
            }
        }
        
        // Create storage directory if it doesn't exist
        if (!file_exists($storagePath)) {
            echo '<div class="action">🔨 إنشاء مجلد storage...</div>';
            // Use @ to suppress warnings and check result
            $result = @mkdir($storagePath, 0755, true);
            if ($result && file_exists($storagePath) && is_dir($storagePath)) {
                echo '<div class="success">✓ تم إنشاء مجلد storage</div>';
                $fixes[] = 'Created storage directory';
            } else {
                echo '<div class="error">✗ فشل إنشاء مجلد storage</div>';
                $errors[] = 'Cannot create storage directory';
                echo '<div class="info"><strong>الحل البديل:</strong> أنشئ المجلد يدوياً عبر File Manager في cPanel</div>';
            }
        } else {
            // Verify it's a real directory now
            if (is_dir($storagePath) && !is_link($storagePath)) {
                $realPath = realpath($storagePath);
                if ($realPath && $realPath === $storagePath) {
                    echo '<div class="success">✓ مجلد storage موجود ويعمل بشكل صحيح</div>';
                } else {
                    echo '<div class="error">✗ مجلد storage موجود لكنه لا يزال معطوباً</div>';
                }
            }
        }
        
        // Step 2: Check and create storage/app
        echo '<h2>2. فحص وإصلاح مجلد storage/app</h2>';
        $storageAppPath = $basePath . '/storage/app';
        
        // First verify storage exists and is valid
        if (!file_exists($storagePath) || !is_dir($storagePath) || is_link($storagePath)) {
            echo '<div class="error">✗ يجب إصلاح مجلد storage أولاً</div>';
            $errors[] = 'Storage directory must be fixed first';
        } else {
            if (!file_exists($storageAppPath)) {
                echo '<div class="action">🔨 إنشاء مجلد storage/app...</div>';
                $result = @mkdir($storageAppPath, 0755, true);
                if ($result && file_exists($storageAppPath) && is_dir($storageAppPath)) {
                    echo '<div class="success">✓ تم إنشاء مجلد storage/app</div>';
                    $fixes[] = 'Created storage/app directory';
                } else {
                    echo '<div class="error">✗ فشل إنشاء مجلد storage/app</div>';
                    $errors[] = 'Cannot create storage/app directory';
                    echo '<div class="info">السبب المحتمل: مجلد storage لا يزال معطوباً</div>';
                }
            } else {
                if (is_dir($storageAppPath) && !is_link($storageAppPath)) {
                    echo '<div class="success">✓ مجلد storage/app موجود</div>';
                } else {
                    echo '<div class="warning">⚠ storage/app موجود لكنه symlink معطوب</div>';
                    if (@unlink($storageAppPath) || @rmdir($storageAppPath)) {
                        echo '<div class="success">✓ تم حذف symlink المعطوب</div>';
                        if (@mkdir($storageAppPath, 0755, true)) {
                            echo '<div class="success">✓ تم إنشاء مجلد storage/app</div>';
                            $fixes[] = 'Recreated storage/app directory';
                        }
                    }
                }
            }
        }
        
        // Step 3: Check and create storage/app/public
        echo '<h2>3. فحص وإصلاح مجلد storage/app/public</h2>';
        $storagePublicPath = $basePath . '/storage/app/public';
        
        if (!file_exists($storageAppPath) || !is_dir($storageAppPath) || is_link($storageAppPath)) {
            echo '<div class="error">✗ يجب إصلاح مجلد storage/app أولاً</div>';
        } else {
            if (!file_exists($storagePublicPath)) {
                echo '<div class="action">🔨 إنشاء مجلد storage/app/public...</div>';
                $result = @mkdir($storagePublicPath, 0755, true);
                if ($result && file_exists($storagePublicPath) && is_dir($storagePublicPath)) {
                    echo '<div class="success">✓ تم إنشاء مجلد storage/app/public</div>';
                    $fixes[] = 'Created storage/app/public directory';
                } else {
                    echo '<div class="error">✗ فشل إنشاء مجلد storage/app/public</div>';
                    $errors[] = 'Cannot create storage/app/public directory';
                }
            } else {
                if (is_dir($storagePublicPath) && !is_link($storagePublicPath)) {
                    echo '<div class="success">✓ مجلد storage/app/public موجود</div>';
                } else {
                    echo '<div class="warning">⚠ storage/app/public موجود لكنه symlink معطوب</div>';
                    if (@unlink($storagePublicPath) || @rmdir($storagePublicPath)) {
                        if (@mkdir($storagePublicPath, 0755, true)) {
                            echo '<div class="success">✓ تم إعادة إنشاء مجلد storage/app/public</div>';
                            $fixes[] = 'Recreated storage/app/public directory';
                        }
                    }
                }
            }
        }
        
        // Step 4: Check and create storage/app/public/uploads
        echo '<h2>4. فحص وإصلاح مجلد storage/app/public/uploads</h2>';
        $storageUploadsPath = $basePath . '/storage/app/public/uploads';
        
        if (!file_exists($storagePublicPath) || !is_dir($storagePublicPath) || is_link($storagePublicPath)) {
            echo '<div class="error">✗ يجب إصلاح مجلد storage/app/public أولاً</div>';
        } else {
            if (!file_exists($storageUploadsPath)) {
                echo '<div class="action">🔨 إنشاء مجلد storage/app/public/uploads...</div>';
                $result = @mkdir($storageUploadsPath, 0755, true);
                if ($result && file_exists($storageUploadsPath) && is_dir($storageUploadsPath)) {
                    echo '<div class="success">✓ تم إنشاء مجلد storage/app/public/uploads</div>';
                    $fixes[] = 'Created storage/app/public/uploads directory';
                } else {
                    echo '<div class="error">✗ فشل إنشاء مجلد storage/app/public/uploads</div>';
                    $errors[] = 'Cannot create storage/app/public/uploads directory';
                }
            } else {
                if (is_dir($storageUploadsPath) && !is_link($storageUploadsPath)) {
                    echo '<div class="success">✓ مجلد storage/app/public/uploads موجود</div>';
                    
                    // List files
                    $files = glob($storageUploadsPath . '/*');
                    if ($files) {
                        echo '<div class="info"><strong>الملفات الموجودة (' . count($files) . '):</strong></div>';
                        echo '<pre>';
                        foreach (array_slice($files, 0, 10) as $file) {
                            if (is_file($file)) {
                                echo basename($file) . ' (' . number_format(filesize($file) / 1024, 2) . ' KB)' . "\n";
                            }
                        }
                        if (count($files) > 10) {
                            echo '... و ' . (count($files) - 10) . ' ملفات أخرى';
                        }
                        echo '</pre>';
                    }
                } else {
                    echo '<div class="warning">⚠ storage/app/public/uploads موجود لكنه symlink معطوب</div>';
                    if (@unlink($storageUploadsPath) || @rmdir($storageUploadsPath)) {
                        if (@mkdir($storageUploadsPath, 0755, true)) {
                            echo '<div class="success">✓ تم إعادة إنشاء مجلد storage/app/public/uploads</div>';
                            $fixes[] = 'Recreated storage/app/public/uploads directory';
                        }
                    }
                }
            }
        }
        
        // Step 5: Set permissions
        echo '<h2>5. تعيين الصلاحيات</h2>';
        $dirs = [
            $storagePath => 'storage',
            $storageAppPath => 'storage/app',
            $storagePublicPath => 'storage/app/public',
            $storageUploadsPath => 'storage/app/public/uploads',
        ];
        
        foreach ($dirs as $dir => $name) {
            if (file_exists($dir)) {
                if (chmod($dir, 0755)) {
                    echo '<div class="success">✓ تم تعيين صلاحيات 755 لمجلد ' . $name . '</div>';
                } else {
                    echo '<div class="warning">⚠ فشل تعيين صلاحيات لمجلد ' . $name . '</div>';
                }
            }
        }
        
        // Step 6: Check public/storage symlink
        echo '<h2>6. فحص symlink في public/storage</h2>';
        $publicStoragePath = $basePath . '/public/storage';
        
        if (file_exists($publicStoragePath)) {
            if (is_link($publicStoragePath)) {
                $linkTarget = readlink($publicStoragePath);
                echo '<div class="info">symlink موجود ويشير إلى: ' . htmlspecialchars($linkTarget) . '</div>';
                
                if (file_exists($linkTarget)) {
                    echo '<div class="success">✓ symlink يعمل بشكل صحيح</div>';
                } else {
                    echo '<div class="warning">⚠ symlink يشير إلى مسار غير موجود</div>';
                    echo '<div class="action">🔨 حذف symlink المعطوب...</div>';
                    if (unlink($publicStoragePath)) {
                        echo '<div class="success">✓ تم حذف symlink المعطوب</div>';
                        $fixes[] = 'Removed broken public/storage symlink';
                    }
                }
            } else {
                echo '<div class="warning">⚠ public/storage موجود لكنه ليس symlink</div>';
            }
        } else {
            echo '<div class="info">public/storage غير موجود - هذا طبيعي على الاستضافة المشتركة</div>';
            echo '<div class="info">Laravel route سيتولى تقديم الملفات</div>';
        }
        
        // Step 7: Test file access
        echo '<h2>7. اختبار الوصول للملفات</h2>';
        $testImageName = 'about_1769959120.jpg';
        $testImagePath = $storageUploadsPath . '/' . $testImageName;
        
        if (file_exists($testImagePath)) {
            echo '<div class="success">✓ ملف الاختبار موجود: ' . htmlspecialchars($testImageName) . '</div>';
            
            $fileSize = filesize($testImagePath);
            $filePerms = substr(sprintf('%o', fileperms($testImagePath)), -4);
            
            echo '<div class="info">الحجم: ' . number_format($fileSize / 1024, 2) . ' KB</div>';
            echo '<div class="info">الصلاحيات: ' . $filePerms . '</div>';
            
            // Set file permissions
            if (chmod($testImagePath, 0644)) {
                echo '<div class="success">✓ تم تعيين صلاحيات 644 للملف</div>';
            }
            
            // Test URL
            $testUrl = '/storage/uploads/' . $testImageName;
            echo '<div class="info"><strong>رابط الاختبار:</strong> <a href="' . htmlspecialchars($testUrl) . '" target="_blank">' . htmlspecialchars($testUrl) . '</a></div>';
        } else {
            echo '<div class="warning">⚠ ملف الاختبار غير موجود</div>';
            echo '<div class="info">ارفع صورة جديدة من لوحة التحكم وستُحفظ في المكان الصحيح</div>';
        }
        
        // Summary
        echo '<h2>8. ملخص الإصلاحات</h2>';
        if (!empty($fixes)) {
            echo '<div class="success"><strong>الإصلاحات المنفذة:</strong></div>';
            echo '<ul>';
            foreach ($fixes as $fix) {
                echo '<li>' . htmlspecialchars($fix) . '</li>';
            }
            echo '</ul>';
        } else {
            echo '<div class="info">لا توجد إصلاحات مطلوبة - كل شيء يعمل بشكل صحيح</div>';
        }
        
        if (!empty($errors)) {
            echo '<div class="error"><strong>الأخطاء:</strong></div>';
            echo '<ul>';
            foreach ($errors as $error) {
                echo '<li>' . htmlspecialchars($error) . '</li>';
            }
            echo '</ul>';
        }
        
        // Final recommendations
        echo '<h2>9. التوصيات النهائية</h2>';
        echo '<div class="info">';
        echo '<strong>بعد الإصلاح:</strong><br>';
        echo '1. تأكد من أن Laravel route موجود في routes/web.php<br>';
        echo '2. تأكد من أن .htaccess يحتوي على قواعد لتوجيه /storage/* إلى Laravel<br>';
        echo '3. جرب رفع صورة جديدة من لوحة التحكم<br>';
        echo '4. اختبر الرابط: /storage/uploads/filename.jpg<br>';
        echo '5. <strong>احذف هذا الملف</strong> بعد الانتهاء لأسباب أمنية<br>';
        echo '</div>';
        
        // Test link
        if (file_exists($testImagePath)) {
            echo '<div style="margin-top: 20px; text-align: center;">';
            echo '<a href="' . htmlspecialchars($testUrl) . '" target="_blank" class="btn">اختبار الصورة الآن</a>';
            echo '</div>';
        }
        ?>
        
        <div style="margin-top: 30px; padding: 15px; background: #fff3cd; border-radius: 4px; border-left: 4px solid #ffc107;">
            <strong>⚠️ تحذير أمني:</strong> احذف هذا الملف (storage_fix.php) بعد الانتهاء من الإصلاح!
        </div>
    </div>
</body>
</html>

