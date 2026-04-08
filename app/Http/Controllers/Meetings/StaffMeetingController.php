<?php

namespace App\Http\Controllers\Meetings;

use App\Http\Controllers\Controller;
use App\Models\Meetings\StaffMeeting;
use Illuminate\Http\Request;

class StaffMeetingController extends Controller
{
    public static function generateMeetingNo(): string
    {
        $year = date('Y');
        $last = StaffMeeting::whereRaw("meeting_no LIKE ?", ["SM-{$year}-%"])->orderByDesc('id')->first();
        $num = $last ? (int) substr($last->meeting_no, -4) + 1 : 1;
        return sprintf('SM-%s-%04d', $year, $num);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'meeting_date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'meeting_type_id' => 'nullable|exists:mt_meeting_types,id',
            'agenda' => 'nullable|string',
            'minutes' => 'nullable|string',
            'status' => 'nullable|in:scheduled,held,postponed,cancelled',
            'notes' => 'nullable|string',
            'employee_ids' => 'nullable|array',
            'employee_ids.*' => 'exists:hr_employees,id',
        ]);

        $data = $request->only(['title', 'meeting_date', 'location', 'meeting_type_id', 'agenda', 'minutes', 'notes']);
        $data['meeting_no'] = self::generateMeetingNo();
        $data['status'] = $request->get('status', 'scheduled');

        $meeting = StaffMeeting::create($data);

        if ($request->filled('employee_ids')) {
            $sync = [];
            foreach ($request->employee_ids as $empId) {
                $sync[$empId] = ['attended' => false];
            }
            $meeting->employees()->sync($sync);
        }

        return redirect()->back()->with('success', 'تم إضافة الاجتماع.');
    }

    public function update(Request $request, StaffMeeting $staff_meeting)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'meeting_date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'meeting_type_id' => 'nullable|exists:mt_meeting_types,id',
            'agenda' => 'nullable|string',
            'minutes' => 'nullable|string',
            'status' => 'required|in:scheduled,held,postponed,cancelled',
            'notes' => 'nullable|string',
            'employee_ids' => 'nullable|array',
            'employee_ids.*' => 'exists:hr_employees,id',
        ]);

        $staff_meeting->update($request->only(['title', 'meeting_date', 'location', 'meeting_type_id', 'agenda', 'minutes', 'status', 'notes']));

        $sync = [];
        if ($request->filled('employee_ids')) {
            foreach ($request->employee_ids as $empId) {
                $pivot = $staff_meeting->employees()->where('employee_id', $empId)->first();
                $sync[$empId] = ['attended' => $pivot?->pivot?->attended ?? false];
            }
        }
        $staff_meeting->employees()->sync($sync);

        return redirect()->back()->with('success', 'تم تحديث الاجتماع.');
    }

    public function destroy(StaffMeeting $staff_meeting)
    {
        $staff_meeting->delete();
        return redirect()->back()->with('success', 'تم حذف الاجتماع.');
    }
}
