<div class="content-card">
    <div class="page-header" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
        <div>
            <h1 class="page-title"><i class="fas fa-folder" style="color: var(--primary-color); margin-left: 0.5rem;"></i> قائمة المشاريع</h1>
            <p class="page-subtitle">عرض وإدارة المشاريع</p>
        </div>
        <a href="{{ route('wesal.programs-projects.show', ['section' => 'projects', 'sub' => 'add']) }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> إضافة مشروع
        </a>
    </div>
    @if(session('success'))<div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>@endif
    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color);">
        @if(isset($projectsList) && $projectsList->count() > 0)
            <div class="table-container">
                <table style="direction: rtl;">
                    <thead><tr><th>رقم المشروع</th><th>الاسم</th><th>الجهة المانحة</th><th>البداية</th><th>النهاية</th><th>الميزانية</th><th>المراحل</th><th style="text-align: center;">الإجراءات</th></tr></thead>
                    <tbody>
                        @foreach($projectsList as $p)
                        <tr>
                            <td><strong>{{ $p->project_no }}</strong></td>
                            <td><a href="{{ route('wesal.programs-projects.show', ['section' => 'project', 'sub' => $p->id]) }}" style="color: var(--primary-color); font-weight: 600;">{{ $p->name_ar }}</a></td>
                            <td>{{ $p->donor?->name_ar ?? '-' }}</td>
                            <td>{{ $p->start_date?->format('Y-m-d') ?? '-' }}</td>
                            <td>{{ $p->end_date?->format('Y-m-d') ?? '-' }}</td>
                            <td>{{ $p->budget_amount ? number_format($p->budget_amount, 2) : '-' }}</td>
                            <td>{{ $p->stages_count ?? 0 }}</td>
                            <td style="text-align: center;">
                                <a href="{{ route('wesal.programs-projects.show', ['section' => 'project', 'sub' => $p->id]) }}" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; margin-left: 0.25rem;"><i class="fas fa-project-diagram"></i> المراحل والتحديثات</a>
                                <a href="{{ route('wesal.programs-projects.show', ['section' => 'edit-project', 'sub' => $p->id]) }}" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; margin-left: 0.25rem;"><i class="fas fa-edit"></i></a>
                                @if($p->status !== 'archived')
                                <form method="POST" action="{{ route('wesal.programs-projects.projects.archive', $p) }}" style="display: inline;" onsubmit="return confirm('أرشفة المشروع؟');">@csrf<button type="submit" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; margin-left: 0.25rem;"><i class="fas fa-archive"></i></button></form>
                                @endif
                                <form method="POST" action="{{ route('wesal.programs-projects.projects.destroy', $p) }}" style="display: inline;" onsubmit="return confirm('حذف المشروع؟');">@csrf @method('DELETE')<button type="submit" class="btn btn-danger" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;"><i class="fas fa-trash"></i></button></form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد مشاريع. <a href="{{ route('wesal.programs-projects.show', ['section' => 'projects', 'sub' => 'add']) }}" style="color: var(--primary-color);">إضافة مشروع</a></p>
        @endif
    </div>
</div>
