<?php

namespace App\Http\Controllers\EOffice;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskUpdate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    /** المهام المعنية بالمستخدم الحالي */
    public function index(Request $request)
    {
        $query = Task::with(['creator', 'assignees', 'updates'])
            ->concernedByUser(auth()->id());

        if ($request->filled('status')) {
            if ($request->status === 'open') {
                $query->where('status', 'open');
            } elseif ($request->status === 'closed') {
                $query->where('status', 'closed');
            }
        }

        $tasks = $query->orderByRaw("CASE WHEN status = 'open' THEN 0 ELSE 1 END")
            ->orderBy('due_at')
            ->paginate(15);

        return view('wesal.index', [
            'page' => 'e-office',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'tasks-index',
            'tasks' => $tasks,
        ]);
    }

    public function create()
    {
        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        return view('wesal.index', [
            'page' => 'e-office',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'tasks-create',
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_at' => 'required|date',
            'due_time' => 'nullable|string|max:5',
            'assignees' => 'required|array',
            'assignees.*' => 'exists:users,id',
        ], [
            'subject.required' => 'عنوان المهمة مطلوب.',
            'due_at.required' => 'وقت الإنجاز مطلوب.',
            'assignees.required' => 'حدد مسؤولاً واحداً على الأقل عن المهمة.',
        ]);

        $assignees = array_filter(array_unique((array) $request->assignees));
        if (empty($assignees)) {
            return back()->withErrors(['assignees' => 'حدد مسؤولاً واحداً على الأقل.'])->withInput();
        }

        $dueAt = $request->due_at;
        if ($request->filled('due_time')) {
            $t = $request->due_time;
            $dueAt = $request->due_at . ' ' . (strlen($t) === 5 ? $t . ':00' : $t);
        } else {
            $dueAt = $request->due_at . ' 23:59:00';
        }

        $task = Task::create([
            'subject' => $request->subject,
            'description' => $request->description,
            'due_at' => $dueAt,
            'created_by' => auth()->id(),
            'status' => 'open',
        ]);

        $task->assignees()->sync($assignees);

        return redirect()->route('wesal.e-office.tasks.show', $task)
            ->with('success', 'تم إنشاء المهمة بنجاح.');
    }

    public function show(Task $task)
    {
        $this->authorizeTask($task);

        $task->assignees()->where('user_id', auth()->id())->updateExistingPivot(auth()->id(), ['seen_at' => now()]);

        $task->load(['creator', 'assignees', 'closedByUser', 'updates.user', 'updates.attachments']);

        return view('wesal.index', [
            'page' => 'e-office',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'tasks-show',
            'task' => $task,
        ]);
    }

    public function addUpdate(Request $request, Task $task)
    {
        $this->authorizeTask($task);

        if ($task->isClosed()) {
            return back()->withErrors(['error' => 'لا يمكن إضافة تحديثات لمهمة مغلقة.']);
        }

        $request->validate([
            'body' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240',
        ], ['body.required' => 'نص التحديث مطلوب.']);

        DB::beginTransaction();
        try {
            $update = $task->updates()->create([
                'user_id' => auth()->id(),
                'body' => $request->body,
            ]);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('task_updates/' . $update->id, 'public');
                    $update->attachments()->create([
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                    ]);
                }
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'حدث خطأ أثناء حفظ التحديث.'])->withInput();
        }

        return back()->with('success', 'تم إضافة التحديث.');
    }

    public function close(Request $request, Task $task)
    {
        $this->authorizeTask($task);

        if ($task->isClosed()) {
            return back()->withErrors(['error' => 'المهمة مغلقة مسبقاً.']);
        }

        $request->validate([
            'evidence' => 'nullable|file|max:10240',
        ]);

        $evidencePath = null;
        $evidenceName = null;
        if ($request->hasFile('evidence')) {
            $file = $request->file('evidence');
            $evidencePath = $file->store('task_evidence/' . $task->id, 'public');
            $evidenceName = $file->getClientOriginalName();
        }

        $task->update([
            'status' => 'closed',
            'closed_at' => now(),
            'closed_by' => auth()->id(),
            'evidence_path' => $evidencePath,
            'evidence_original_name' => $evidenceName,
        ]);

        return back()->with('success', 'تم إغلاق المهمة بنجاح.');
    }

    private function authorizeTask(Task $task): void
    {
        $userId = auth()->id();
        $isCreator = $task->created_by === $userId;
        $isAssignee = $task->assignees()->where('users.id', $userId)->exists();
        if (!$isCreator && !$isAssignee) {
            abort(404);
        }
    }
}
