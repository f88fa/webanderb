<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class EmployeeController extends Controller
{
    public function store(Request $request)
    {
        if (!auth()->user()->can('hr.employees.create')) {
            abort(403, 'ليس لديك صلاحية إضافة موظف.');
        }
        $rules = [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:hr_departments,id',
            'direct_manager_id' => 'nullable|exists:hr_employees,id',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:30',
            'national_id' => 'nullable|string|max:50',
            'hire_date' => 'nullable|date',
            'job_title' => 'nullable|string|max:255',
            'base_salary' => 'nullable|numeric|min:0',
            'link_user' => 'nullable|in:existing,create,none',
            'user_id' => 'required_if:link_user,existing|nullable|exists:users,id',
            'new_user_name' => 'required_if:link_user,create|nullable|string|max:255',
            'new_user_email' => 'required_if:link_user,create|nullable|email|unique:users,email',
            'new_user_password' => 'nullable|required_if:link_user,create|string|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ];
        $validated = $request->validate($rules);

        $userId = $this->resolveUserId($request, $validated);

        $maxNo = Employee::pluck('employee_no')->map(fn ($n) => is_numeric($n) ? (int) $n : 0)->max() ?? 0;
        $employeeNo = (string) ($maxNo + 1);

        Employee::create(array_merge($request->only([
            'name_ar', 'name_en', 'department_id', 'direct_manager_id', 'email', 'phone',
            'national_id', 'hire_date', 'job_title', 'base_salary',
        ]), ['user_id' => $userId, 'employee_no' => $employeeNo]));

        return redirect()->route('wesal.hr.show', ['section' => 'employees'])->with('success', 'تم إضافة الموظف بنجاح.');
    }

    public function update(Request $request, Employee $employee)
    {
        if (!auth()->user()->can('hr.employees.edit')) {
            abort(403, 'ليس لديك صلاحية تعديل الموظفين.');
        }
        $rules = [
            'employee_no' => 'required|string|max:50|unique:hr_employees,employee_no,' . $employee->id,
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:hr_departments,id',
            'direct_manager_id' => 'nullable|exists:hr_employees,id',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:30',
            'national_id' => 'nullable|string|max:50',
            'hire_date' => 'nullable|date',
            'job_title' => 'nullable|string|max:255',
            'base_salary' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,left,suspended',
            'link_user' => 'nullable|in:existing,create,none',
            'user_id' => 'required_if:link_user,existing|nullable|exists:users,id',
            'new_user_name' => 'required_if:link_user,create|nullable|string|max:255',
            'new_user_email' => 'required_if:link_user,create|nullable|email|unique:users,email',
            'new_user_password' => 'nullable|required_if:link_user,create|string|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
            'signature' => 'nullable|image|max:2048|mimes:png,jpg,jpeg,gif,webp',
        ];
        $validated = $request->validate($rules);

        $userId = $this->resolveUserId($request, $validated);

        $data = array_merge($request->only([
            'employee_no', 'name_ar', 'name_en', 'department_id', 'direct_manager_id', 'email', 'phone',
            'national_id', 'hire_date', 'job_title', 'base_salary', 'status',
        ]), ['user_id' => $userId]);

        if ($request->hasFile('signature') && $request->file('signature')->isValid()) {
            $dir = 'employee_signatures/' . $employee->id;
            if ($employee->signature_path) {
                Storage::disk('public')->delete($employee->signature_path);
            }
            $path = $request->file('signature')->store($dir, 'public');
            $data['signature_path'] = $path;
        }

        $employee->update($data);

        return redirect()->route('wesal.hr.show', ['section' => 'employees'])->with('success', 'تم تحديث بيانات الموظف.');
    }

    protected function resolveUserId(Request $request, array $validated): ?int
    {
        $linkUser = $validated['link_user'] ?? $request->input('link_user', 'none');
        if ($linkUser === 'none' || empty($linkUser)) {
            return null;
        }
        if ($linkUser === 'existing' && !empty($validated['user_id'])) {
            $user = User::find($validated['user_id']);
            if ($user && !empty($validated['roles'] ?? [])) {
                $user->syncRoles($validated['roles']);
            }
            return (int) $validated['user_id'];
        }
        if ($linkUser === 'create' && !empty($validated['new_user_email'])) {
            $user = User::create([
                'name' => $validated['new_user_name'],
                'email' => $validated['new_user_email'],
                'password' => Hash::make($validated['new_user_password'] ?? 'password'),
            ]);
            if (!empty($validated['roles'] ?? [])) {
                $user->syncRoles($validated['roles']);
            }
            return $user->id;
        }
        return null;
    }

    public function destroy(Employee $employee)
    {
        if (!auth()->user()->can('hr.employees.delete')) {
            abort(403, 'ليس لديك صلاحية حذف الموظفين.');
        }
        $employee->delete();
        return redirect()->route('wesal.hr.show', ['section' => 'employees'])->with('success', 'تم حذف الموظف.');
    }
}
