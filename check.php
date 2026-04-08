<?php
/**
 * Laravel Environment & Permissions Checker
 * هذا الملف لفحص صلاحيات المجلدات وإعدادات السيرفر
 */

header('Content-Type: text/html; charset=utf-8');

echo "<h2>تقرير فحص بيئة العمل لمشروع (وصال)</h2>";
echo "<hr>";

// 1. فحص إصدار PHP
echo "<b>إصدار PHP الحالي:</b> " . phpversion() . "<br>";

// 2. فحص ملف .env
if (file_exists('.env')) {
    echo "<span style='color:green'>✔ ملف .env موجود.</span><br>";
} else {
    echo "<span style='color:red'>✘ ملف .env غير موجود! (قد يكون هذا سبب خطأ 500).</span><br>";
}

// 3. فحص المجلدات الحساسة وصلاحياتها
$directories = [
    'storage',
    'storage/logs',
    'storage/framework/views',
    'bootstrap/cache',
    'resources/views/wesal/pages/beneficiaries/sections'
];

echo "<h3>فحص المجلدات والصلاحيات:</h3>";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>
        <tr>
            <th>المجلد</th>
            <th>موجود؟</th>
            <th>قابل للكتابة؟</th>
            <th>الصلاحيات الحالية</th>
        </tr>";

foreach ($directories as $dir) {
    $exists = is_dir($dir) ? "<span style='color:green'>نعم</span>" : "<span style='color:red'>لا</span>";
    $writable = is_writable($dir) ? "<span style='color:green'>نعم</span>" : "<span style='color:red'>لا (سبب محتمل لـ 500)</span>";
    $perms = file_exists($dir) ? substr(sprintf('%o', fileperms($dir)), -4) : "N/A";
    
    echo "<tr>
            <td>$dir</td>
            <td>$exists</td>
            <td>$writable</td>
            <td>$perms</td>
          </tr>";
}
echo "</table>";

// 4. فحص ملفات الـ View المفقودة (بناءً على الخطأ السابق)
echo "<h3>فحص ملفات الـ View المطلوبة:</h3>";
$target_view = 'resources/views/wesal/pages/beneficiaries/sections/create.blade.php';
if (file_exists($target_view)) {
    echo "<span style='color:green'>✔ ملف create.blade.php موجود في المسار الصحيح.</span><br>";
} else {
    echo "<span style='color:red'>✘ ملف create.blade.php مفقود! (هذا هو سبب خطأ InvalidArgumentException).</span><br>";
}

// 5. فحص الإضافات المطلوبة لارافل
echo "<h3>فحص إضافات PHP المطلوبة:</h3>";
$extensions = ['openssl', 'pdo', 'mbstring', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath'];
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✔ $ext: <span style='color:green'>مفعلة</span><br>";
    } else {
        echo "✘ $ext: <span style='color:red'>غير مفعلة!</span><br>";
    }
}

echo "<hr><p><b>ملاحظة:</b> بعد الانتهاء من الفحص، قم بحذف هذا الملف فوراً لأسباب أمنية.</p>";