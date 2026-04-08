@php $p = $reportProject ?? null; @endphp
@if(!$p)
    <div class="content-card"><p class="alert alert-error">المشروع غير موجود.</p><a href="{{ route('wesal.programs-projects.show', ['section' => 'reports']) }}" class="btn btn-secondary">العودة للتقارير</a></div>
@else
<div class="content-card">
    <div class="page-header" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
        <div>
            <h1 class="page-title"><i class="fas fa-chart-pie" style="color: var(--primary-color); margin-left: 0.5rem;"></i> تقرير ملخص — {{ $p->name_ar }}</h1>
            <p class="page-subtitle">رقم المشروع: {{ $p->project_no }}</p>
        </div>
        <div class="no-print" style="display: flex; gap: 0.5rem;">
            <a href="{{ route('wesal.programs-projects.show', ['section' => 'reports']) }}" class="btn btn-secondary"><i class="fas fa-arrow-right"></i> تغيير التقرير</a>
            <button type="button" onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> طباعة</button>
        </div>
    </div>

    <div class="report-summary-boxes" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div class="report-box"><span class="report-box-label">الجهة المانحة</span><span class="report-box-value">{{ $p->donor?->name_ar ?? '—' }}</span></div>
        <div class="report-box"><span class="report-box-label">تاريخ البداية</span><span class="report-box-value">{{ $p->start_date?->format('Y-m-d') ?? '—' }}</span></div>
        <div class="report-box"><span class="report-box-label">تاريخ النهاية</span><span class="report-box-value">{{ $p->end_date?->format('Y-m-d') ?? '—' }}</span></div>
        <div class="report-box"><span class="report-box-label">الميزانية</span><span class="report-box-value" dir="ltr">{{ $p->budget_amount ? number_format($p->budget_amount, 2) : '—' }}</span></div>
        <div class="report-box"><span class="report-box-label">المنفق</span><span class="report-box-value" dir="ltr">{{ $p->spent_amount ? number_format($p->spent_amount, 2) : '0.00' }}</span></div>
        <div class="report-box"><span class="report-box-label">عدد المراحل</span><span class="report-box-value">{{ $p->stages_count ?? $p->stages->count() ?? 0 }}</span></div>
        <div class="report-box"><span class="report-box-label">عدد المهام</span><span class="report-box-value">{{ $p->tasks_count ?? $p->tasks->count() ?? 0 }}</span></div>
        <div class="report-box"><span class="report-box-label">الحالة</span><span class="report-box-value">{{ $p->status === 'active' ? 'نشط' : ($p->status === 'completed' ? 'مكتمل' : 'مؤرشف') }}</span></div>
    </div>

    @php
        $completedStages = $p->stages->where('status', 'completed')->count();
        $totalStages = $p->stages->count();
    @endphp
    <div style="background: rgba(255,255,255,0.05); padding: 1rem 1.5rem; border-radius: 12px; border: 1px solid var(--border-color);">
        <h3 style="color: var(--primary-color); margin-bottom: 0.75rem;">المراحل</h3>
        <p style="margin: 0;">مراحل مكتملة: <strong>{{ $completedStages }}</strong> من أصل <strong>{{ $totalStages }}</strong>@if($totalStages > 0) — نسبة الإنجاز: <strong>{{ round($totalStages ? ($completedStages / $totalStages) * 100 : 0) }}%</strong>@endif</p>
    </div>
</div>
<style>
.report-box { background: rgba(255,255,255,0.06); padding: 1rem; border-radius: 10px; border: 1px solid var(--border-color); }
.report-box-label { display: block; color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 0.25rem; }
.report-box-value { font-weight: 700; font-size: 1rem; }
</style>
@endif
