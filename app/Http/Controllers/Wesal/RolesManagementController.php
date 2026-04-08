<?php

namespace App\Http\Controllers\Wesal;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\PermissionsRegistry;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class RolesManagementController extends Controller
{
    public function store(Request $request)
    {
        if (!auth()->user()->can('roles.create')) {
            abort(403, 'ليس لديك صلاحية إنشاء أدوار.');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
            'name_ar' => 'nullable|string|max:100',
        ], [
            'name.required' => 'اسم الدور مطلوب.',
            'name.unique' => 'هذا الدور موجود مسبقاً.',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => config('wesal_permissions.guard', 'web'),
        ]);
        if (!empty($validated['name_ar'] ?? null) && \Schema::hasColumn('roles', 'label_ar')) {
            $role->forceFill(['label_ar' => $validated['name_ar']])->save();
        }

        AuditLog::log('create_role', $role, null, $role->toArray(), 'إنشاء دور جديد: ' . $role->name);

        return redirect()->route('wesal.page', ['page' => 'roles-permissions'])
            ->with('success', 'تم إنشاء الدور بنجاح.');
    }

    public function updatePermissions(Request $request)
    {
        if (!auth()->user()->can('roles.edit')) {
            abort(403, 'ليس لديك صلاحية تعديل صلاحيات الأدوار.');
        }
        $guard = config('wesal_permissions.guard', 'web');
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => [
                'string',
                Rule::exists('permissions', 'name')->where('guard_name', $guard),
            ],
        ]);

        $role = Role::findOrFail($validated['role_id']);

        if (PermissionsRegistry::isRoleProtected($role->name)) {
            return back()->with('error', 'لا يمكن تعديل صلاحيات الدور المحمي.');
        }

        $oldPerms = $role->permissions->pluck('name')->toArray();
        $newPerms = $validated['permissions'] ?? [];

        $role->syncPermissions($newPerms);

        AuditLog::log('update_role_permissions', $role, ['permissions' => $oldPerms], ['permissions' => $newPerms],
            'تحديث صلاحيات الدور: ' . $role->name);

        return back()->with('success', 'تم تحديث صلاحيات الدور.');
    }

    public function destroy(Role $role)
    {
        if (!auth()->user()->can('roles.delete')) {
            abort(403, 'ليس لديك صلاحية حذف الأدوار.');
        }
        if (PermissionsRegistry::isRoleProtected($role->name)) {
            return back()->with('error', 'لا يمكن حذف الدور المحمي.');
        }

        $count = $role->users()->count();
        if ($count > 0) {
            return back()->with('error', "لا يمكن حذف الدور لوجود {$count} مستخدم مرتبط به.");
        }

        $old = $role->toArray();
        $role->delete();

        AuditLog::log('delete_role', null, $old, null, 'حذف الدور: ' . ($old['name'] ?? ''));

        return back()->with('success', 'تم حذف الدور.');
    }
}
