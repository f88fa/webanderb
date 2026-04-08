<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'code' => 'required|string|max:30|unique:hr_leave_types,code',
            'days_per_year' => 'required|integer|min:0|max:365',
            'is_paid' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        LeaveType::create([
            'name_ar' => $request->name_ar,
            'code' => strtoupper($request->code),
            'days_per_year' => $request->days_per_year,
            'is_paid' => $request->boolean('is_paid', true),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('wesal.hr.show', ['section' => 'leave-types'])->with('success', 'تم إضافة نوع الإجازة.');
    }

    public function update(Request $request, LeaveType $leave_type)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'code' => 'required|string|max:30|unique:hr_leave_types,code,' . $leave_type->id,
            'days_per_year' => 'required|integer|min:0|max:365',
            'is_paid' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $leave_type->update([
            'name_ar' => $request->name_ar,
            'code' => strtoupper($request->code),
            'days_per_year' => $request->days_per_year,
            'is_paid' => $request->boolean('is_paid', true),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('wesal.hr.show', ['section' => 'leave-types'])->with('success', 'تم تحديث نوع الإجازة.');
    }

    public function destroy(LeaveType $leave_type)
    {
        if ($leave_type->leaveRequests()->exists() || $leave_type->leaveBalances()->exists()) {
            return redirect()->route('wesal.hr.show', ['section' => 'leave-types'])
                ->with('error', 'لا يمكن حذف نوع إجازة مستخدم في طلبات أو أرصدة.');
        }
        $leave_type->delete();
        return redirect()->route('wesal.hr.show', ['section' => 'leave-types'])->with('success', 'تم حذف نوع الإجازة.');
    }
}
