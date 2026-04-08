<?php

namespace App\Helpers;

class PathHelper
{
    /**
     * Detect if we're in a shared hosting environment (public_html) or standard Laravel (public)
     * 
     * @return string 'public_html' or 'public'
     */
    public static function detectPublicPath(): string
    {
        // Get the current script directory
        $scriptDir = dirname($_SERVER['SCRIPT_FILENAME']);
        $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
        
        // Check if we're in public_html (shared hosting like Hostinger)
        if (strpos($scriptDir, 'public_html') !== false || 
            strpos($documentRoot, 'public_html') !== false ||
            basename($documentRoot) === 'public_html') {
            return 'public_html';
        }
        
        // Check if public directory exists in parent
        $parentDir = dirname($scriptDir);
        if (file_exists($parentDir . '/public') && is_dir($parentDir . '/public')) {
            return 'public';
        }
        
        // Default to public_html for shared hosting compatibility
        return 'public_html';
    }
    
    /**
     * Get the public path based on environment
     * 
     * @return string
     */
    public static function getPublicPath(): string
    {
        $publicType = self::detectPublicPath();
        
        if ($publicType === 'public_html') {
            // In shared hosting, public_html IS the public directory
            return base_path();
        }
        
        // Standard Laravel structure
        return base_path('public');
    }
    
    /**
     * Get storage URL path
     * 
     * @return string
     */
    public static function getStorageUrl(): string
    {
        $publicType = self::detectPublicPath();
        
        if ($publicType === 'public_html') {
            // In shared hosting, storage is directly accessible
            return '/storage';
        }
        
        // Standard Laravel structure
        return '/storage';
    }
    
    /**
     * Check if we're in shared hosting environment
     * 
     * @return bool
     */
    public static function isSharedHosting(): bool
    {
        return self::detectPublicPath() === 'public_html';
    }
}

