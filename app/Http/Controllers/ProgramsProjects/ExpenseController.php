<?php

namespace App\Http\Controllers\ProgramsProjects;

use App\Http\Controllers\Controller;
use App\Models\ProgramsProjects\Expense;
use App\Models\ProgramsProjects\Project;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:pp_projects,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $expense = Expense::create($request->only([
            'project_id', 'description', 'amount', 'expense_date', 'category', 'notes',
        ]));

        $project = Project::find($request->project_id);
        if ($project) {
            $project->increment('spent_amount', $expense->amount);
        }

        return redirect()->back()->with('success', 'تم إضافة المصروف.');
    }

    public function destroy(Expense $expense)
    {
        $project = $expense->project;
        if ($project) {
            $project->decrement('spent_amount', $expense->amount);
        }
        $expense->delete();
        return redirect()->back()->with('success', 'تم حذف المصروف.');
    }
}
