<?php
/**
 * Direct Storage File Server
 * 
 * This file should be placed in: public_html/storage/index.php
 * It serves files directly from storage/app/public without Laravel routing
 * 
 * This is a fallback solution for shared hosting where Laravel routes don't work properly
 */

// Get the requested file path from REQUEST_URI
$requestUri = $_SERVER['REQUEST_URI'] ?? '';

// Extract path from URI
$parsedUrl = parse_url($requestUri);
$path = $parsedUrl['path'] ?? '';

// Remove /storage prefix if present
$path = preg_replace('#^/storage/#', '', $path);
$path = preg_replace('#^storage/#', '', $path);
$path = ltrim($path, '/');

// If path is empty or just 'index.php', try to get from query string
if (empty($path) || $path === 'index.php') {
    $path = $_GET['file'] ?? $_GET['path'] ?? '';
    $path = ltrim($path, '/');
}

// Security: prevent directory traversal
if (empty($path) || strpos($path, '..') !== false || strpos($path, './') !== false || strpos($path, '/') === 0) {
    http_response_code(404);
    die('File not found');
}

// Get base path (this file should be in public_html/storage/)
$currentFile = __FILE__;
$basePath = dirname($currentFile); // This is public_html/storage
$rootPath = dirname($basePath); // This is public_html
$storagePath = $rootPath . '/storage/app/public';

// Build full file path
$filePath = $storagePath . '/' . $path;

// Normalize paths for security
$realFilePath = realpath($filePath);
$realStoragePath = realpath($storagePath);

// Security check: ensure file is within storage/app/public
if (!$realFilePath || !$realStoragePath) {
    http_response_code(404);
    die('File not found');
}

if (strpos($realFilePath, $realStoragePath) !== 0) {
    http_response_code(404);
    die('File not found');
}

// Check if file exists
if (!file_exists($realFilePath) || !is_file($realFilePath)) {
    http_response_code(404);
    die('File not found');
}

// Get file info
$fileSize = filesize($realFilePath);
$mimeType = mime_content_type($realFilePath);

// Fallback for common image types
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

