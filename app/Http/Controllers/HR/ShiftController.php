<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'break_minutes' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        Shift::create([
            'name_ar' => $request->name_ar,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'break_minutes' => $request->break_minutes ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('wesal.hr.show', ['section' => 'shifts'])->with('success', 'تم إضافة الوردية.');
    }

    public function update(Request $request, Shift $shift)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'break_minutes' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $shift->update([
            'name_ar' => $request->name_ar,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'break_minutes' => $request->break_minutes ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('wesal.hr.show', ['section' => 'shifts'])->with('success', 'تم تحديث الوردية.');
    }

    public function destroy(Shift $shift)
    {
        $shift->delete();
        return redirect()->route('wesal.hr.show', ['section' => 'shifts'])->with('success', 'تم حذف الوردية.');
    }
}
