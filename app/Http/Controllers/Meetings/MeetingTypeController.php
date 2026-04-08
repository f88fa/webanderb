<?php

namespace App\Http\Controllers\Meetings;

use App\Http\Controllers\Controller;
use App\Models\Meetings\MeetingType;
use Illuminate\Http\Request;

class MeetingTypeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $maxOrder = MeetingType::max('sort_order') ?? 0;
        MeetingType::create(array_merge($request->only(['name_ar', 'name_en', 'description']), ['sort_order' => $maxOrder + 1]));

        return redirect()->back()->with('success', 'تم إضافة نوع الاجتماع.');
    }

    public function update(Request $request, MeetingType $meeting_type)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $meeting_type->update($request->only(['name_ar', 'name_en', 'description']));

        return redirect()->back()->with('success', 'تم تحديث نوع الاجتماع.');
    }

    public function destroy(MeetingType $meeting_type)
    {
        $meeting_type->delete();
        return redirect()->back()->with('success', 'تم حذف نوع الاجتماع.');
    }
}
