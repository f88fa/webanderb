<?php

namespace App\Http\Controllers\Meetings;

use App\Http\Controllers\Controller;
use App\Models\Meetings\BoardMember;
use Illuminate\Http\Request;

class BoardMemberController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'position_ar' => 'nullable|string|max:255',
            'position_en' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email',
            'notes' => 'nullable|string',
        ]);

        BoardMember::create($request->only(['name_ar', 'name_en', 'position_ar', 'position_en', 'phone', 'email', 'notes']));

        return redirect()->back()->with('success', 'تم إضافة العضو.');
    }

    public function update(Request $request, BoardMember $board_member)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'position_ar' => 'nullable|string|max:255',
            'position_en' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        $data = $request->only(['name_ar', 'name_en', 'position_ar', 'position_en', 'phone', 'email', 'notes']);
        $data['is_active'] = $request->boolean('is_active', true);
        $board_member->update($data);

        return redirect()->back()->with('success', 'تم تحديث العضو.');
    }

    public function destroy(BoardMember $board_member)
    {
        $board_member->delete();
        return redirect()->back()->with('success', 'تم حذف العضو.');
    }
}
