<?php

use App\Helpers\PathHelper;
use Illuminate\Support\Facades\Storage;

if (!function_exists('public_path_auto')) {
    /**
     * Get public path automatically based on hosting environment
     * 
     * @param string $path
     * @return string
     */
    function public_path_auto($path = ''): string
    {
        $publicPath = PathHelper::getPublicPath();
        return $publicPath . ($path ? '/' . ltrim($path, '/') : '');
    }
}

if (!function_exists('client_ip')) {
    /**
     * Get the real client IP address (works behind proxies/load balancers)
     * يرجّع عنوان IP العميل الحقيقي - يفحص X-Forwarded-For و X-Real-IP و CF-Connecting-IP
     *
     * @param \Illuminate\Http\Request|null $request
     * @return string
     */
    function client_ip(?\Illuminate\Http\Request $request = null): string
    {
        $req = $request ?? request();
        foreach (['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CF_CONNECTING_IP'] as $key) {
            $val = $req->server($key);
            if (empty($val)) {
                continue;
            }
            $first = trim((string) (explode(',', $val)[0] ?? ''));
            if ($first !== '' && filter_var($first, FILTER_VALIDATE_IP)) {
                return $first;
            }
        }
        return (string) ($req->ip() ?? $req->server('REMOTE_ADDR') ?? '');
    }
}

if (!function_exists('website_page_url')) {
    /**
     * رابط صفحة إعدادات الموقع: من وصال يبقى ضمن مسار وصال، ومن لوحة التحكم يبقى ضمن لوحة التحكم
     * عند فتح الصفحة من نظام وصال يعيد روابط wesal.page حتى لا ينتقل المستخدم إلى لوحة التحكم
     *
     * @param string $page اسم الصفحة (settings, about, services, ...)
     * @param array<string, mixed> $query معاملات إضافية (edit, edit_category, edit_policy, ...)
     * @return string
     */
    function website_page_url(string $page, array $query = []): string
    {
        if (request()->routeIs('wesal.*')) {
            $url = route('wesal.page', $page);
            return $query ? $url . '?' . http_build_query($query) : $url;
        }
        return route('dashboard', array_merge(['page' => $page], $query));
    }
}

if (!function_exists('settings_update_url')) {
    /**
     * رابط حفظ إعدادات الموقع: من وصال يرسل إلى wesal.settings.update، ومن لوحة التحكم إلى dashboard.settings.update
     * حتى لا يحوّل المستخدم إلى لوحة التحكم المستقلة عند الحفظ من وصال.
     *
     * @return string
     */
    function settings_update_url(): string
    {
        return request()->routeIs('wesal.*') ? route('wesal.settings.update') : route('dashboard.settings.update');
    }
}

if (!function_exists('settings_route')) {
    /**
     * رابط مسار إعدادات (إعادة ضبط، توليد ألوان، إلخ): من وصال يبقى ضمن wesal، ومن لوحة التحكم ضمن dashboard.
     *
     * @param string $name اسم المسار بدون البادئة (مثل reset-colors, reset-hero-background, generate-section-colors)
     * @param array<string, mixed> $params معاملات إضافية للمسار (مثل ['section' => $key])
     * @return string
     */
    function settings_route(string $name, array $params = []): string
    {
        $wesalName = 'wesal.settings.' . $name;
        $dashboardName = 'dashboard.settings.' . $name;
        if (request()->routeIs('wesal.*')) {
            return route($wesalName, $params);
        }
        return route($dashboardName, $params);
    }
}

if (!function_exists('youtube_video_id')) {
    /**
     * استخراج معرف فيديو يوتيوب من الرابط (youtube.com/watch?v=ID, youtu.be/ID, youtube.com/embed/ID)
     */
    function youtube_video_id(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $m)) {
            return $m[1];
        }
        if (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $url, $m)) {
            return $m[1];
        }
        if (preg_match('/youtube\.com\/watch\?.*v=([a-zA-Z0-9_-]+)/', $url, $m)) {
            return $m[1];
        }
        return null;
    }
}

if (!function_exists('is_shared_hosting')) {
    /**
     * Check if we're in shared hosting environment
     * 
     * @return bool
     */
    function is_shared_hosting(): bool
    {
        return PathHelper::isSharedHosting();
    }
}

if (!function_exists('storage_url_auto')) {
    /**
     * Get storage URL automatically based on hosting environment
     * 
     * @param string $path
     * @return string
     */
    function storage_url_auto($path = ''): string
    {
        $baseUrl = PathHelper::getStorageUrl();
        return $baseUrl . ($path ? '/' . ltrim($path, '/') : '');
    }
}

if (!function_exists('image_storage_path')) {
    /**
     * Get absolute filesystem path for Intervention Image or file operations
     * Converts relative path (e.g., 'uploads/filename.jpg') to absolute path
     * 
     * @param string|null $path Relative path stored in database (e.g., 'uploads/filename.jpg')
     * @return string Absolute filesystem path for Intervention Image
     * @throws \InvalidArgumentException if path is empty or invalid
     */
    function image_storage_path($path): string
    {
        // Throw exception if path is empty or null
        if (empty($path)) {
            throw new \InvalidArgumentException('Image path cannot be empty');
        }
        
        // If it's already an absolute path, return as-is (but validate it's in storage/app/public)
        if (strpos($path, storage_path('app/public')) === 0) {
            return $path;
        }
        
        // Remove 'storage/' prefix if present (backward compatibility)
        $path = str_replace('storage/', '', $path);
        $path = ltrim($path, '/');
        
        // Return absolute path
        $fullPath = storage_path('app/public/' . $path);
        
        // Security: Verify the path is within storage/app/public (prevent directory traversal)
        $realPath = realpath($fullPath);
        $realStoragePath = realpath(storage_path('app/public'));
        
        if ($realPath && $realStoragePath && strpos($realPath, $realStoragePath) === 0) {
            return $realPath;
        }
        
        // If realpath fails, return the constructed path (file might not exist yet)
        return $fullPath;
    }
}

if (!function_exists('image_asset_url')) {
    /**
     * Get public URL for files under storage/app/public (images, PDFs, beneficiary_uploads/, etc.)
     * Converts relative path (e.g., 'uploads/filename.jpg') to asset URL
     * Handles various formats: 'uploads/file.jpg', 'storage/uploads/file.jpg', full URLs
     * 
     * @param string|null $path Relative path stored in database (e.g., 'uploads/filename.jpg')
     * @return string Asset URL for displaying in views, or empty string if path is invalid
     */
    function image_asset_url($path): string
    {
        // Return empty string if path is empty or null
        if (empty($path)) {
            return '';
        }
        
        // If it's already a full URL (http/https), return as-is
        if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
            return $path;
        }
        
        // Reject temp paths (these should never be saved, but check anyway)
        if (strpos($path, '/private/') !== false || 
            strpos($path, '/tmp/') !== false || 
            strpos($path, '/var/folders/') !== false ||
            strpos($path, 'php') === 0) {
            \Log::warning('Invalid temp path detected in image_asset_url: ' . $path);
            return ''; // Return empty string for invalid paths
        }
        
        // Remove 'storage/' prefix if present (backward compatibility)
        $path = str_replace('storage/', '', $path);
        $path = ltrim($path, '/');
        
        // Return empty string if path is still empty after cleaning
        if (empty($path)) {
            return '';
        }
        
        // Return asset URL
        return asset('storage/' . $path);
    }
}

if (!function_exists('format_beneficiary_choice_display')) {
    /**
     * عرض قيم «خيار واحد / خيار متعدد» بدون أقواس [] أو تنسيق JSON.
     * عنصر واحد: النص كما هو. أكثر من عنصر: «خيار1»، «خيار2» (فاصلة عربية).
     * يدعم القيم المخزّنة كسلسلة JSON مثل ["أ"].
     */
    function format_beneficiary_choice_display(mixed $raw): string
    {
        if ($raw === null || $raw === '') {
            return '';
        }
        if (is_int($raw) || is_float($raw) || is_bool($raw)) {
            return (string) $raw;
        }
        if (is_string($raw)) {
            $t = trim($raw);
            if ($t === '') {
                return '';
            }
            if (str_starts_with($t, '[')) {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    return format_beneficiary_choice_display($decoded);
                }
            }

            return $raw;
        }
        if (! is_array($raw)) {
            return '';
        }
        $values = [];
        foreach ($raw as $v) {
            if (is_array($v)) {
                $nested = format_beneficiary_choice_display($v);
                if ($nested !== '') {
                    $values[] = $nested;
                }

                continue;
            }
            if ($v === null || $v === '') {
                continue;
            }
            $s = trim((string) $v);
            if ($s !== '') {
                $values[] = $s;
            }
        }
        if (count($values) === 0) {
            return '';
        }
        if (count($values) === 1) {
            return $values[0];
        }

        return implode('، ', array_map(static fn (string $v) => '«'.$v.'»', $values));
    }
}

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
            $imageName = $prefix . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Use Laravel Storage ONLY - this ensures files are saved in the correct location
            // according to config/filesystems.php settings
            $imagePath = Storage::disk('public')->putFileAs('uploads', $file, $imageName);
            
            if (!$imagePath) {
                \Log::error('Image upload failed - Storage::disk(\'public\')->putFileAs() returned false');
                return [
                    'success' => false,
                    'path' => null,
                    'message' => 'فشل رفع الصورة. حاول مرة أخرى.'
                ];
            }
            
            // Normalize Storage path - remove 'storage/' prefix if present
            // Storage::putFileAs() returns path like 'uploads/filename.jpg' or 'storage/uploads/filename.jpg'
            $imagePath = str_replace('\\', '/', $imagePath);
            $imagePath = ltrim($imagePath, '/');
            $imagePath = preg_replace('#^storage/#', '', $imagePath);
            
            // Ensure path starts with 'uploads/'
            if (strpos($imagePath, 'uploads/') !== 0) {
                $imagePath = 'uploads/' . ltrim($imagePath, '/');
            }
            
            // Verify file actually exists
            $fullPath = storage_path('app/public/' . $imagePath);
            if (!file_exists($fullPath)) {
                \Log::error('Image upload failed - file not found: ' . $fullPath . ' (stored path: ' . $imagePath . ')');
                return [
                    'success' => false,
                    'path' => null,
                    'message' => 'فشل رفع الصورة. الملف غير موجود بعد الرفع.'
                ];
            }
            
            // Final validation: reject temp paths
            if (strpos($imagePath, '/private/') !== false || 
                strpos($imagePath, '/tmp/') !== false || 
                strpos($imagePath, '/var/folders/') !== false ||
                strpos($imagePath, 'php') === 0) {
                \Log::error('Invalid temp path detected in upload result: ' . $imagePath);
                @unlink($fullPath); // Delete the file if it was created
                return [
                    'success' => false,
                    'path' => null,
                    'message' => 'مسار غير صالح. حاول مرة أخرى.'
                ];
            }
            
            // Log successful upload for debugging
            \Log::info('Image uploaded successfully: ' . $imagePath . ' -> ' . $fullPath);
            
            // Return relative path (e.g., 'uploads/filename.jpg')
            return [
                'success' => true,
                'path' => $imagePath,
                'message' => 'تم الرفع بنجاح.'
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

if (!function_exists('upload_file_safely')) {
    /**
     * Upload file safely with validation (for PDF, DOC, etc.)
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $prefix Prefix for filename (e.g., 'file', 'document')
     * @return array ['success' => bool, 'path' => string|null, 'message' => string]
     */
    function upload_file_safely($file, string $prefix = 'file'): array
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
            $fileName = $prefix . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Use Laravel Storage ONLY
            $filePath = Storage::disk('public')->putFileAs('uploads', $file, $fileName);
            
            if (!$filePath) {
                \Log::error('File upload failed - Storage::disk(\'public\')->putFileAs() returned false');
                return [
                    'success' => false,
                    'path' => null,
                    'message' => 'فشل رفع الملف. حاول مرة أخرى.'
                ];
            }
            
            // Normalize Storage path
            $filePath = str_replace('\\', '/', $filePath);
            $filePath = ltrim($filePath, '/');
            $filePath = preg_replace('#^storage/#', '', $filePath);
            
            // Ensure path starts with 'uploads/'
            if (strpos($filePath, 'uploads/') !== 0) {
                $filePath = 'uploads/' . ltrim($filePath, '/');
            }
            
            // Verify file actually exists
            $fullPath = storage_path('app/public/' . $filePath);
            if (!file_exists($fullPath)) {
                \Log::error('File upload failed - file not found: ' . $fullPath . ' (stored path: ' . $filePath . ')');
                return [
                    'success' => false,
                    'path' => null,
                    'message' => 'فشل رفع الملف. الملف غير موجود بعد الرفع.'
                ];
            }
            
            // Final validation: reject temp paths
            if (strpos($filePath, '/private/') !== false || 
                strpos($filePath, '/tmp/') !== false || 
                strpos($filePath, '/var/folders/') !== false ||
                strpos($filePath, 'php') === 0) {
                \Log::error('Invalid temp path detected in upload result: ' . $filePath);
                @unlink($fullPath);
                return [
                    'success' => false,
                    'path' => null,
                    'message' => 'مسار غير صالح. حاول مرة أخرى.'
                ];
            }
            
            \Log::info('File uploaded successfully: ' . $filePath . ' -> ' . $fullPath);
            
            return [
                'success' => true,
                'path' => $filePath,
                'message' => 'تم الرفع بنجاح.'
            ];
            
        } catch (\Exception $e) {
            \Log::error('File upload error: ' . $e->getMessage());
            return [
                'success' => false,
                'path' => null,
                'message' => 'حدث خطأ أثناء رفع الملف: ' . $e->getMessage()
            ];
        }
    }
}

if (!function_exists('news_excerpt')) {
    /**
     * استخراج ملخص نصي من محتوى الخبر (بدون وسوم HTML أو أكواد)
     *
     * @param string|null $content
     * @param int $length
     * @return string
     */
    function news_excerpt(?string $content, int $length = 150): string
    {
        if (empty($content)) {
            return '';
        }
        $text = strip_tags($content);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', trim($text));
        $text = mb_substr($text, 0, $length);
        return $text . (mb_strlen($text) >= $length ? '...' : '');
    }
}

if (!function_exists('normalize_css_hex')) {
    /**
     * تطبيع لون هيكس للعرض في CSS (#RRGGBB) — معالجة قيم RTL مثل FFFFFF#
     */
    function normalize_css_hex(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }
        $value = trim($value);
        $hex = preg_replace('/#/u', '', $value);
        if (preg_match('/^[0-9A-Fa-f]{6}$/', $hex)) {
            return '#' . $hex;
        }
        return $value;
    }
}

if (!function_exists('hijri_calendar_current_year')) {
    /**
     * السنة الهجرية الحالية (تُحدَّد من خادم التطبيق — تزيد تلقائياً مع مرور السنوات).
     * يُفضَّل ext-intl (تقويم islamic-civil)؛ وإلا تُستخدم معادلة تقريبية من التاريخ الميلادي.
     */
    function hijri_calendar_current_year(): int
    {
        if (class_exists(\IntlDateFormatter::class)) {
            try {
                $fmt = new \IntlDateFormatter(
                    'en_US@calendar=islamic-civil',
                    \IntlDateFormatter::NONE,
                    \IntlDateFormatter::NONE,
                    'Asia/Riyadh',
                    \IntlDateFormatter::TRADITIONAL,
                    'y'
                );
                if ($fmt !== false) {
                    $y = (int) $fmt->format(time());
                    if ($y >= 1350 && $y <= 1600) {
                        return $y;
                    }
                }
            } catch (\Throwable $e) {
                // تجاهل
            }
        }

        if (! function_exists('gregoriantojd')) {
            return max(1350, (int) date('Y') - 621);
        }

        $tz = new DateTimeZone('Asia/Riyadh');
        $now = new DateTimeImmutable('now', $tz);
        $gy = (int) $now->format('Y');
        $gm = (int) $now->format('n');
        $gd = (int) $now->format('j');
        $jd = gregoriantojd($gm, $gd, $gy);
        $l = (int) floor(($jd - 1948440 + 10632) / 10631);
        $n = $jd - 1948440 + 10632 - (int) floor((10631 * $l + 8) / 10631);
        $j = (int) floor((100 * $n + 99) / 30601);
        $hy = $j + 33 * $l + 622;

        return max(1350, min($hy, 1600));
    }
}