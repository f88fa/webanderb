<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-chart-line" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            التقارير
        </h1>
        <p class="page-subtitle">تقارير المستفيدين والخدمات والدعم المالي</p>
    </div>
    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color); margin-bottom: 2rem;">
        <h3 style="color: var(--primary-color); margin-bottom: 1rem;">ملخص المستفيدين والدعم</h3>
        @if(isset($beneficiariesForReport) && $beneficiariesForReport->count() > 0)
            <div class="table-container">
                <table style="direction: rtl; width: 100%;">
                    <thead>
                        <tr>
                            <th>رقم المستفيد</th>
                            <th>الاسم</th>
                            <th>عدد الخدمات/المساعدات</th>
                            <th>إجمالي مبالغ الدعم</th>
                            <th>عدد الدعم المالي (مرتبط بطلب صرف)</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($beneficiariesForReport as $b)
                        <tr>
                            <td><strong>{{ $b->beneficiary_no }}</strong></td>
                            <td>{{ $b->name_ar }}</td>
                            <td>{{ $b->service_records_count ?? 0 }}</td>
                            <td dir="ltr" style="text-align: left;">{{ isset($b->total_support_amount) && $b->total_support_amount ? number_format($b->total_support_amount, 2) : '0.00' }}</td>
                            <td>{{ $b->financial_services_count ?? 0 }}</td>
                            <td><a href="{{ route('wesal.beneficiaries.show', ['section' => 'profile', 'sub' => $b->id]) }}" class="btn btn-secondary" style="padding: 0.35rem 0.7rem; font-size: 0.85rem;">ملف المستفيد</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="color: var(--text-secondary); margin: 0;">لا يوجد مستفيدون لعرض التقرير.</p>
        @endif
    </div>
</div>
