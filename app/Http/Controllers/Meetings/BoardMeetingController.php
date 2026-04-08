<?php

namespace App\Http\Controllers\Meetings;

use App\Http\Controllers\Controller;
use App\Models\Meetings\BoardMeeting;
use Illuminate\Http\Request;

class BoardMeetingController extends Controller
{
    public static function generateMeetingNo(): string
    {
        $year = date('Y');
        $last = BoardMeeting::whereRaw("meeting_no LIKE ?", ["BM-{$year}-%"])->orderByDesc('id')->first();
        $num = $last ? (int) substr($last->meeting_no, -4) + 1 : 1;
        return sprintf('BM-%s-%04d', $year, $num);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'meeting_date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'agenda' => 'nullable|string',
            'minutes' => 'nullable|string',
            'status' => 'nullable|in:scheduled,held,postponed,cancelled',
            'notes' => 'nullable|string',
            'attendee_ids' => 'nullable|array',
            'attendee_ids.*' => 'exists:mt_board_members,id',
        ]);

        $data = $request->only(['title', 'meeting_date', 'location', 'agenda', 'minutes', 'notes']);
        $data['meeting_no'] = self::generateMeetingNo();
        $data['status'] = $request->get('status', 'scheduled');

        $meeting = BoardMeeting::create($data);

        if ($request->filled('attendee_ids')) {
            $sync = [];
            foreach ($request->attendee_ids as $memberId) {
                $sync[$memberId] = ['attended' => false];
            }
            $meeting->boardMembers()->sync($sync);
        }

        return redirect()->back()->with('success', 'تم إضافة الاجتماع.');
    }

    public function update(Request $request, BoardMeeting $board_meeting)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'meeting_date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'agenda' => 'nullable|string',
            'minutes' => 'nullable|string',
            'status' => 'required|in:scheduled,held,postponed,cancelled',
            'notes' => 'nullable|string',
            'attendee_ids' => 'nullable|array',
            'attendee_ids.*' => 'exists:mt_board_members,id',
        ]);

        $board_meeting->update($request->only(['title', 'meeting_date', 'location', 'agenda', 'minutes', 'status', 'notes']));

        $sync = [];
        if ($request->filled('attendee_ids')) {
            foreach ($request->attendee_ids as $memberId) {
                $sync[$memberId] = ['attended' => $board_meeting->boardMembers()->where('board_member_id', $memberId)->first()?->pivot?->attended ?? false];
            }
        }
        $board_meeting->boardMembers()->sync($sync);

        return redirect()->back()->with('success', 'تم تحديث الاجتماع.');
    }

    public function destroy(BoardMeeting $board_meeting)
    {
        $board_meeting->delete();
        return redirect()->back()->with('success', 'تم حذف الاجتماع.');
    }
}
