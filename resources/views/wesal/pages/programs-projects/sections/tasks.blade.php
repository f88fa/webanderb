<div class="content-card">
    <div class="page-header">
        <h1 class="page-title"><i class="fas fa-tasks" style="color: var(--primary-color);"></i> المهام</h1>
    </div>
    @if(session('success'))<div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>@endif
    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color); margin-bottom: 2rem;">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">إضافة مهمة</h3>
        <form method="POST" action="{{ route('wesal.programs-projects.project-tasks.store') }}" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem; align-items: end;">
            @csrf
            <div><label class="form-label">المشروع <span style="color:#dc3545">*</span></label><select name="project_id" class="form-control" required id="taskProject"><option value="">-- اختر --</option>@foreach($projects ?? [] as $pr)<option value="{{ $pr->id }}">{{ $pr->name_ar }}</option>@endforeach</select></div>
            <div><label class="form-label">اسم المهمة <span style="color:#dc3545">*</span></label><input type="text" name="name_ar" class="form-control" required></div>
            <div><label class="form-label">تاريخ الاستحقاق</label><input type="date" name="due_date" class="form-control"></div>
            <div><label class="form-label">المُعيّن</label><select name="assignee_id" class="form-control"><option value="">-- اختر --</option>@foreach($users ?? [] as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach</select></div>
            <div><label class="form-label">الحالة</label><select name="status" class="form-control"><option value="todo">لم تبدأ</option><option value="in_progress">جاري</option><option value="done">منتهية</option></select></div>
            <div><label class="form-label">الأولوية</label><select name="priority" class="form-control"><option value="low">منخفضة</option><option value="medium" selected>متوسطة</option><option value="high">عالية</option></select></div>
            <div><button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة</button></div>
        </form>
    </div>
    <h3 style="color: var(--text-primary); margin-bottom: 1rem;">قائمة المهام</h3>
    @if(isset($projectTasks) && $projectTasks->count() > 0)
        <div class="table-container">
            <table style="direction: rtl;"><thead><tr><th>المشروع</th><th>المهمة</th><th>المُعيّن</th><th>الاستحقاق</th><th>الحالة</th><th style="text-align: center;">الإجراءات</th></tr></thead><tbody>
                @foreach($projectTasks as $t)
                <tr><td>{{ $t->project?->name_ar }}</td><td>{{ $t->name_ar }}</td><td>{{ $t->assignee?->name ?? '-' }}</td><td>{{ $t->due_date?->format('Y-m-d') ?? '-' }}</td><td>{{ $t->status === 'done' ? 'منتهية' : ($t->status === 'in_progress' ? 'جاري' : 'لم تبدأ') }}</td><td style="text-align: center;"><form method="POST" action="{{ route('wesal.programs-projects.project-tasks.destroy', $t) }}" style="display: inline;" onsubmit="return confirm('حذف المهمة؟');">@csrf @method('DELETE')<button type="submit" class="btn btn-danger" style="padding: 0.4rem 0.8rem;"><i class="fas fa-trash"></i></button></form></td></tr>
                @endforeach
            </tbody></table>
        </div>
        {{ $projectTasks->links() }}
    @else
        <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد مهام.</p>
    @endif
</div>
