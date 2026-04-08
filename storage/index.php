<?php
/**
 * يخدم الملفات المرفوعة عندما يكون DOCUMENT_ROOT = جذر المشروع (erbpro/)
 * الموقع: storage/index.php (بجانب مجلد app/)
 */
$path = isset($_GET['file']) ? trim($_GET['file'], '/') : '';
if (empty($path)) {
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $parsed = parse_url($uri);
    $path = preg_replace('#^/storage/#', '', $parsed['path'] ?? '');
    $path = trim($path, '/');
}

if (empty($path) || strpos($path, '..') !== false || strpos($path, './') !== false) {
    http_response_code(404);
    die('File not found');
}

$baseDir = dirname(__FILE__);
$storagePath = realpath($baseDir . '/app/public') ?: $baseDir . '/app/public';

if (!is_dir($storagePath)) {
    $storagePath = realpath($baseDir . '/app/private/public') ?: $baseDir . '/app/private/public';
}

if (!$storagePath || !is_dir($storagePath)) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    die("Storage not found. Base: " . $baseDir);
}

$filePath = $storagePath . '/' . $path;
$realFile = realpath($filePath);
$realStorage = realpath($storagePath);

if (!$realFile || !$realStorage || strpos($realFile, $realStorage) !== 0) {
    http_response_code(404);
    die('File not found');
}

if (!is_file($realFile)) {
    http_response_code(404);
    die('File not found');
}

$mime = mime_content_type($realFile);
if (!$mime) {
    $ext = strtolower(pathinfo($realFile, PATHINFO_EXTENSION));
    $map = ['jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','gif'=>'image/gif','webp'=>'image/webp','pdf'=>'application/pdf','svg'=>'image/svg+xml'];
    $mime = $map[$ext] ?? 'application/octet-stream';
}

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($realFile));
header('Content-Disposition: inline; filename="' . basename($path) . '"');
header('Cache-Control: public, max-age=31536000');

readfile($realFile);
exit;
