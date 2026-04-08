<?php

namespace App\Http\Middleware;

use App\Services\PermissionsRegistry;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWesalPermission
{
    /**
     * خريطة المسارات → صلاحية مطلوبة
     */
    protected static array $routePermissionMap = [
        'wesal' => 'wesal.home',
        'wesal.e-office' => 'wesal.e-office',
        'wesal.finance' => 'wesal.finance',
        'wesal.hr' => 'wesal.hr',
        'wesal.beneficiaries' => 'wesal.beneficiaries',
        'wesal.programs-projects' => 'wesal.programs-projects',
        'wesal.meetings' => 'wesal.meetings',
        'wesal.communications' => 'wesal.communications',
        'wesal.requests' => 'wesal.administrative-requests',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->hasRole('SuperAdmin')) {
            return $next($request);
        }

        $permission = $this->resolvePermission($request);
        // الجهة المانحة: إذن خاص لعرض مشاريعها فقط
        if ($permission === 'wesal.programs-projects' && $user->can('donor.view_projects') && $user->donor()->exists()) {
            return $next($request);
        }
        if ($permission && !$user->can($permission)) {
            $permissionLabel = PermissionsRegistry::getPermissionLabelAr($permission);
            return response()->view('wesal.errors.forbidden', [
                'permission' => $permission,
                'permission_label' => $permissionLabel,
            ], 403);
        }

        return $next($request);
    }

    protected function resolvePermission(Request $request): ?string
    {
        $routeName = $request->route()?->getName() ?? '';
        $page = $request->route('page');

        // مسار الصفحة الرئيسية: /wesal/{page} — نحدد الصلاحية حسب قيمة page
        $pageToPermission = [
            'home' => 'wesal.home',
            'e-office' => 'wesal.e-office',
            'finance' => 'wesal.finance',
            'hr' => 'wesal.hr',
            'beneficiaries' => 'wesal.beneficiaries',
            'programs-projects' => 'wesal.programs-projects',
            'meetings' => 'wesal.meetings',
            'communications' => 'wesal.communications',
        ];
        if ($page && isset($pageToPermission[$page])) {
            return $pageToPermission[$page];
        }

        // صفحة مستخدمين النظام والأدوار والصلاحيات
        if (in_array($page, ['users', 'roles-permissions'])) {
            return 'wesal.users';
        }
        // إعدادات النظام + معاينة ورقة الخطاب
        if ($page === 'system-settings') {
            return 'wesal.system-settings';
        }
        if ($routeName === 'wesal.letter-paper-preview' || $routeName === 'wesal.system-settings.update') {
            return 'wesal.system-settings';
        }
        // صفحات الموقع الإلكتروني
        $websitePages = ['settings', 'about', 'vision-mission', 'services', 'partners', 'media', 'banner-sections', 'section-order', 'section_order', 'menu', 'board-members', 'executive-director', 'staff', 'files', 'reports', 'policies', 'projects', 'testimonials', 'news'];
        if (in_array($page, $websitePages)) {
            return 'wesal.website';
        }

        // مسارات فرعية (مثل wesal.hr.leave.approvals، wesal.finance.periods)
        foreach (self::$routePermissionMap as $prefix => $perm) {
            if ($prefix === 'wesal') {
                continue; // لا نطابق البادئة العامة هنا؛ الصفحات الرئيسية عالجناها أعلاه
            }
            if ($routeName === $prefix || str_starts_with($routeName, $prefix . '.')) {
                return $perm;
            }
        }

        // الرئيسية، المكتب الإلكتروني، الاجتماعات، الطلبات الإدارية — متاحة لأي مستخدم مسجل
        return 'wesal.home';
    }
}
