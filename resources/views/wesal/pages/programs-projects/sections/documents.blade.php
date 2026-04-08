<div class="content-card">
    <div class="page-header">
        <h1 class="page-title"><i class="fas fa-folder-open" style="color: var(--primary-color);"></i> المستندات</h1>
    </div>
    @if(session('success'))<div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>@endif
    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color); margin-bottom: 2rem;">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">إضافة مستند</h3>
        <form method="POST" action="{{ route('wesal.programs-projects.project-documents.store') }}" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem; align-items: end;">
            @csrf
            <div><label class="form-label">المشروع <span style="color:#dc3545">*</span></label><select name="project_id" class="form-control" required><option value="">-- اختر --</option>@foreach($projects ?? [] as $pr)<option value="{{ $pr->id }}">{{ $pr->project_no }} - {{ $pr->name_ar }}</option>@endforeach</select></div>
            <div><label class="form-label">العنوان <span style="color:#dc3545">*</span></label><input type="text" name="title" class="form-control" required></div>
            <div><label class="form-label">نوع المستند</label><input type="text" name="document_type" class="form-control" placeholder="عقد، تقرير"></div>
            <div><label class="form-label">التاريخ</label><input type="date" name="document_date" class="form-control"></div>
            <div><label class="form-label">ملاحظات</label><input type="text" name="notes" class="form-control"></div>
            <div><button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة</button></div>
        </form>
    </div>
    <h3 style="color: var(--text-primary); margin-bottom: 1rem;">قائمة المستندات</h3>
    @if(isset($projectDocuments) && $projectDocuments->count() > 0)
        <div class="table-container">
            <table style="direction: rtl;"><thead><tr><th>المشروع</th><th>العنوان</th><th>النوع</th><th>التاريخ</th><th style="text-align: center;">الإجراءات</th></tr></thead><tbody>
                @foreach($projectDocuments as $d)
                <tr><td>{{ $d->project?->name_ar }}</td><td>{{ $d->title }}</td><td>{{ $d->document_type ?? '-' }}</td><td>{{ $d->document_date?->format('Y-m-d') ?? '-' }}</td><td style="text-align: center;"><form method="POST" action="{{ route('wesal.programs-projects.project-documents.destroy', $d) }}" style="display: inline;" onsubmit="return confirm('حذف المستند؟');">@csrf @method('DELETE')<button type="submit" class="btn btn-danger" style="padding: 0.4rem 0.8rem;"><i class="fas fa-trash"></i></button></form></td></tr>
                @endforeach
            </tbody></table>
        </div>
        {{ $projectDocuments->links() }}
    @else
        <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد مستندات.</p>
    @endif
</div>
