<?php
/**
 * Direct Storage File Server for Hostinger Shared Hosting
 * 
 * ارفع هذا الملف إلى: public_html/storage/index.php
 * 
 * هذا الملف يعمل تلقائياً مع أي مشروع Laravel على Hostinger
 * يدعم symlinks ومسارات storage/app/public و storage/app/private/public
 */

// Get the requested file path
$path = '';

// Method 1: From query string (from .htaccess RewriteRule with ?file=$1)
if (isset($_GET['file']) && !empty($_GET['file'])) {
    $path = $_GET['file'];
}

// Method 2: From REQUEST_URI
if (empty($path)) {
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    if (!empty($requestUri)) {
        $parsedUrl = parse_url($requestUri);
        $path = $parsedUrl['path'] ?? '';
        $path = preg_replace('#^/storage/#', '', $path);
        $path = preg_replace('#^storage/#', '', $path);
    }
}

// Clean up
$path = trim($path, '/');

// Security check
if (empty($path) || strpos($path, '..') !== false || strpos($path, './') !== false) {
    http_response_code(404);
    die('File not found');
}

// Get storage path - AUTO-DETECT FOR HOSTINGER
$docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
$basePath = dirname(__FILE__);

// Determine project root from DOCUMENT_ROOT
$projectRoot = null;
if ($docRoot && preg_match('#/home/([^/]+)/domains/([^/]+)/public_html#', $docRoot, $matches)) {
    // Hostinger pattern: /home/u298155993/domains/greenatmosphere.org.sa/public_html
    $projectRoot = '/home/' . $matches[1] . '/domains/' . $matches[2];
} elseif ($docRoot && (basename($docRoot) === 'public_html' || strpos($docRoot, 'public_html') !== false)) {
    // General shared hosting: go up from public_html
    $projectRoot = dirname($docRoot);
} else {
    // Fallback: from this file's location
    $projectRoot = dirname(dirname($basePath));
}

// Try to find storage/app/public
$storagePath = null;
$possiblePaths = [];

// Priority 1: Standard path (may be symlink)
$possiblePaths[] = $projectRoot . '/storage/app/public';

// Priority 2: Real path (if symlink exists)
$possiblePaths[] = $projectRoot . '/storage/app/private/public';

// Priority 3: From this file's location
$possiblePaths[] = dirname(dirname($basePath)) . '/storage/app/public';
$possiblePaths[] = dirname(dirname($basePath)) . '/storage/app/private/public';

// Find the first existing path and resolve symlinks
foreach ($possiblePaths as $tryPath) {
    $realPath = realpath($tryPath);
    if ($realPath && is_dir($realPath)) {
        $storagePath = $realPath;
        break;
    }
}

// Final verification
if (!$storagePath) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    die("Storage directory not found.\n\nProject Root: " . $projectRoot . "\n\nTried paths:\n" . implode("\n", $possiblePaths) . "\n\nDOCUMENT_ROOT: " . $docRoot . "\nBase path: " . $basePath);
}

// Build full file path
$filePath = $storagePath . '/' . $path;

// Normalize and verify (resolve symlinks)
$realFilePath = realpath($filePath);

// Security: ensure file is within storage directory
// Check against both possible paths (symlink and real)
$allowedPaths = [
    realpath($projectRoot . '/storage/app/public'),
    realpath($projectRoot . '/storage/app/private/public'),
];

$isAllowed = false;
foreach ($allowedPaths as $allowedPath) {
    if ($allowedPath && $realFilePath && strpos($realFilePath, $allowedPath) === 0) {
        $isAllowed = true;
        break;
    }
}

if (!$isAllowed || !$realFilePath) {
    http_response_code(404);
    die('File not found');
}

// Check if file exists
if (!file_exists($realFilePath) || !is_file($realFilePath)) {
    http_response_code(404);
    die('File not found: ' . $path);
}

// Get file info
$fileSize = filesize($realFilePath);
$mimeType = mime_content_type($realFilePath);

// Fallback for MIME type
if (!$mimeType) {
    $extension = strtolower(pathinfo($realFilePath, PATHINFO_EXTENSION));
    $mimeTypes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'webp' => 'image/webp',
        'pdf' => 'application/pdf',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
    ];
    $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
}

// Set headers
header('Content-Type: ' . $mimeType);
header('Content-Length: ' . $fileSize);
header('Content-Disposition: inline; filename="' . basename($path) . '"');
header('Cache-Control: public, max-age=31536000');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');

// Output file
readfile($realFilePath);
exit;

