<?php

namespace App\Services;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * السجل المركزي للصلاحيات — يقرأ من config ويُحدّث قاعدة البيانات تلقائياً
 */
class PermissionsRegistry
{
    public static function sync(): void
    {
        $config = config('wesal_permissions', []);
        $guard = $config['guard'] ?? 'web';
        $groups = $config['groups'] ?? [];

        foreach ($groups as $group) {
            foreach ($group['permissions'] ?? [] as $name => $label) {
                Permission::firstOrCreate(
                    ['name' => $name, 'guard_name' => $guard],
                    ['name' => $name, 'guard_name' => $guard]
                );
            }
        }

        // منح دور SuperAdmin كل الصلاحيات تلقائياً (بما فيها إعدادات النظام وألوان لوحة التحكم)
        $superAdmin = Role::where('name', 'SuperAdmin')->where('guard_name', $guard)->first();
        if ($superAdmin) {
            $allNames = self::getAllPermissionNames();
            $superAdmin->syncPermissions(Permission::where('guard_name', $guard)->whereIn('name', $allNames)->get());
        }
    }

    /** مجموعة الصلاحيات حسب الأقسام (من config) */
    public static function getGroupedPermissions(): array
    {
        $config = config('wesal_permissions', []);
        return $config['groups'] ?? [];
    }

    /** جميع أسماء الصلاحيات من config */
    public static function getAllPermissionNames(): array
    {
        $names = [];
        foreach (self::getGroupedPermissions() as $group) {
            foreach (array_keys($group['permissions'] ?? []) as $name) {
                $names[] = $name;
            }
        }
        return $names;
    }

    public static function isRoleProtected(string $roleName): bool
    {
        $config = config('wesal_permissions', []);
        $protected = $config['protected_roles'] ?? ['SuperAdmin'];
        return in_array($roleName, $protected, true);
    }

    /** تسمية الدور بالعربي للعرض في الأعمدة */
    public static function getRoleLabelAr($role): string
    {
        $name = is_object($role) ? $role->name : $role;
        if (is_object($role) && !empty($role->label_ar ?? null)) {
            return $role->label_ar;
        }
        $config = config('wesal_permissions', []);
        $map = $config['roles_label_ar'] ?? [];
        return $map[$name] ?? $name;
    }

    /** تسمية الصلاحية بالعربي (للعرض في صفحة عدم الصلاحية) */
    public static function getPermissionLabelAr(string $permissionName): string
    {
        foreach (self::getGroupedPermissions() as $group) {
            $perms = $group['permissions'] ?? [];
            if (isset($perms[$permissionName])) {
                return $perms[$permissionName];
            }
        }
        return $permissionName;
    }
}
