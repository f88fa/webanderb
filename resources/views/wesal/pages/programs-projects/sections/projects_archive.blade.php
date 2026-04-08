<div class="content-card">
    <div class="page-header">
        <h1 class="page-title"><i class="fas fa-archive" style="color: var(--primary-color);"></i> أرشيف المشاريع</h1>
    </div>
    @if(session('success'))<div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>@endif
    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color);">
        @if(isset($archivedProjects) && $archivedProjects->count() > 0)
            <div class="table-container">
                <table style="direction: rtl;">
                    <thead><tr><th>رقم المشروع</th><th>الاسم</th><th>الجهة المانحة</th><th style="text-align: center;">الإجراءات</th></tr></thead>
                    <tbody>
                        @foreach($archivedProjects as $p)
                        <tr>
                            <td><strong>{{ $p->project_no }}</strong></td>
                            <td>{{ $p->name_ar }}</td>
                            <td>{{ $p->donor?->name_ar ?? '-' }}</td>
                            <td style="text-align: center;">
                                <form method="POST" action="{{ route('wesal.programs-projects.projects.unarchive', $p) }}" style="display: inline;">@csrf<button type="submit" class="btn btn-primary" style="padding: 0.4rem 0.8rem;"><i class="fas fa-undo"></i> إعادة تفعيل</button></form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد مشاريع في الأرشيف.</p>
        @endif
    </div>
</div>
