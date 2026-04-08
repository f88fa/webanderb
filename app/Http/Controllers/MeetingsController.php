<?php

namespace App\Http\Controllers;

use App\Models\Meetings\BoardDecision;
use App\Models\Meetings\BoardMeeting;
use App\Models\Meetings\BoardMember;
use App\Models\Meetings\MeetingType;
use App\Models\Meetings\StaffMeeting;
use Illuminate\Http\Request;

/**
 * قسم الاجتماعات
 */
class MeetingsController extends Controller
{
    private const EDIT_SECTIONS = ['edit-board-member', 'edit-board-meeting', 'edit-staff-meeting', 'edit-board-decision', 'edit-meeting-type'];

    public function show(Request $request, ?string $section = null, ?string $sub = null)
    {
        $formType = $section
            ? (in_array($section, self::EDIT_SECTIONS) ? "meetings-{$section}" : ($sub && !in_array($section, self::EDIT_SECTIONS) ? "meetings-{$section}-{$sub}" : "meetings-{$section}"))
            : 'meetings';

        $data = [
            'page' => 'meetings',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => $formType,
            'meetingsSection' => $section,
            'meetingsSub' => $sub,
        ];

        $this->loadSectionData($section, $sub, $data);

        return view('wesal.index', $data);
    }

    private function loadSectionData(?string $section, ?string $sub, array &$data): void
    {
        if ($section === 'meeting-types') {
            $data['meetingTypes'] = MeetingType::orderBy('sort_order')->orderBy('name_ar')->get();
            return;
        }

        if ($section === 'board-members') {
            $data['boardMembers'] = BoardMember::orderBy('name_ar')->get();
            return;
        }

        if ($section === 'board-meetings') {
            $data['boardMeetings'] = BoardMeeting::with('boardMembers')->orderByDesc('meeting_date')->paginate(15);
            $data['boardMembersList'] = BoardMember::where('is_active', true)->orderBy('name_ar')->get();
            return;
        }

        if ($section === 'staff-meetings') {
            $data['staffMeetings'] = StaffMeeting::with(['meetingType', 'employees'])->orderByDesc('meeting_date')->paginate(15);
            $data['meetingTypes'] = MeetingType::orderBy('sort_order')->orderBy('name_ar')->get();
            $data['employeesList'] = \App\Models\HR\Employee::where('status', 'active')->orderBy('name_ar')->get();
            return;
        }

        if ($section === 'board-decisions') {
            $data['boardDecisions'] = BoardDecision::with('boardMeeting')->orderByDesc('decision_date')->paginate(15);
            $data['boardMeetingsList'] = BoardMeeting::orderByDesc('meeting_date')->get();
            return;
        }

        if ($section === 'edit-meeting-type' && $sub && is_numeric($sub)) {
            $data['editMeetingType'] = MeetingType::findOrFail((int) $sub);
            return;
        }

        if ($section === 'edit-board-member' && $sub && is_numeric($sub)) {
            $data['editBoardMember'] = BoardMember::findOrFail((int) $sub);
            return;
        }

        if ($section === 'edit-board-meeting' && $sub && is_numeric($sub)) {
            $data['editBoardMeeting'] = BoardMeeting::with('boardMembers')->findOrFail((int) $sub);
            $data['boardMembersList'] = BoardMember::where('is_active', true)->orderBy('name_ar')->get();
            return;
        }

        if ($section === 'edit-staff-meeting' && $sub && is_numeric($sub)) {
            $data['editStaffMeeting'] = StaffMeeting::with('employees')->findOrFail((int) $sub);
            $data['meetingTypes'] = MeetingType::orderBy('sort_order')->orderBy('name_ar')->get();
            $data['employeesList'] = \App\Models\HR\Employee::where('status', 'active')->orderBy('name_ar')->get();
            return;
        }

        if ($section === 'edit-board-decision' && $sub && is_numeric($sub)) {
            $data['editBoardDecision'] = BoardDecision::findOrFail((int) $sub);
            $data['boardMeetingsList'] = BoardMeeting::orderByDesc('meeting_date')->get();
            return;
        }
    }
}
