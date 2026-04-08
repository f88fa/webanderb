<?php

namespace App\Http\Controllers\ProgramsProjects;

use App\Http\Controllers\Controller;
use App\Models\ProgramsProjects\ProjectTask;
use Illuminate\Http\Request;

class ProjectTaskController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:pp_projects,id',
            'stage_id' => 'nullable|exists:pp_stages,id',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'assignee_id' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'status' => 'nullable|in:todo,in_progress,done',
            'priority' => 'nullable|in:low,medium,high',
            'notes' => 'nullable|string',
        ]);

        ProjectTask::create($request->only([
            'project_id', 'stage_id', 'name_ar', 'name_en', 'assignee_id',
            'due_date', 'status', 'priority', 'notes',
        ]));

        return redirect()->back()->with('success', 'تم إضافة المهمة.');
    }

    public function update(Request $request, ProjectTask $project_task)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'stage_id' => 'nullable|exists:pp_stages,id',
            'assignee_id' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'status' => 'required|in:todo,in_progress,done',
            'priority' => 'required|in:low,medium,high',
            'notes' => 'nullable|string',
        ]);

        $project_task->update($request->only([
            'name_ar', 'name_en', 'stage_id', 'assignee_id', 'due_date', 'status', 'priority', 'notes',
        ]));

        return redirect()->back()->with('success', 'تم تحديث المهمة.');
    }

    public function destroy(ProjectTask $project_task)
    {
        $project_task->delete();
        return redirect()->back()->with('success', 'تم حذف المهمة.');
    }
}
