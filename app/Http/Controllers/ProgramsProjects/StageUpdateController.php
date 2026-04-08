<?php

namespace App\Http\Controllers\ProgramsProjects;

use App\Http\Controllers\Controller;
use App\Models\ProgramsProjects\StageUpdate;
use App\Models\ProgramsProjects\StageUpdateAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StageUpdateController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'stage_id' => 'required|exists:pp_stages,id',
            'update_date' => 'required|date',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'progress_percentage' => 'nullable|integer|min:0|max:100',
            'attachments.*' => 'nullable|file|max:20480',
        ]);

        $stage = \App\Models\ProgramsProjects\Stage::findOrFail($request->stage_id);
        if ($stage->status === 'completed') {
            return redirect()->route('wesal.programs-projects.show', ['section' => 'project', 'sub' => $stage->project_id])
                ->with('error', 'لا يمكن إضافة تحديث لمرحلة مغلقة.');
        }

        $update = StageUpdate::create([
            'stage_id' => $request->stage_id,
            'update_date' => $request->update_date,
            'title' => $request->title,
            'description' => $request->description,
            'progress_percentage' => $request->progress_percentage,
            'updated_by' => auth()->id(),
        ]);

        if ($request->hasFile('attachments')) {
            $dir = 'stage_updates/' . $update->id;
            foreach ($request->file('attachments') as $file) {
                $path = $file->store($dir, 'public');
                StageUpdateAttachment::create([
                    'stage_update_id' => $update->id,
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'uploaded_by' => auth()->id(),
                ]);
            }
        }

        return redirect()->route('wesal.programs-projects.show', ['section' => 'project', 'sub' => $stage->project_id])
            ->with('success', 'تم تسجيل تحديث المرحلة.');
    }

    public function destroy(StageUpdate $stageUpdate)
    {
        $projectId = $stageUpdate->stage?->project_id;
        $stageUpdate->delete();
        return redirect()->route('wesal.programs-projects.show', ['section' => 'project', 'sub' => $projectId])
            ->with('success', 'تم حذف سجل التحديث.');
    }
}
