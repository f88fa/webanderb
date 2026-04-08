<?php
/**
 * Helper Functions for Safe Image Upload in Hostinger
 * 
 * أضف هذه الدوال إلى: app/Helpers/helpers.php
 * أو أنشئ ملف جديد: app/Helpers/UploadHelper.php
 */

if (!function_exists('ensure_uploads_directory')) {
    /**
     * Ensure uploads directory exists and is writable
     * 
     * @return array ['success' => bool, 'message' => string]
     */
    function ensure_uploads_directory(): array
    {
        $uploadsPath = storage_path('app/public/uploads');
        
        // Create directory if not exists
        if (!is_dir($uploadsPath)) {
            if (!mkdir($uploadsPath, 0755, true)) {
                return [
                    'success' => false,
                    'message' => 'فشل إنشاء مجلد الرفع. تحقق من الصلاحيات.'
                ];
            }
        }
        
        // Check if writable
        if (!is_writable($uploadsPath)) {
            return [
                'success' => false,
                'message' => 'مجلد الرفع غير قابل للكتابة. تحقق من الصلاحيات.'
            ];
        }
        
        return ['success' => true, 'message' => ''];
    }
}

if (!function_exists('upload_image_safely')) {
    /**
     * Upload image file safely with validation
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $prefix Prefix for filename (e.g., 'about', 'news')
     * @return array ['success' => bool, 'path' => string|null, 'message' => string]
     */
    function upload_image_safely($file, string $prefix = 'image'): array
    {
        try {
            // Ensure uploads directory exists
            $dirCheck = ensure_uploads_directory();
            if (!$dirCheck['success']) {
                return [
                    'success' => false,
                    'path' => null,
                    'message' => $dirCheck['message']
                ];
            }
            
            // Generate filename
            $imageName = $prefix . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Upload file using Storage facade
            $imagePath = \Illuminate\Support\Facades\Storage::disk('public')->putFileAs('uploads', $file, $imageName);
            
            // Verify upload succeeded
            if (!$imagePath) {
                return [
                    'success' => false,
                    'path' => null,
                    'message' => 'فشل رفع الصورة. حاول مرة أخرى.'
                ];
            }
            
            // Get the actual storage path from config
            $storageRoot = config('filesystems.disks.public.root');
            $fullPath = $storageRoot . '/' . $imagePath;
            
            // Verify file actually exists
            if (!file_exists($fullPath)) {
                \Log::error('Image upload failed - file not found: ' . $fullPath);
                \Log::error('Storage root: ' . $storageRoot);
                \Log::error('Image path: ' . $imagePath);
                return [
                    'success' => false,
                    'path' => null,
                    'message' => 'فشل رفع الصورة. الملف غير موجود بعد الرفع.'
                ];
            }
            
            // Return relative path
            return [
                'success' => true,
                'path' => 'uploads/' . $imageName,
                'message' => ''
            ];
            
        } catch (\Exception $e) {
            \Log::error('Image upload error: ' . $e->getMessage());
            return [
                'success' => false,
                'path' => null,
                'message' => 'حدث خطأ أثناء رفع الصورة: ' . $e->getMessage()
            ];
        }
    }
}

