<?php

namespace App\Http\Controllers\Wesal;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * حفظ مستخدم جديد
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('users.create')) {
            abort(403, 'ليس لديك صلاحية إضافة مستخدم.');
        }
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'role'     => ['nullable', 'string', 'exists:roles,name'],
        ], [
            'name.required'     => 'الاسم مطلوب.',
            'email.required'    => 'البريد الإلكتروني مطلوب.',
            'email.email'       => 'أدخل بريداً إلكترونياً صحيحاً.',
            'email.unique'      => 'هذا البريد مسجل مسبقاً.',
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.confirmed'=> 'تأكيد كلمة المرور غير مطابق.',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => $validated['password'],
        ]);

        if (!empty($validated['role'])) {
            $user->assignRole($validated['role']);
        }

        return redirect()->route('wesal.page', ['page' => 'users'])
            ->with('success', 'تمت إضافة المستخدم بنجاح.');
    }

    /**
     * تحديث بيانات مستخدم
     */
    public function update(Request $request, User $user)
    {
        if (!auth()->user()->can('users.edit')) {
            abort(403, 'ليس لديك صلاحية تعديل المستخدمين.');
        }
        if ($user->hasRole('SuperAdmin') && !auth()->user()->hasRole('SuperAdmin')) {
            abort(403, 'لا يمكن تعديل حساب المدير الأعلى.');
        }
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'confirmed', Password::defaults()],
        ], [
            'name.required'  => 'الاسم مطلوب.',
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.unique'   => 'هذا البريد مسجل لمستخدم آخر.',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = $validated['password'];
        }
        $user->save();

        return redirect()->route('wesal.page', ['page' => 'users'])
            ->with('success', 'تم تحديث المستخدم بنجاح.');
    }

    /**
     * تحديث أدوار المستخدم
     */
    public function updateRoles(Request $request, User $user)
    {
        if (!auth()->user()->can('users.roles.assign')) {
            abort(403, 'ليس لديك صلاحية تعيين الأدوار.');
        }
        if ($user->hasRole('SuperAdmin') && !auth()->user()->hasRole('SuperAdmin')) {
            abort(403, 'لا يمكن تغيير أدوار المدير الأعلى.');
        }
        $validated = $request->validate([
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ]);

        $roles = $validated['roles'] ?? [];
        $user->syncRoles($roles);

        return redirect()->route('wesal.page', ['page' => 'users'])
            ->with('success', 'تم تحديث صلاحيات المستخدم بنجاح.');
    }
}
