<?php

namespace App\Http\Controllers\Meetings;

use App\Http\Controllers\Controller;
use App\Models\Meetings\BoardDecision;
use Illuminate\Http\Request;

class BoardDecisionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'decision_date' => 'required|date',
            'board_meeting_id' => 'nullable|exists:mt_board_meetings,id',
            'decision_no' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        BoardDecision::create($request->only(['title', 'decision_date', 'board_meeting_id', 'decision_no', 'description', 'notes']));

        return redirect()->back()->with('success', 'تم إضافة القرار.');
    }

    public function update(Request $request, BoardDecision $board_decision)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'decision_date' => 'required|date',
            'board_meeting_id' => 'nullable|exists:mt_board_meetings,id',
            'decision_no' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $board_decision->update($request->only(['title', 'decision_date', 'board_meeting_id', 'decision_no', 'description', 'notes']));

        return redirect()->back()->with('success', 'تم تحديث القرار.');
    }

    public function destroy(BoardDecision $board_decision)
    {
        $board_decision->delete();
        return redirect()->back()->with('success', 'تم حذف القرار.');
    }
}
