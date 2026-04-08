<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function store(Request $request)
    {
        if (!auth()->user()->can('hr.departments.create')) {
            abort(403, 'ليس لديك صلاحية إضافة قسم.');
        }
        $request->validate([
            'code' => 'required|string|max:50|unique:hr_departments,code',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:hr_departments,id',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        Department::create([
            'code' => $request->code,
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'parent_id' => $request->parent_id,
            'order' => $request->order ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('wesal.hr.show', ['section' => 'departments'])->with('success', 'تم إضافة القسم.');
    }

    public function update(Request $request, Department $department)
    {
        if (!auth()->user()->can('hr.departments.edit')) {
            abort(403, 'ليس لديك صلاحية تعديل الأقسام.');
        }
        $request->validate([
            'code' => 'required|string|max:50|unique:hr_departments,code,' . $department->id,
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:hr_departments,id',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $department->update([
            'code' => $request->code,
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'parent_id' => $request->parent_id,
            'order' => $request->order ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('wesal.hr.show', ['section' => 'departments'])->with('success', 'تم تحديث القسم.');
    }

    public function destroy(Department $department)
    {
        if (!auth()->user()->can('hr.departments.delete')) {
            abort(403, 'ليس لديك صلاحية حذف الأقسام.');
        }
        if ($department->employees()->count() > 0) {
            return redirect()->route('wesal.hr.show', ['section' => 'departments'])->with('error', 'لا يمكن حذف قسم يحتوي على موظفين.');
        }
        $department->delete();
        return redirect()->route('wesal.hr.show', ['section' => 'departments'])->with('success', 'تم حذف القسم.');
    }
}
