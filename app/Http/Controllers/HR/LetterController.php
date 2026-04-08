<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Letter;
use Illuminate\Http\Request;

class LetterController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:hr_employees,id',
            'subject' => 'required|string|max:255',
            'letter_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        Letter::create($request->only(['employee_id', 'subject', 'letter_date', 'notes']));
        return redirect()->route('wesal.hr.show', ['section' => 'letters'])->with('success', 'تم إضافة الخطاب.');
    }

    public function destroy(Letter $letter)
    {
        $letter->delete();
        return redirect()->route('wesal.hr.show', ['section' => 'letters'])->with('success', 'تم حذف الخطاب.');
    }
}
