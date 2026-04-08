<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\AdministrativeRequest;
use App\Models\HR\AttendanceRecord;
use App\Models\HR\Employee;
use App\Models\HR\LeaveRequest;
use App\Models\HR\LeaveType;
use App\Services\AttendanceRestrictionService;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * الطلبات الإدارية — واجهة ذاتية للمستخدم (خاصة به فقط)
 * يلزم ربط المستخدم بموظف (user_id على hr_employees)
 */
class AdministrativeRequestsController extends Controller
{
    protected function myEmployee(): ?Employee
    {
        return Employee::where('user_id', auth()->id())->first();
    }

    public function show(Request $request, ?string $section = null)
    {
        $employee = $this->myEmployee();
        $section = in_array($section, ['leave', 'general', 'financial', 'attendance'], true) ? $section : 'leave';

        $data = [
            'page' => 'requests',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'requestSection' => $section,
            'employee' => $employee,
        ];

        if ($section === 'leave') {
            $data['leaveTypes'] = LeaveType::activeList();
            if ($employee) {
                $data['myLeaveRequests'] = LeaveRequest::where('employee_id', $employee->id)
                    ->with(['leaveType', 'approvedByUser'])
                    ->orderByDesc('created_at')
                    ->paginate(15);
            } else {
                $data['myLeaveRequests'] = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            }
            $data['approvalSequence'] = \App\Models\HR\RequestApprovalSequence::getForType('leave');
        }

        if ($section === 'general') {
            if ($employee) {
                $data['myRequests'] = AdministrativeRequest::where('employee_id', $employee->id)
                    ->where('type', AdministrativeRequest::TYPE_GENERAL)
                    ->orderByDesc('created_at')
                    ->paginate(15);
            } else {
                $data['myRequests'] = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            }
        }

        if ($section === 'financial') {
            if ($employee) {
                $data['myRequests'] = AdministrativeRequest::where('employee_id', $employee->id)
                    ->where('type', AdministrativeRequest::TYPE_FINANCIAL)
                    ->orderByDesc('created_at')
                    ->paginate(15);
            } else {
                $data['myRequests'] = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            }
        }

        if ($section === 'attendance') {
            if ($employee) {
                $data['todayRecord'] = AttendanceRecord::where('employee_id', $employee->id)
                    ->whereDate('date', now()->toDateString())
                    ->first();
                $date = $request->get('date', now()->toDateString());
                $data['date'] = $date;
                $data['myRecords'] = AttendanceRecord::where('employee_id', $employee->id)
                    ->whereDate('date', $date)
                    ->orderBy('check_in')
                    ->get();
            } else {
                $data['todayRecord'] = null;
                $data['date'] = $request->get('date', now()->toDateString());
                $data['myRecords'] = collect();
            }
        }

        return view('wesal.index', $data);
    }

    /**
     * عرض تفاصيل طلب إجازة للموظف (طلبه فقط) مع تسلسل الموافقات
     */
    public function showLeave(LeaveRequest $leaveRequest)
    {
        $employee = $this->myEmployee();
        if (!$employee || $leaveRequest->employee_id !== $employee->id) {
            abort(403, 'غير مصرح بعرض هذا الطلب.');
        }
        $leaveRequest->load(['employee', 'leaveType', 'approvedByUser']);
        $approvalSequence = \App\Models\HR\RequestApprovalSequence::getForType('leave');
        return view('wesal.index', [
            'page' => 'requests',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'requestSection' => 'leave-show',
            'leaveRequest' => $leaveRequest,
            'approvalSequence' => $approvalSequence,
            'fromRequests' => true,
        ]);
    }

    public function storeGeneral(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'nullable|string|max:5000',
        ]);

        $employee = $this->myEmployee();
        if (!$employee) {
            return redirect()->route('wesal.requests.show', ['section' => 'general'])
                ->with('error', 'يجب ربط حسابك بموظف في الموارد البشرية.');
        }

        AdministrativeRequest::create([
            'employee_id' => $employee->id,
            'type' => AdministrativeRequest::TYPE_GENERAL,
            'title' => $request->title,
            'body' => $request->body,
            'status' => AdministrativeRequest::STATUS_PENDING,
        ]);

        return redirect()->route('wesal.requests.show', ['section' => 'general'])
            ->with('success', 'تم تقديم الطلب العام.');
    }

    public function storeFinancial(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'nullable|string|max:5000',
        ]);

        $employee = $this->myEmployee();
        if (!$employee) {
            return redirect()->route('wesal.requests.show', ['section' => 'financial'])
                ->with('error', 'يجب ربط حسابك بموظف في الموارد البشرية.');
        }

        AdministrativeRequest::create([
            'employee_id' => $employee->id,
            'type' => AdministrativeRequest::TYPE_FINANCIAL,
            'title' => $request->title,
            'body' => $request->body,
            'status' => AdministrativeRequest::STATUS_PENDING,
        ]);

        return redirect()->route('wesal.requests.show', ['section' => 'financial'])
            ->with('success', 'تم تقديم الطلب المالي.');
    }

    /** تقديم طلب إجازة من واجهة الطلبات الإدارية (موظف المستخدم الحالي فقط) */
    public function storeLeave(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:hr_leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $employee = $this->myEmployee();
        if (!$employee) {
            return redirect()->route('wesal.requests.show', ['section' => 'leave'])
                ->with('error', 'يجب ربط حسابك بموظف في الموارد البشرية.');
        }

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        $days = $start->diffInDays($end) + 1;

        LeaveRequest::create([
            'employee_id' => $employee->id,
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days' => $days,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        return redirect()->route('wesal.requests.show', ['section' => 'leave'])
            ->with('success', 'تم تقديم طلب الإجازة.');
    }

    /** تسجيل حضور من الطلبات الإدارية (للموظف المرتبط بالمستخدم فقط) */
    public function attendanceCheckIn(Request $request)
    {
        $employee = $this->myEmployee();
        if (!$employee) {
            return redirect()->route('wesal.requests.show', ['section' => 'attendance'])
                ->with('error', 'يجب ربط حسابك بموظف.');
        }
        if (!AttendanceRestrictionService::isAllowed($request)) {
            return redirect()->route('wesal.requests.show', ['section' => 'attendance'])
                ->with('error', AttendanceRestrictionService::getErrorMessage());
        }
        $date = $request->get('date', now()->toDateString());
        $record = AttendanceRecord::firstOrCreate(
            ['employee_id' => $employee->id, 'date' => $date],
            ['status' => 'present']
        );
        if (!$record->check_in) {
            $record->update(['check_in' => now()->format('H:i:s')]);
            return redirect()->route('wesal.requests.show', ['section' => 'attendance'])->with('success', 'تم تسجيل الحضور.');
        }
        return redirect()->route('wesal.requests.show', ['section' => 'attendance'])->with('info', 'تم تسجيل الحضور مسبقاً.');
    }

    /** تسجيل انصراف من الطلبات الإدارية */
    public function attendanceCheckOut(Request $request)
    {
        $employee = $this->myEmployee();
        if (!$employee) {
            return redirect()->route('wesal.requests.show', ['section' => 'attendance'])
                ->with('error', 'يجب ربط حسابك بموظف.');
        }
        if (!AttendanceRestrictionService::isAllowed($request)) {
            return redirect()->route('wesal.requests.show', ['section' => 'attendance'])
                ->with('error', AttendanceRestrictionService::getErrorMessage());
        }
        $date = $request->get('date', now()->toDateString());
        $record = AttendanceRecord::where('employee_id', $employee->id)->whereDate('date', $date)->first();
        if ($record && !$record->check_out) {
            $record->update(['check_out' => now()->format('H:i:s')]);
            return redirect()->route('wesal.requests.show', ['section' => 'attendance'])->with('success', 'تم تسجيل الانصراف.');
        }
        return redirect()->route('wesal.requests.show', ['section' => 'attendance'])->with('error', 'لم يتم العثور على سجل حضور أو تم تسجيل الانصراف مسبقاً.');
    }
}
