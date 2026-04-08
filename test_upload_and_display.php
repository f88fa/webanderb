<?php
/**
 * اختبار شامل: رفع صورة ثم عرضها مباشرة
 * 
 * ارفع هذا الملف إلى: public_html/test_upload_and_display.php
 */

header('Content-Type: text/html; charset=utf-8');

// تحميل Laravel
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$uploadResult = null;
$testImagePath = null;

// Handle upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_image'])) {
    try {
        $file = $request->file('test_image');
        
        if ($file && $file->isValid()) {
            // Use the helper function
            $uploadResult = upload_image_safely($file, 'test');
            
            if ($uploadResult['success']) {
                $testImagePath = $uploadResult['path'];
            }
        }
    } catch (\Exception $e) {
        $uploadResult = [
            'success' => false,
            'message' => 'خطأ: ' . $e->getMessage()
        ];
    }
}

// Get all uploaded files
$uploadsPath = storage_path('app/public/uploads');
$files = [];
if (is_dir($uploadsPath)) {
    $files = array_filter(scandir($uploadsPath), function($file) use ($uploadsPath) {
        return $file !== '.' && $file !== '..' && is_file($uploadsPath . '/' . $file);
    });
}

?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>اختبار الرفع والعرض</title>
    <style>
        body { font-family: Arial; padding: 20px; direction: rtl; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #667eea; padding-bottom: 10px; }
        .section { margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 5px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        code { background: #eee; padding: 2px 5px; border-radius: 3px; }
        .upload-form { background: #fff3cd; border: 2px solid #ffc107; padding: 20px; border-radius: 5px; margin: 20px 0; }
        .upload-form input[type="file"] { margin: 10px 0; padding: 10px; width: 100%; border: 2px dashed #667eea; border-radius: 5px; }
        .upload-form button { background: #667eea; color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .image-preview { margin: 20px 0; text-align: center; }
        .image-preview img { max-width: 500px; border: 2px solid #ddd; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table th, table td { padding: 10px; border: 1px solid #ddd; text-align: right; }
        table th { background: #667eea; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 اختبار شامل: الرفع والعرض</h1>
        
        <?php if ($uploadResult): ?>
            <div class="section">
                <h2>نتيجة الرفع</h2>
                <?php if ($uploadResult['success']): ?>
                    <p class="success">✅ <?= htmlspecialchars($uploadResult['message'] ?: 'تم رفع الصورة بنجاح!') ?></p>
                    <p><strong>المسار:</strong> <code><?= htmlspecialchars($testImagePath) ?></code></p>
                    
                    <?php
                    $fullPath = storage_path('app/public/' . $testImagePath);
                    $exists = file_exists($fullPath);
                    ?>
                    <p><strong>الملف موجود:</strong> <?= $exists ? '<span class="success">✅ نعم</span>' : '<span class="error">❌ لا</span>' ?></p>
                    <?php if ($exists): ?>
                        <p><strong>حجم الملف:</strong> <?= number_format(filesize($fullPath) / 1024, 2) ?> KB</p>
                        <p><strong>المسار الكامل:</strong> <code><?= htmlspecialchars($fullPath) ?></code></p>
                        
                        <div class="image-preview">
                            <h3>معاينة الصورة:</h3>
                            <?php
                            $imageUrl = '/storage/' . $testImagePath;
                            ?>
                            <p><strong>URL:</strong> <code><?= htmlspecialchars($imageUrl) ?></code></p>
                            <p><a href="<?= htmlspecialchars($imageUrl) ?>" target="_blank">افتح الصورة في تبويب جديد</a></p>
                            <img src="<?= htmlspecialchars($imageUrl) ?>" alt="Test Image" onerror="this.style.border='3px solid red'; this.alt='فشل تحميل الصورة - 404';">
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="error">❌ <?= htmlspecialchars($uploadResult['message']) ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="section">
            <h2>رفع صورة جديدة</h2>
            <div class="upload-form">
                <form method="POST" enctype="multipart/form-data">
                    <strong>اختر صورة:</strong><br>
                    <input type="file" name="test_image" accept="image/*" required><br>
                    <button type="submit">رفع الصورة</button>
                </form>
            </div>
        </div>
        
        <div class="section">
            <h2>الملفات المرفوعة</h2>
            <?php if (count($files) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>اسم الملف</th>
                            <th>الحجم</th>
                            <th>الرابط</th>
                            <th>التحقق</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($files as $file): ?>
                            <?php
                            $filePath = $uploadsPath . '/' . $file;
                            $relativePath = 'uploads/' . $file;
                            $fileUrl = '/storage/' . $relativePath;
                            $fileExists = file_exists($filePath);
                            ?>
                            <tr>
                                <td><code><?= htmlspecialchars($file) ?></code></td>
                                <td><?= number_format(filesize($filePath) / 1024, 2) ?> KB</td>
                                <td><a href="<?= htmlspecialchars($fileUrl) ?>" target="_blank">افتح</a></td>
                                <td><?= $fileExists ? '<span class="success">✅ موجود</span>' : '<span class="error">❌ غير موجود</span>' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="error">❌ لا توجد ملفات مرفوعة</p>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <h2>معلومات النظام</h2>
            <p><strong>مسار uploads:</strong> <code><?= htmlspecialchars($uploadsPath) ?></code></p>
            <p><strong>المجلد موجود:</strong> <?= is_dir($uploadsPath) ? '<span class="success">✅ نعم</span>' : '<span class="error">❌ لا</span>' ?></p>
            <p><strong>قابل للكتابة:</strong> <?= is_writable($uploadsPath) ? '<span class="success">✅ نعم</span>' : '<span class="error">❌ لا</span>' ?></p>
        </div>
    </div>
</body>
</html>

