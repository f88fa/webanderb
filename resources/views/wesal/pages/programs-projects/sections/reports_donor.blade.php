@php
    $p = $reportProject ?? null;
    $settings = $settings ?? \App\Models\SiteSetting::getAllAsArray();
    $orgName = $settings['organization_name_ar'] ?? $settings['site_name'] ?? 'المؤسسة';
@endphp
@if(!$p)
    <div class="content-card"><p class="alert alert-error">المشروع غير موجود.</p><a href="{{ route('wesal.programs-projects.show', ['section' => 'reports']) }}" class="btn btn-secondary">العودة للتقارير</a></div>
@else
<div class="donor-report" id="donor-report">
    <div class="donor-report-actions no-print" style="margin-bottom: 1rem; display: flex; gap: 0.5rem;">
        <a href="{{ route('wesal.programs-projects.show', ['section' => 'reports']) }}" class="btn btn-secondary"><i class="fas fa-arrow-right"></i> تغيير التقرير</a>
        <button type="button" onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> طباعة / حفظ PDF</button>
    </div>

    <div class="donor-report-paper">
        {{-- غلاف التقرير --}}
        <div class="donor-report-cover">
            <div class="donor-report-cover-org">{{ $orgName }}</div>
            <h1 class="donor-report-cover-title">تقرير المشروع</h1>
            <h2 class="donor-report-cover-project">{{ $p->name_ar }}</h2>
            <p class="donor-report-cover-meta">رقم المشروع: {{ $p->project_no }}@if($p->donor) — الجهة المانحة: {{ $p->donor->name_ar }}@endif</p>
            <p class="donor-report-cover-date">تاريخ التقرير: {{ now()->format('Y-m-d') }}</p>
        </div>

        {{-- ملخص تنفيذي --}}
        <div class="donor-report-section">
            <h2 class="donor-report-h2">الملخص التنفيذي</h2>
            <table class="donor-report-summary-table">
                <tr><td class="donor-td-label">اسم المشروع</td><td class="donor-td-value">{{ $p->name_ar }}</td></tr>
                <tr><td class="donor-td-label">رقم المشروع</td><td class="donor-td-value">{{ $p->project_no }}</td></tr>
                <tr><td class="donor-td-label">الجهة المانحة</td><td class="donor-td-value">{{ $p->donor?->name_ar ?? '—' }}</td></tr>
                <tr><td class="donor-td-label">فترة التنفيذ</td><td class="donor-td-value">{{ $p->start_date?->format('Y-m-d') ?? '—' }} — {{ $p->end_date?->format('Y-m-d') ?? '—' }}</td></tr>
                <tr><td class="donor-td-label">الميزانية المعتمدة</td><td class="donor-td-value" dir="ltr">{{ $p->budget_amount ? number_format($p->budget_amount, 2) : '—' }}</td></tr>
                <tr><td class="donor-td-label">المصروف الفعلي</td><td class="donor-td-value" dir="ltr">{{ $p->spent_amount ? number_format($p->spent_amount, 2) : '0.00' }}</td></tr>
                <tr><td class="donor-td-label">عدد المراحل</td><td class="donor-td-value">{{ $p->stages->count() }} (مكتملة: {{ $p->stages->where('status', 'completed')->count() }})</td></tr>
            </table>
        </div>

        {{-- وصف المشروع --}}
        @if($p->description)
        <div class="donor-report-section">
            <h2 class="donor-report-h2">وصف المشروع</h2>
            <p class="donor-report-p">{{ $p->description }}</p>
        </div>
        @endif

        {{-- مراحل المشروع --}}
        <div class="donor-report-section">
            <h2 class="donor-report-h2">مراحل المشروع وحالة التنفيذ</h2>
            @if($p->stages && $p->stages->count() > 0)
                <table class="donor-report-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>المرحلة</th>
                            <th>من</th>
                            <th>إلى</th>
                            <th>الحالة</th>
                            <th>ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($p->stages as $idx => $stage)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td><strong>{{ $stage->name_ar }}</strong></td>
                            <td>{{ $stage->start_date?->format('Y-m-d') ?? '—' }}</td>
                            <td>{{ $stage->end_date?->format('Y-m-d') ?? '—' }}</td>
                            <td>{{ $stage->status === 'completed' ? 'مكتملة' : ($stage->status === 'in_progress' ? 'غير مكتملة' : 'معلقة') }}</td>
                            <td>@if($stage->status === 'completed' && $stage->closure_reason){{ Str::limit($stage->closure_reason, 60) }}@else — @endif</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="donor-report-p">لم يتم تسجيل مراحل للمشروع بعد.</p>
            @endif
        </div>

        {{-- ملخص سجلات التحديث --}}
        @php $totalUpdates = $p->stages->sum(fn($s) => $s->updates->count()); @endphp
        @if($totalUpdates > 0)
        <div class="donor-report-section">
            <h2 class="donor-report-h2">سجلات التحديث والمتابعة</h2>
            <p class="donor-report-p">تم تسجيل <strong>{{ $totalUpdates }}</strong> تحديثاً خلال فترة تنفيذ المشروع، موزعة على مراحل المشروع. تفاصيل كل تحديث (التاريخ، المسؤول، الوصف، نسبة الإنجاز) متوفرة في النظام.</p>
        </div>
        @endif

        {{-- الجانب المالي --}}
        <div class="donor-report-section">
            <h2 class="donor-report-h2">الملخص المالي</h2>
            <table class="donor-report-summary-table">
                <tr><td class="donor-td-label">الميزانية المعتمدة</td><td class="donor-td-value" dir="ltr">{{ $p->budget_amount ? number_format($p->budget_amount, 2) : '—' }}</td></tr>
                <tr><td class="donor-td-label">إجمالي المصروفات المسجلة</td><td class="donor-td-value" dir="ltr">{{ $p->spent_amount ? number_format($p->spent_amount, 2) : '0.00' }}</td></tr>
            </table>
        </div>

        {{-- ختام --}}
        <div class="donor-report-section donor-report-footer">
            <p class="donor-report-p">تم إعداد هذا التقرير من نظام إدارة المشاريع. للاستفسار أو طلب تفاصيل إضافية يرجى التواصل مع {{ $orgName }}.</p>
            <p class="donor-report-p" style="margin-top: 1rem;">تاريخ الإصدار: {{ now()->format('Y-m-d') }}</p>
        </div>
    </div>
</div>

<style>
.donor-report-paper { max-width: 210mm; margin: 0 auto; padding: 2rem; background: #fff; color: #1a1a1a; border-radius: 8px; box-shadow: 0 2px 12px rgba(0,0,0,0.1); }
.donor-report-cover { text-align: center; padding: 2rem 0; border-bottom: 2px solid #2d5a45; margin-bottom: 2rem; }
.donor-report-cover-org { font-size: 0.95rem; color: #555; margin-bottom: 0.5rem; }
.donor-report-cover-title { font-size: 1.5rem; color: #2d5a45; margin: 0.5rem 0; }
.donor-report-cover-project { font-size: 1.25rem; color: #1a1a1a; margin: 0.5rem 0; }
.donor-report-cover-meta { font-size: 0.95rem; color: #555; margin: 0.5rem 0; }
.donor-report-cover-date { font-size: 0.9rem; color: #777; margin-top: 1rem; }
.donor-report-section { margin-bottom: 1.5rem; }
.donor-report-h2 { font-size: 1.1rem; color: #2d5a45; border-bottom: 1px solid #ddd; padding-bottom: 0.4rem; margin-bottom: 0.75rem; }
.donor-report-p { line-height: 1.6; margin: 0.5rem 0; color: #333; }
.donor-report-summary-table { width: 100%; border-collapse: collapse; margin: 0.5rem 0; }
.donor-report-summary-table td { padding: 0.5rem 0.75rem; border-bottom: 1px solid #eee; }
.donor-td-label { color: #555; width: 40%; }
.donor-td-value { font-weight: 600; color: #1a1a1a; }
.donor-report-table { width: 100%; border-collapse: collapse; font-size: 0.9rem; margin: 0.5rem 0; }
.donor-report-table th, .donor-report-table td { padding: 0.5rem 0.75rem; text-align: right; border: 1px solid #ddd; }
.donor-report-table th { background: #f5f5f5; color: #2d5a45; font-weight: 600; }
.donor-report-footer { margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #ddd; font-size: 0.9rem; color: #666; }
@media print {
    .no-print { display: none !important; }
    .donor-report-paper { box-shadow: none; padding: 1rem; }
    .donor-report-cover { border-bottom-color: #2d5a45; }
}
</style>
@endif
