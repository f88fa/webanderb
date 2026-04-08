<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-clipboard-check" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            التقييم
        </h1>
        <p class="page-subtitle">تقييم أداء الموظفين</p>
    </div>
    @if(isset($reviews) && $reviews->count() > 0)
        <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color);">
            <div class="table-container">
                <table style="direction: rtl;">
                    <thead><tr><th>الموظف</th><th style="text-align: center;">السنة</th><th style="text-align: center;">الفترة</th><th style="text-align: center;">التقييم</th></tr></thead>
                    <tbody>@foreach($reviews as $r)<tr><td>{{ $r->employee->name_ar }}</td><td style="text-align: center;">{{ $r->year }}</td><td style="text-align: center;">{{ $r->period }}</td><td style="text-align: center;">{{ $r->rating ?? '-' }}</td></tr>@endforeach</tbody>
                </table>
            </div>
        </div>
    @else
        <div style="background: rgba(255,255,255,0.05); padding: 2rem; border-radius: 12px; border: 2px solid var(--border-color);">
            <p style="text-align: center; color: var(--text-secondary); margin: 0;">لا توجد تقييمات. يمكن إضافة نموذج تقييم لاحقاً.</p>
        </div>
    @endif
</div>
