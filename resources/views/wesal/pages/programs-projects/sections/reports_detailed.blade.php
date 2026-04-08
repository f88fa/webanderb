@php $p = $reportProject ?? null; @endphp
@if(!$p)
    <div class="content-card"><p class="alert alert-error">المشروع غير موجود.</p><a href="{{ route('wesal.programs-projects.show', ['section' => 'reports']) }}" class="btn btn-secondary">العودة للتقارير</a></div>
@else
<div class="content-card report-detailed">
    <div class="page-header" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
        <div>
            <h1 class="page-title"><i class="fas fa-list-alt" style="color: var(--primary-color); margin-left: 0.5rem;"></i> تقرير تفصيلي — {{ $p->name_ar }}</h1>
            <p class="page-subtitle">رقم المشروع: {{ $p->project_no }} — {{ $p->donor?->name_ar ?? '' }}</p>
        </div>
        <div class="no-print" style="display: flex; gap: 0.5rem;">
            <a href="{{ route('wesal.programs-projects.show', ['section' => 'reports']) }}" class="btn btn-secondary"><i class="fas fa-arrow-right"></i> تغيير التقرير</a>
            <button type="button" onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> طباعة</button>
        </div>
    </div>

    <div class="report-section">
        <h2 class="report-section-title">1. بيانات المشروع</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 0.75rem;">
            <div><span class="report-label">رقم المشروع</span><span class="report-value">{{ $p->project_no }}</span></div>
            <div><span class="report-label">الاسم</span><span class="report-value">{{ $p->name_ar }}</span></div>
            <div><span class="report-label">الجهة المانحة</span><span class="report-value">{{ $p->donor?->name_ar ?? '—' }}</span></div>
            <div><span class="report-label">تاريخ البداية</span><span class="report-value">{{ $p->start_date?->format('Y-m-d') ?? '—' }}</span></div>
            <div><span class="report-label">تاريخ النهاية</span><span class="report-value">{{ $p->end_date?->format('Y-m-d') ?? '—' }}</span></div>
            <div><span class="report-label">الميزانية</span><span class="report-value" dir="ltr">{{ $p->budget_amount ? number_format($p->budget_amount, 2) : '—' }}</span></div>
            <div><span class="report-label">المنفق</span><span class="report-value" dir="ltr">{{ $p->spent_amount ? number_format($p->spent_amount, 2) : '0.00' }}</span></div>
            <div><span class="report-label">الحالة</span><span class="report-value">{{ $p->status === 'active' ? 'نشط' : ($p->status === 'completed' ? 'مكتمل' : 'مؤرشف') }}</span></div>
        </div>
        @if($p->description)<p style="margin-top: 0.75rem;"><span class="report-label">الوصف</span><br><span class="report-value">{{ $p->description }}</span></p>@endif
    </div>

    <div class="report-section">
        <h2 class="report-section-title">2. المراحل وسجلات التحديث</h2>
        @if($p->stages && $p->stages->count() > 0)
            @foreach($p->stages as $idx => $stage)
            <div class="report-stage-block">
                <h3 class="report-stage-name">{{ $idx + 1 }}. {{ $stage->name_ar }}</h3>
                <p style="color: var(--text-secondary); font-size: 0.9rem;">الفترة: {{ $stage->start_date?->format('Y-m-d') ?? '—' }} — {{ $stage->end_date?->format('Y-m-d') ?? '—' }} | الحالة: {{ $stage->status === 'completed' ? 'مكتملة' : ($stage->status === 'in_progress' ? 'غير مكتملة' : 'معلقة') }}@if($stage->status === 'completed' && $stage->closure_reason) | سبب الإغلاق: {{ Str::limit($stage->closure_reason, 80) }}@endif</p>
                @if($stage->updates && $stage->updates->count() > 0)
                    <table class="report-table">
                        <thead><tr><th>التاريخ</th><th>المسجّل</th><th>العنوان</th><th>التفاصيل</th><th>نسبة الإنجاز</th></tr></thead>
                        <tbody>
                            @foreach($stage->updates as $up)
                            <tr>
                                <td>{{ $up->update_date?->format('Y-m-d') }}</td>
                                <td>{{ $up->updatedByUser?->name ?? '—' }}</td>
                                <td>{{ $up->title ?? '—' }}</td>
                                <td>{{ Str::limit($up->description, 100) }}</td>
                                <td>{{ $up->progress_percentage !== null ? $up->progress_percentage . '%' : '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
            @endforeach
        @else
            <p style="color: var(--text-secondary);">لا توجد مراحل مسجلة.</p>
        @endif
    </div>

    <div class="report-section">
        <h2 class="report-section-title">3. المصروفات</h2>
        @if($p->expenses && $p->expenses->count() > 0)
            <table class="report-table">
                <thead><tr><th>التاريخ</th><th>الوصف</th><th>المبلغ</th></tr></thead>
                <tbody>
                    @foreach($p->expenses as $e)
                    <tr><td>{{ $e->expense_date?->format('Y-m-d') ?? '—' }}</td><td>{{ $e->description ?? '—' }}</td><td dir="ltr">{{ $e->amount ? number_format($e->amount, 2) : '—' }}</td></tr>
                    @endforeach
                </tbody>
            </table>
            <p style="margin-top: 0.5rem;"><strong>الإجمالي:</strong> <span dir="ltr">{{ number_format($p->expenses->sum('amount'), 2) }}</span></p>
        @else
            <p style="color: var(--text-secondary);">لا توجد مصروفات مسجلة.</p>
        @endif
    </div>

    <div class="report-section">
        <h2 class="report-section-title">4. المستندات</h2>
        <p style="color: var(--text-secondary);">عدد المستندات المرفقة بالمشروع: <strong>{{ $p->documents->count() ?? 0 }}</strong></p>
    </div>
</div>
<style>.report-section { margin-bottom: 1.5rem; } .report-section-title { font-size: 1.1rem; color: var(--primary-color); border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 0.75rem; } .report-stage-block { margin-bottom: 1rem; padding: 0.75rem; background: rgba(255,255,255,0.03); border-radius: 8px; } .report-stage-name { font-size: 1rem; margin-bottom: 0.35rem; } .report-table { width: 100%; border-collapse: collapse; font-size: 0.9rem; } .report-table th, .report-table td { padding: 0.5rem; text-align: right; border: 1px solid var(--border-color); } .report-label { color: var(--text-secondary); font-size: 0.85rem; display: block; } .report-value { font-weight: 600; }</style>
@endif
