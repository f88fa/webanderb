<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-project-diagram" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            الهيكل التنظيمي
        </h1>
        <p class="page-subtitle">عرض الهيكل التنظيمي حسب الأقسام</p>
    </div>

    @if(isset($departmentTree) && $departmentTree->count() > 0)
        <div style="padding: 0.5rem 0;">
            @foreach($departmentTree as $dept)
                <div style="margin-bottom: 1rem; padding: 1.25rem; background: rgba(255,255,255,0.05); border-radius: 12px; border: 2px solid var(--border-color); border-right: 4px solid var(--primary-color);">
                    <strong style="color: var(--text-primary);">{{ $dept->name_ar }}</strong>
                    <span style="color: var(--text-secondary); font-size: 0.9rem;">({{ $dept->code }}) — {{ $dept->employees_count ?? 0 }} موظف</span>
                    @if($dept->children->count() > 0)
                        <div style="margin-top: 0.75rem; margin-right: 1.5rem;">
                            @foreach($dept->children as $child)
                                <div style="padding: 0.75rem 1rem; background: rgba(0,0,0,0.15); border-radius: 8px; margin-bottom: 0.5rem; border-right: 3px solid var(--primary-color);">
                                    {{ $child->name_ar }} ({{ $child->code }}) — {{ $child->employees_count ?? 0 }} موظف
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا يوجد هيكل تنظيمي. أضف أقساماً من صفحة <a href="{{ route('wesal.hr.show', ['section' => 'departments']) }}" style="color: var(--primary-color);">الأقسام</a>.</p>
    @endif
</div>
