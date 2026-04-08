<?php

namespace App\Http\Controllers\ProgramsProjects;

use App\Http\Controllers\Controller;
use App\Models\ProgramsProjects\Donor;
use Illuminate\Http\Request;

class DonorController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        Donor::create($request->only([
            'name_ar', 'name_en', 'contact_name', 'phone', 'email', 'address', 'notes',
        ]));

        return redirect()->route('wesal.programs-projects.show', ['section' => 'donors', 'sub' => 'list'])
            ->with('success', 'تم إضافة الجهة المانحة.');
    }

    public function update(Request $request, Donor $donor)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $data = $request->only(['name_ar', 'name_en', 'contact_name', 'phone', 'email', 'address', 'notes', 'user_id']);
        $data['user_id'] = $request->filled('user_id') ? $request->user_id : null;
        $donor->update($data);

        return redirect()->back()->with('success', 'تم تحديث الجهة المانحة.');
    }

    public function destroy(Donor $donor)
    {
        $donor->delete();
        return redirect()->back()->with('success', 'تم حذف الجهة المانحة.');
    }
}
