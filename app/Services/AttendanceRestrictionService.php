<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Http\Request;

/**
 * التحقق من إعدادات الحضور والانصراف:
 * - anywhere: من أي مكان واتصال
 * - ip_restricted: فقط من عناوين IP مسموحة
 * - location_restricted: فقط من داخل دائرة جغرافية (نقطة + نصف قطر بالمتر)
 */
class AttendanceRestrictionService
{
    public const MODE_ANYWHERE = 'anywhere';
    public const MODE_IP_RESTRICTED = 'ip_restricted';
    public const MODE_LOCATION_RESTRICTED = 'location_restricted';

    public static function getMode(): string
    {
        $mode = SiteSetting::getValue('attendance_mode', self::MODE_ANYWHERE);
        return in_array($mode, [self::MODE_ANYWHERE, self::MODE_IP_RESTRICTED, self::MODE_LOCATION_RESTRICTED], true)
            ? $mode
            : self::MODE_ANYWHERE;
    }

    /** @return list<string> */
    public static function getAllowedIps(): array
    {
        $raw = SiteSetting::getValue('attendance_allowed_ips', '');
        if ($raw === '') {
            return [];
        }
        $ips = preg_split('/[\s,،;\n]+/', $raw, -1, PREG_SPLIT_NO_EMPTY);
        return array_values(array_map('trim', array_filter($ips)));
    }

    public static function getLocationLat(): ?float
    {
        $v = SiteSetting::getValue('attendance_location_lat', '');
        return $v !== '' && is_numeric($v) ? (float) $v : null;
    }

    public static function getLocationLng(): ?float
    {
        $v = SiteSetting::getValue('attendance_location_lng', '');
        return $v !== '' && is_numeric($v) ? (float) $v : null;
    }

    /** نصف القطر بالمتر */
    public static function getLocationRadiusMeters(): float
    {
        $v = SiteSetting::getValue('attendance_location_radius_meters', '100');
        return $v !== '' && is_numeric($v) && (float) $v > 0 ? (float) $v : 100.0;
    }

    /**
     * التحقق من السماح بتسجيل الحضور/الانصراف حسب الإعدادات.
     * في وضع الموقع يُتوقع أن الطلب يحتوي على latitude و longitude.
     */
    public static function isAllowed(Request $request, ?float $lat = null, ?float $lng = null): bool
    {
        $mode = self::getMode();

        if ($mode === self::MODE_ANYWHERE) {
            return true;
        }

        if ($mode === self::MODE_IP_RESTRICTED) {
            $allowed = self::getAllowedIps();
            if (empty($allowed)) {
                return false; // لا عناوين مسموحة = لا أحد مسموح
            }
            $clientIp = client_ip($request);
            return in_array($clientIp, $allowed, true);
        }

        if ($mode === self::MODE_LOCATION_RESTRICTED) {
            $centerLat = self::getLocationLat();
            $centerLng = self::getLocationLng();
            $radiusMeters = self::getLocationRadiusMeters();
            if ($centerLat === null || $centerLng === null) {
                return false;
            }
            $useLat = $lat ?? ($request->has('latitude') && is_numeric($request->input('latitude')) ? (float) $request->input('latitude') : null);
            $useLng = $lng ?? ($request->has('longitude') && is_numeric($request->input('longitude')) ? (float) $request->input('longitude') : null);
            if ($useLat === null || $useLng === null) {
                return false;
            }
            $distanceMeters = self::haversineDistanceMeters($centerLat, $centerLng, $useLat, $useLng);
            return $distanceMeters <= $radiusMeters;
        }

        return false;
    }

    /** مسافة تقريبية بالمتر بين نقطتين (صيغة Haversine) */
    public static function haversineDistanceMeters(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // متر
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    /** رسالة خطأ عند عدم السماح */
    public static function getErrorMessage(): string
    {
        $mode = self::getMode();
        if ($mode === self::MODE_IP_RESTRICTED) {
            return 'تسجيل الحضور والانصراف مسموح فقط من شبكة/اتصال معتمد. عنوانك الحالي غير مسموح.';
        }
        if ($mode === self::MODE_LOCATION_RESTRICTED) {
            return 'يُسمح بتسجيل الحضور والانصراف فقط من الموقع المعتمد. تأكد من تفعيل الموقع أو أنك داخل نطاق المقر.';
        }
        return 'غير مسموح بتسجيل الحضور والانصراف في هذه اللحظة.';
    }

    /** رسالة توضيحية للوضع الحالي (للعرض في الواجهة) */
    public static function getInfoMessage(): ?string
    {
        $mode = self::getMode();
        if ($mode === self::MODE_ANYWHERE) {
            return null;
        }
        if ($mode === self::MODE_IP_RESTRICTED) {
            return 'يُسمح بالتسجيل فقط من شبكة/اتصال معتمد (عنوان IP محدد).';
        }
        if ($mode === self::MODE_LOCATION_RESTRICTED) {
            return 'يُسمح بالتسجيل فقط من الموقع المعتمد (داخل نطاق المقر). سيتم طلب إذن الموقع عند التسجيل.';
        }
        return null;
    }
}
