<div class="content-card">
    <div class="page-header">
        <h1 class="page-title"><i class="fas fa-puzzle-piece" style="color: var(--primary-color);"></i> المراحل</h1>
    </div>
    @if(session('success'))<div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>@endif
    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color); margin-bottom: 2rem;">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">إضافة مرحلة</h3>
        <form method="POST" action="{{ route('wesal.programs-projects.stages.store') }}" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem; align-items: end;">
            @csrf
            <div><label class="form-label">المشروع <span style="color:#dc3545">*</span></label><select name="project_id" class="form-control" required><option value="">-- اختر --</option>@foreach($projects ?? [] as $pr)<option value="{{ $pr->id }}">{{ $pr->project_no }} - {{ $pr->name_ar }}</option>@endforeach</select></div>
            <div><label class="form-label">اسم المرحلة <span style="color:#dc3545">*</span></label><input type="text" name="name_ar" class="form-control" required></div>
            <div><label class="form-label">التاريخ من</label><input type="date" name="start_date" class="form-control"></div>
            <div><label class="form-label">التاريخ إلى</label><input type="date" name="end_date" class="form-control"></div>
            <div><label class="form-label">الحالة</label><select name="status" class="form-control"><option value="pending">قيد الانتظار</option><option value="in_progress">جاري التنفيذ</option><option value="completed">مكتملة</option></select></div>
            <div><button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة</button></div>
        </form>
    </div>
    <h3 style="color: var(--text-primary); margin-bottom: 1rem;">قائمة المراحل</h3>
    @if(isset($stages) && $stages->count() > 0)
        <div class="table-container">
            <table style="direction: rtl;"><thead><tr><th>المشروع</th><th>المرحلة</th><th>من</th><th>إلى</th><th>الحالة</th><th style="text-align: center;">الإجراءات</th></tr></thead><tbody>
                @foreach($stages as $s)
                <tr><td>{{ $s->project?->name_ar }}</td><td>{{ $s->name_ar }}</td><td>{{ $s->start_date?->format('Y-m-d') ?? '-' }}</td><td>{{ $s->end_date?->format('Y-m-d') ?? '-' }}</td><td>{{ $s->status === 'completed' ? 'مكتملة' : ($s->status === 'in_progress' ? 'جاري التنفيذ' : 'قيد الانتظار') }}</td><td style="text-align: center;"><form method="POST" action="{{ route('wesal.programs-projects.stages.destroy', $s) }}" style="display: inline;" onsubmit="return confirm('حذف المرحلة؟');">@csrf @method('DELETE')<button type="submit" class="btn btn-danger" style="padding: 0.4rem 0.8rem;"><i class="fas fa-trash"></i></button></form></td></tr>
                @endforeach
            </tbody></table>
        </div>
        {{ $stages->links() }}
    @else
        <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد مراحل.</p>
    @endif
</div>
