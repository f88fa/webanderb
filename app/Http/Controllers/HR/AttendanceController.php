<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\AttendanceRecord;
use App\Models\HR\Employee;
use App\Services\AttendanceRestrictionService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function checkIn(Request $request)
    {
        $request->validate(['employee_id' => 'required|exists:hr_employees,id']);

        if (!AttendanceRestrictionService::isAllowed($request)) {
            return redirect()->back()->with('error', AttendanceRestrictionService::getErrorMessage());
        }

        $date = $request->get('date', now()->toDateString());
        $record = AttendanceRecord::firstOrCreate(
            ['employee_id' => $request->employee_id, 'date' => $date],
            ['status' => 'present']
        );

        if (!$record->check_in) {
            $record->update(['check_in' => now()->format('H:i:s')]);
            return redirect()->back()->with('success', 'تم تسجيل الحضور.');
        }
        return redirect()->back()->with('info', 'تم تسجيل الحضور مسبقاً.');
    }

    public function checkOut(Request $request)
    {
        $request->validate(['employee_id' => 'required|exists:hr_employees,id']);

        if (!AttendanceRestrictionService::isAllowed($request)) {
            return redirect()->back()->with('error', AttendanceRestrictionService::getErrorMessage());
        }

        $date = $request->get('date', now()->toDateString());
        $record = AttendanceRecord::where('employee_id', $request->employee_id)->whereDate('date', $date)->first();

        if ($record && !$record->check_out) {
            $record->update(['check_out' => now()->format('H:i:s')]);
            return redirect()->back()->with('success', 'تم تسجيل الانصراف.');
        }
        return redirect()->back()->with('error', 'لم يتم العثور على سجل حضور أو تم تسجيل الانصراف مسبقاً.');
    }
}
