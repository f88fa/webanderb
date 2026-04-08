<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\LeaveBalance;
use App\Models\HR\LeaveRequest;
use App\Models\HR\LeaveType;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function show(LeaveRequest $leaveRequest)
    {
        $leaveRequest->load(['employee', 'leaveType', 'approvedByUser']);
        $approvalSequence = \App\Models\HR\RequestApprovalSequence::getForType('leave');

        return view('wesal.index', [
            'page' => 'hr',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'hr-leave-show',
            'hrSection' => 'leave',
            'hrSub' => 'show',
            'leaveRequest' => $leaveRequest,
            'approvalSequence' => $approvalSequence,
        ]);
    }

    public function storeRequest(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:hr_employees,id',
            'leave_type_id' => 'required|exists:hr_leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        $start = \Carbon\Carbon::parse($request->start_date);
        $end = \Carbon\Carbon::parse($request->end_date);
        $days = $start->diffInDays($end) + 1;

        LeaveRequest::create([
            'employee_id' => $request->employee_id,
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days' => $days,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        return redirect()->route('wesal.hr.show', ['section' => 'leave'])->with('success', 'تم تقديم طلب الإجازة.');
    }

    public function approve(LeaveRequest $leaveRequest)
    {
        if (!auth()->user()->can('hr.leave.approve')) {
            return redirect()->route('wesal.hr.show', ['section' => 'leave', 'sub' => 'approvals'])
                ->with('error', 'ليس لديك صلاحية اعتماد طلبات الإجازة.');
        }

        $year = $leaveRequest->start_date->format('Y');
        $balance = LeaveBalance::firstOrCreate(
            [
                'employee_id' => $leaveRequest->employee_id,
                'leave_type_id' => $leaveRequest->leave_type_id,
                'year' => $year,
            ],
            ['balance' => LeaveType::find($leaveRequest->leave_type_id)->days_per_year ?? 21, 'used' => 0]
        );
        $remaining = (float) $balance->balance - (float) $balance->used;
        if ($remaining < (float) $leaveRequest->days) {
            return redirect()->route('wesal.hr.show', ['section' => 'leave', 'sub' => 'approvals'])
                ->with('error', 'رصيد الإجازة غير كافٍ. المتبقي: ' . round($remaining, 1) . ' يوم، المطلوب: ' . $leaveRequest->days . ' يوم.');
        }

        $leaveRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        $balance->increment('used', $leaveRequest->days);

        return redirect()->route('wesal.hr.show', ['section' => 'leave', 'sub' => 'approvals'])->with('success', 'تمت الموافقة على طلب الإجازة.');
    }

    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        if (!auth()->user()->can('hr.leave.approve')) {
            return redirect()->route('wesal.hr.show', ['section' => 'leave', 'sub' => 'approvals'])
                ->with('error', 'ليس لديك صلاحية رفض طلبات الإجازة.');
        }

        $request->validate(['rejection_reason' => 'nullable|string|max:500']);

        $leaveRequest->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->route('wesal.hr.show', ['section' => 'leave', 'sub' => 'approvals'])->with('success', 'تم رفض طلب الإجازة.');
    }
}
