<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\AttendanceRecord;
use App\Models\HR\Department;
use App\Models\HR\Employee;
use App\Models\HR\LeaveRequest;
use App\Models\HR\LeaveType;
use App\Models\HR\PayrollRun;
use App\Models\HR\Shift;
use App\Services\Finance\ExcelExportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * قسم الموارد البشرية - يعيد عرض wesal.index مع page=hr و formType حسب المسار ويحمّل البيانات
 */
class HrController extends Controller
{
    public function show(Request $request, ?string $section = null, ?string $sub = null)
    {
        $formType = $section
            ? ($sub ? "hr-{$section}-{$sub}" : "hr-{$section}")
            : 'hr';

        $data = [
            'page' => 'hr',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => $formType,
            'hrSection' => $section,
            'hrSub' => $sub,
        ];

        $this->loadSectionData($request, $section, $sub, $data);

        return view('wesal.index', $data);
    }

    /**
     * تصدير السجل اليومي للحضور إلى Excel (عربي 100%)
     */
    public function attendanceLogExport(Request $request)
    {
        try {
            $excel = app(ExcelExportService::class);
            $date = $request->get('date', now()->toDateString());
            $records = AttendanceRecord::whereDate('date', $date)
                ->with(['employee', 'shift'])
                ->orderBy('check_in')
                ->get();

            $spreadsheet = $excel->newSpreadsheet();
            $sheet = $excel->setupArabicSheet($spreadsheet, $excel->ensureUtf8('السجل اليومي'));

            $headers = ['الموظف', 'وقت الحضور', 'وقت الانصراف', 'مدة الحضور', 'الوردية'];
            foreach ($headers as $i => $h) {
                $excel->setCellValueByColumnAndRow($sheet, $i + 1, 1, $excel->ensureUtf8($h));
            }
            $excel->styleHeaderRow($sheet, 1, count($headers));

            $row = 2;
            foreach ($records as $r) {
                $dateStr = $r->date ? (is_object($r->date) ? $r->date->format('Y-m-d') : $r->date) : now()->toDateString();
                $ci = $r->check_in ? Carbon::parse($dateStr . ' ' . $r->check_in) : null;
                $co = $r->check_out ? Carbon::parse($dateStr . ' ' . $r->check_out) : null;
                $dur = ($ci && $co) ? $ci->diffInMinutes($co) : null;
                $durFormatted = $dur !== null
                    ? (floor($dur / 60) > 0 ? floor($dur / 60) . ' ساعة ' : '') . ($dur % 60) . ' دقيقة'
                    : '-';

                $empName = $r->employee?->name_ar ?? '-';
                $excel->setCellValueByColumnAndRow($sheet, 1, $row, $excel->ensureUtf8($empName));
                $excel->setCellValueByColumnAndRow($sheet, 2, $row, $r->check_in ? $ci->format('H:i') : '-');
                $excel->setCellValueByColumnAndRow($sheet, 3, $row, $r->check_out ? $co->format('H:i') : '-');
                $excel->setCellValueByColumnAndRow($sheet, 4, $row, $excel->ensureUtf8($durFormatted));
                $excel->setCellValueByColumnAndRow($sheet, 5, $row, $excel->ensureUtf8($r->shift?->name_ar ?? '-'));
                $excel->setArabicFont($sheet, "A{$row}:E{$row}");
                $row++;
            }

            return $excel->download($spreadsheet, 'سجل-الحضور-' . $date, false);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Attendance log export failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('wesal.hr.show', ['section' => 'attendance', 'sub' => 'log'])
                ->with('error', 'فشل تصدير Excel: ' . $e->getMessage());
        }
    }

    public function editEmployee(Employee $employee)
    {
        $employee->load(['leaveBalances.leaveType', 'leaveRequests' => fn($q) => $q->with(['leaveType', 'approvedByUser'])]);
        $year = request()->get('balance_year', now()->year);
        $data = [
            'page' => 'hr',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'hr-employees-edit',
            'hrSection' => 'employees',
            'hrSub' => 'edit',
            'editEmployee' => $employee,
            'departments' => Department::where('is_active', true)->orderBy('order')->get(),
            'employeesForManager' => Employee::active()->where('id', '!=', $employee->id)->orderBy('name_ar')->get(),
            'users' => \App\Models\User::whereDoesntHave('employee')->orWhere('id', $employee->user_id)->orderBy('name')->get(),
            'roles' => \Spatie\Permission\Models\Role::where('guard_name', 'web')->orderBy('name')->get(),
            'balanceYear' => (int) $year,
            'leaveTypes' => LeaveType::where('is_active', true)->orderBy('code')->get(),
        ];
        return view('wesal.index', $data);
    }

    private function loadSectionData(Request $request, ?string $section, ?string $sub, array &$data): void
    {
        if ($section === 'employees' && !$sub) {
            $data['employees'] = Employee::with(['department', 'user'])->orderBy('employee_no')->get();
            return;
        }
        if ($section === 'employees' && $sub === 'create') {
            $data['departments'] = Department::where('is_active', true)->orderBy('order')->get();
            $data['employeesForManager'] = Employee::active()->orderBy('name_ar')->get();
            $data['users'] = \App\Models\User::whereDoesntHave('employee')->orderBy('name')->get();
            $data['roles'] = \Spatie\Permission\Models\Role::where('guard_name', 'web')->orderBy('name')->get();
            return;
        }
        if ($section === 'departments') {
            $data['departments'] = Department::withCount('employees')->with('parent')->orderBy('order')->get();
            return;
        }
        if ($section === 'organization') {
            $data['departmentTree'] = Department::whereNull('parent_id')
                ->where('is_active', true)
                ->with(['children' => fn ($q) => $q->withCount('employees')->orderBy('order')])
                ->withCount('employees')
                ->orderBy('order')
                ->get();
            return;
        }
        if ($section === 'attendance' && $sub === 'log') {
            $date = $request->get('date', now()->toDateString());
            $data['date'] = $date;
            $data['records'] = AttendanceRecord::whereDate('date', $date)->with(['employee', 'shift'])->orderBy('check_in')->get();
            return;
        }
        if ($section === 'attendance') {
            $data['employees'] = Employee::active()->orderBy('name_ar')->get();
            $data['todayRecords'] = AttendanceRecord::whereDate('date', now()->toDateString())->with('employee')->get();
            $data['shifts'] = Shift::activeList();
            return;
        }
        if ($section === 'shifts') {
            $data['shifts'] = Shift::orderBy('start_time')->get();
            return;
        }
        if ($section === 'attendance' && $sub === 'reports') {
            $data['employees'] = Employee::active()->orderBy('name_ar')->get();
            return;
        }
        if ($section === 'leave') {
            $data['leaveTypes'] = LeaveType::activeList();
            $data['employees'] = Employee::active()->orderBy('name_ar')->get();
            $data['approvalSequence'] = \App\Models\HR\RequestApprovalSequence::getForType('leave');
            return;
        }
        if ($section === 'leave' && $sub === 'balance') {
            $year = (int) ($request->get('year') ?: now()->year);
            $data['balanceYear'] = $year;
            $data['leaveTypes'] = LeaveType::where('is_active', true)->orderBy('code')->get();
            $data['employees'] = Employee::active()
                ->with(['leaveBalances' => fn($q) => $q->where('year', $year)->with('leaveType')])
                ->orderBy('name_ar')
                ->get();
            return;
        }
        if ($section === 'leave' && $sub === 'approvals') {
            $data['pendingRequests'] = LeaveRequest::where('status', 'pending')->with(['employee', 'leaveType'])->orderBy('created_at')->get();
            return;
        }
        if ($section === 'leave' && $sub === 'record') {
            $employeeId = $request->get('employee_id');
            $data['employees'] = Employee::active()->orderBy('name_ar')->get();
            $data['selectedEmployeeId'] = $employeeId ? (int) $employeeId : null;
            $query = LeaveRequest::with(['leaveType', 'approvedByUser', 'employee']);
            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }
            $data['leaveRecords'] = $query->orderByDesc('created_at')->paginate(20);
            return;
        }
        if ($section === 'leave-types') {
            $data['leaveTypes'] = LeaveType::orderBy('code')->get();
            $data['editLeaveType'] = $request->has('edit') ? LeaveType::find($request->get('edit')) : null;
            return;
        }
        if ($section === 'payroll') {
            $data['payrollRuns'] = PayrollRun::orderByDesc('year')->orderByDesc('month')->get();
            $data['employees'] = Employee::active()->orderBy('name_ar')->get();
            return;
        }
        if ($section === 'payroll' && in_array($sub, ['allowances', 'advances', 'export'], true)) {
            $data['employees'] = Employee::active()->orderBy('name_ar')->get();
            if ($sub === 'allowances') {
                $data['allowanceTypes'] = \App\Models\HR\AllowanceDeductionType::allowances();
                $data['deductionTypes'] = \App\Models\HR\AllowanceDeductionType::deductions();
            }
            if ($sub === 'advances') {
                $data['advances'] = \App\Models\HR\Advance::with('employee')->orderByDesc('created_at')->paginate(20);
            }
            return;
        }
        if (in_array($section, ['contracts', 'decisions', 'letters'], true)) {
            $data['employees'] = Employee::active()->orderBy('name_ar')->get();
            if ($section === 'contracts') {
                $data['contracts'] = \App\Models\HR\Contract::with('employee')->orderByDesc('start_date')->paginate(15);
            }
            if ($section === 'decisions') {
                $data['decisions'] = \App\Models\HR\Decision::with('employee')->orderByDesc('decision_date')->paginate(15);
            }
            if ($section === 'letters') {
                $data['letters'] = \App\Models\HR\Letter::with('employee')->orderByDesc('letter_date')->paginate(15);
            }
            return;
        }
        if ($section === 'performance' || ($section === 'performance' && in_array($sub, ['goals', 'training'], true))) {
            $data['employees'] = Employee::active()->orderBy('name_ar')->get();
            $data['reviews'] = \App\Models\HR\PerformanceReview::with('employee')->orderByDesc('year')->paginate(15);
            return;
        }
        if ($section === 'reports') {
            $data['employees'] = Employee::active()->orderBy('name_ar')->get();
            return;
        }
        if ($section === 'request-settings') {
            $data['requestTypes'] = \App\Models\HR\RequestApprovalSequence::TYPES;
            $data['sequencesByType'] = \App\Models\HR\RequestApprovalSequence::orderBy('request_type')->orderBy('step')->get()->groupBy('request_type');
            $data['employeesForApprover'] = Employee::active()->orderBy('name_ar')->get();
            $data['roles'] = \Spatie\Permission\Models\Role::where('guard_name', 'web')->orderBy('name')->get();
            return;
        }
    }
}
