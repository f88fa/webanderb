<?php

namespace App\Http\Controllers\ProgramsProjects;

use App\Http\Controllers\Controller;
use App\Models\ProgramsProjects\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'project_no' => 'nullable|string|max:50|unique:pp_projects,project_no',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'donor_id' => 'nullable|exists:pp_donors,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $data = $request->only([
            'name_ar', 'name_en', 'description', 'donor_id',
            'start_date', 'end_date', 'budget_amount', 'notes',
        ]);
        $data['project_no'] = $request->filled('project_no')
            ? $request->project_no
            : self::generateProjectNo();

        Project::create($data);

        return redirect()->route('wesal.programs-projects.show', ['section' => 'projects', 'sub' => 'list'])
            ->with('success', 'تم إضافة المشروع بنجاح.');
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'project_no' => 'required|string|max:50|unique:pp_projects,project_no,' . $project->id,
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'donor_id' => 'nullable|exists:pp_donors,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'budget_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,completed,archived',
            'notes' => 'nullable|string',
        ]);

        $project->update($request->only([
            'project_no', 'name_ar', 'name_en', 'description', 'donor_id',
            'start_date', 'end_date', 'budget_amount', 'status', 'notes',
        ]));

        return redirect()->route('wesal.programs-projects.show', ['section' => 'projects', 'sub' => 'list'])
            ->with('success', 'تم تحديث المشروع.');
    }

    public function archive(Project $project)
    {
        $project->update(['status' => 'archived']);
        return redirect()->back()->with('success', 'تم أرشفة المشروع.');
    }

    public function unarchive(Project $project)
    {
        $project->update(['status' => 'active']);
        return redirect()->back()->with('success', 'تم إعادة تفعيل المشروع.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('wesal.programs-projects.show', ['section' => 'projects', 'sub' => 'list'])
            ->with('success', 'تم حذف المشروع.');
    }

    public static function generateProjectNo(): string
    {
        $year = date('Y');
        $last = Project::whereRaw("project_no LIKE ?", ["PP-{$year}-%"])->orderByDesc('id')->first();
        $num = $last ? (int) substr($last->project_no, -4) + 1 : 1;
        return sprintf('PP-%s-%04d', $year, $num);
    }
}
