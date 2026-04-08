<?php

namespace App\Http\Controllers\ProgramsProjects;

use App\Http\Controllers\Controller;
use App\Models\ProgramsProjects\Stage;
use Illuminate\Http\Request;

class StageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:pp_projects,id',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'nullable|in:pending,in_progress,completed',
            'notes' => 'nullable|string',
        ]);

        Stage::create($request->only([
            'project_id', 'name_ar', 'name_en', 'order', 'start_date', 'end_date', 'status', 'notes',
        ]));

        return redirect()->back()->with('success', 'تم إضافة المرحلة.');
    }

    public function update(Request $request, Stage $stage)
    {
        $rules = [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed',
            'notes' => 'nullable|string',
            'closure_reason' => 'nullable|string|max:2000',
        ];
        if ($request->status === 'completed') {
            $rules['closure_reason'] = 'required|string|max:2000';
        }
        $request->validate($rules);

        $data = $request->only(['name_ar', 'name_en', 'order', 'start_date', 'end_date', 'status', 'notes']);
        if ($request->status === 'completed') {
            $data['closed_at'] = now();
            $data['closed_by'] = auth()->id();
            $data['closure_reason'] = $request->closure_reason;
        } else {
            $data['closed_at'] = null;
            $data['closed_by'] = null;
            $data['closure_reason'] = null;
        }

        $stage->update($data);

        $projectId = $stage->project_id;
        return redirect()->route('wesal.programs-projects.show', ['section' => 'project', 'sub' => $projectId])
            ->with('success', 'تم تحديث المرحلة.');
    }

    public function destroy(Stage $stage)
    {
        $stage->delete();
        return redirect()->back()->with('success', 'تم حذف المرحلة.');
    }
}
