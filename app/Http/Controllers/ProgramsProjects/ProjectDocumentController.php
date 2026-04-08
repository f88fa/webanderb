<?php

namespace App\Http\Controllers\ProgramsProjects;

use App\Http\Controllers\Controller;
use App\Models\ProgramsProjects\ProjectDocument;
use Illuminate\Http\Request;

class ProjectDocumentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:pp_projects,id',
            'title' => 'required|string|max:255',
            'document_type' => 'nullable|string|max:50',
            'document_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        ProjectDocument::create($request->only([
            'project_id', 'title', 'document_type', 'document_date', 'notes',
        ]));

        return redirect()->back()->with('success', 'تم إضافة المستند.');
    }

    public function destroy(ProjectDocument $project_document)
    {
        $project_document->delete();
        return redirect()->back()->with('success', 'تم حذف المستند.');
    }
}
