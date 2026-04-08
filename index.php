<?php

/**
 * Auto-detection entry point for Laravel
 * 
 * This file automatically detects the hosting environment:
 * - Shared hosting (public_html): Direct execution
 * - Standard Laravel (public): Redirect to public/index.php
 */

// Detect hosting environment
$scriptDir = dirname(__FILE__);
$documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';

// Check if we're in shared hosting (public_html)
$isSharedHosting = (
    strpos($scriptDir, 'public_html') !== false || 
    strpos($documentRoot, 'public_html') !== false ||
    basename($documentRoot) === 'public_html' ||
    !file_exists($scriptDir . '/public')
);

if ($isSharedHosting) {
    // Shared hosting: This file IS the entry point
    // Adjust paths to point to Laravel root
    $laravelRoot = dirname(__FILE__);
    
    // Define Laravel paths
    define('LARAVEL_START', microtime(true));
    
    // Determine if the application is in maintenance mode...
    if (file_exists($maintenance = $laravelRoot.'/storage/framework/maintenance.php')) {
        require $maintenance;
    }
    
    // Register the Composer autoloader...
    require $laravelRoot.'/vendor/autoload.php';
    
    // Bootstrap Laravel and handle the request...
    /** @var \Illuminate\Foundation\Application $app */
    $app = require_once $laravelRoot.'/bootstrap/app.php';
    
    $app->handleRequest(\Illuminate\Http\Request::capture());
} else {
    // Standard Laravel: Redirect to public/index.php
    // This should not happen in normal Laravel setup, but included for safety
    if (file_exists($scriptDir . '/public/index.php')) {
        require $scriptDir . '/public/index.php';
    } else {
        die('Laravel public directory not found. Please check your installation.');
    }
}

