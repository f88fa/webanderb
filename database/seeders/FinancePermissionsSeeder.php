<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class FinancePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء الصلاحيات
        $permissions = [
            'finance.view',
            'finance.accountant',
            'finance.admin',
            'finance.super_admin',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // إنشاء الأدوار
        $viewerRole = Role::firstOrCreate(['name' => 'FinanceViewer', 'guard_name' => 'web']);
        $accountantRole = Role::firstOrCreate(['name' => 'FinanceAccountant', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'FinanceAdmin', 'guard_name' => 'web']);
        $superAdminRole = Role::firstOrCreate(['name' => 'FinanceSuperAdmin', 'guard_name' => 'web']);

        // تعيين الصلاحيات للأدوار
        $viewerRole->givePermissionTo('finance.view');
        $accountantRole->givePermissionTo(['finance.view', 'finance.accountant']);
        $adminRole->givePermissionTo(['finance.view', 'finance.accountant', 'finance.admin']);
        $superAdminRole->givePermissionTo(['finance.view', 'finance.accountant', 'finance.admin', 'finance.super_admin']);

        $this->command->info('تم إنشاء صلاحيات المالية بنجاح.');
    }
}
