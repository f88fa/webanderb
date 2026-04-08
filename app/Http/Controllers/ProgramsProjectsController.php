<?php

namespace App\Http\Controllers;

use App\Models\ProgramsProjects\Agreement;
use App\Models\ProgramsProjects\Donor;
use App\Models\ProgramsProjects\Expense;
use App\Models\ProgramsProjects\Grant;
use App\Models\ProgramsProjects\Project;
use App\Models\ProgramsProjects\ProjectDocument;
use App\Models\ProgramsProjects\ProjectTask;
use App\Models\ProgramsProjects\Stage;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * قسم البرامج والمشاريع
 */
class ProgramsProjectsController extends Controller
{
    public function show(Request $request, ?string $section = null, ?string $sub = null)
    {
        $formType = $section
            ? (($section === 'edit-project' || $section === 'edit-donor' || $section === 'project') ? "pp-" . ($section === 'project' ? 'project-show' : $section) : ($sub && !in_array($section, ['edit-project', 'edit-donor', 'project']) ? "pp-{$section}-{$sub}" : "pp-{$section}"))
            : 'pp';

        $data = [
            'page' => 'programs-projects',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => $formType,
            'ppSection' => $section,
            'ppSub' => $sub,
        ];

        $this->loadSectionData($section, $sub, $data);

        return view('wesal.index', $data);
    }

    private function loadSectionData(?string $section, ?string $sub, array &$data): void
    {
        $user = request()->user();
        $donorUser = $user && $user->donor()->exists() ? $user->donor : null;
        $projectQuery = Project::active()->with('donor')->orderBy('project_no');
        if ($donorUser) {
            $projectQuery = $projectQuery->where('donor_id', $donorUser->id);
        }
        $projects = $projectQuery->get();

        if (in_array($section, ['projects', 'stages', 'tasks', 'donors', 'budgets', 'documents', 'project', 'reports'], true) || ($section === 'donors' && $sub)) {
            $data['projects'] = $projects;
            $data['donors'] = $donorUser ? collect([$donorUser]) : Donor::orderBy('name_ar')->get();
            $data['isDonorUser'] = (bool) $donorUser;
        }

        if ($section === 'reports') {
            $reportTypes = ['summary' => 'تقرير ملخص', 'detailed' => 'تقرير تفصيلي', 'donor' => 'تقرير للجهة المانحة / جهة خارجية'];
            $data['reportTypes'] = $reportTypes;
            $projectId = request()->query('project_id');
            if ($sub && in_array($sub, array_keys($reportTypes), true) && $projectId && is_numeric($projectId)) {
                $reportProject = Project::with([
                    'donor',
                    'stages' => fn ($q) => $q->orderBy('order')->with([
                        'closedByUser',
                        'updates' => fn ($u) => $u->with(['updatedByUser', 'attachments'])->orderBy('update_date')->orderBy('id'),
                    ]),
                    'expenses',
                    'documents',
                ])->withCount(['tasks', 'stages'])->findOrFail((int) $projectId);
                if ($donorUser && $reportProject->donor_id !== $donorUser->id) {
                    abort(403, 'ليس لديك صلاحية عرض تقرير هذا المشروع.');
                }
                $data['formType'] = 'pp-reports-' . $sub;
                $data['reportProject'] = $reportProject;
                $data['reportType'] = $sub;
            }
            return;
        }

        if ($section === 'project' && $sub && is_numeric($sub)) {
            $project = Project::with([
                'donor',
                'stages' => fn ($q) => $q->orderBy('order')->with([
                    'closedByUser',
                    'updates' => fn ($u) => $u->with(['updatedByUser', 'attachments'])->orderBy('update_date')->orderBy('id'),
                ]),
            ])->findOrFail((int) $sub);
            if ($donorUser && $project->donor_id !== $donorUser->id) {
                abort(403, 'ليس لديك صلاحية عرض هذا المشروع.');
            }
            $data['showProject'] = $project;
            return;
        }

        if ($section === 'projects') {
            if ($sub === 'add' && $donorUser) {
                abort(403, 'الجهة المانحة لا يمكنها إضافة مشاريع.');
            }
            $baseQuery = Project::with('donor')->withCount(['stages', 'tasks'])->orderBy('project_no');
            if ($donorUser) {
                $baseQuery = $baseQuery->where('donor_id', $donorUser->id);
            }
            if ($sub === 'list') {
                $data['projectsList'] = (clone $baseQuery)->get();
            } elseif ($sub === 'archive') {
                $data['archivedProjects'] = (clone $baseQuery)->archived()->get();
            }
            return;
        }

        if ($section === 'donors' && $donorUser) {
            return; // الجهة المانحة لا تصل لقسم الجهات
        }

        if ($section === 'stages') {
            $stagesQuery = Stage::with('project')->orderByDesc('created_at');
            if ($donorUser) {
                $stagesQuery = $stagesQuery->whereHas('project', fn ($q) => $q->where('donor_id', $donorUser->id));
            }
            $data['stages'] = $stagesQuery->paginate(15);
            return;
        }

        if ($section === 'tasks') {
            $tasksQuery = ProjectTask::with(['project', 'stage', 'assignee'])->orderByDesc('created_at');
            if ($donorUser) {
                $tasksQuery = $tasksQuery->whereHas('project', fn ($q) => $q->where('donor_id', $donorUser->id));
            }
            $data['projectTasks'] = $tasksQuery->paginate(15);
            $data['users'] = User::orderBy('name')->get();
            return;
        }

        if ($section === 'donors') {
            $data['donorsList'] = Donor::withCount(['projects', 'agreements', 'grants'])->orderBy('name_ar')->get();
            if ($sub === 'agreements') {
                $data['agreements'] = Agreement::with(['donor', 'project'])->orderByDesc('created_at')->paginate(15);
            }
            if ($sub === 'grants') {
                $data['grants'] = Grant::with(['donor', 'project'])->orderByDesc('grant_date')->paginate(15);
                $data['agreementsForGrants'] = Agreement::with('donor')->orderBy('title')->get();
            }
            return;
        }

        if ($section === 'budgets') {
            $expQuery = Expense::with('project')->orderByDesc('expense_date');
            if ($donorUser) {
                $expQuery = $expQuery->whereHas('project', fn ($q) => $q->where('donor_id', $donorUser->id));
            }
            $data['expenses'] = $expQuery->paginate(20);
            return;
        }

        if ($section === 'documents') {
            $docQuery = ProjectDocument::with('project')->orderByDesc('document_date');
            if ($donorUser) {
                $docQuery = $docQuery->whereHas('project', fn ($q) => $q->where('donor_id', $donorUser->id));
            }
            $data['projectDocuments'] = $docQuery->paginate(15);
            return;
        }

        if ($section === 'edit-project' && $sub && is_numeric($sub)) {
            if ($donorUser) {
                abort(403, 'الجهة المانحة لا يمكنها تعديل المشاريع.');
            }
            $data['editProject'] = Project::with('donor')->findOrFail((int) $sub);
            $data['donors'] = Donor::orderBy('name_ar')->get();
            return;
        }

        if ($section === 'edit-donor' && $sub && is_numeric($sub)) {
            $data['editDonor'] = Donor::findOrFail((int) $sub);
            $donorId = (int) $sub;
            $data['usersForDonor'] = \App\Models\User::where(function ($q) use ($donorId) {
                $q->whereDoesntHave('donor')->orWhereHas('donor', fn ($dq) => $dq->where('id', $donorId));
            })->orderBy('name')->get();
            return;
        }
    }
}
