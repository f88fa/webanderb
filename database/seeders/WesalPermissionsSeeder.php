<?php

namespace Database\Seeders;

use App\Services\PermissionsRegistry;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class WesalPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        PermissionsRegistry::sync();
        $guard = config('wesal_permissions.guard', 'web');

        // دور SuperAdmin — يرى كل شيء
        $superAdmin = Role::firstOrCreate(['name' => 'SuperAdmin', 'guard_name' => $guard]);
        foreach (Permission::where('guard_name', $guard)->get() as $perm) {
            $superAdmin->givePermissionTo($perm);
        }

        // دور Admin — يرى كل أقسام Wesal ما عدا إدارة المستخدمين والصلاحيات
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => $guard]);
        $allPermNames = PermissionsRegistry::getAllPermissionNames();
        $adminPermNames = array_filter($allPermNames, fn ($p) => $p !== 'wesal.users');
        foreach ($adminPermNames as $name) {
            $admin->givePermissionTo($name);
        }

        // تعيين SuperAdmin للمستخدم الافتراضي
        $adminUser = \App\Models\User::where('email', 'admin@example.com')->first();
        if ($adminUser && !$adminUser->hasRole('SuperAdmin')) {
            $adminUser->assignRole('SuperAdmin');
        }

        $this->command->info('تم إنشاء صلاحيات Wesal بنجاح.');
    }
}
