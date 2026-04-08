@php $pr = $paymentRequest ?? null; @endphp
<div class="content-card">
    <div class="page-header" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
        <div>
            <h1 class="page-title">
                <i class="fas fa-users" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
                تفاصيل المستفيدين — طلب الصرف {{ $pr->request_no ?? '' }}
            </h1>
            <p class="page-subtitle">قائمة المستفيدين المرتبطين بهذا الطلب (دعم جماعي)</p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('wesal.finance.payment-requests.index') }}" class="btn btn-secondary">العودة لطلبات الصرف</a>
            @if($pr->journalEntry)
                <a href="{{ route('wesal.finance.journal-entries.print', $pr->journalEntry) }}" target="_blank" rel="noopener" class="btn btn-primary"><i class="fas fa-print"></i> طباعة السند</a>
            @endif
        </div>
    </div>

    @if($pr)
        <div style="background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border: 1px solid var(--border-color);">
            <p style="margin: 0 0 0.5rem 0;"><strong>المستفيد (الوصف):</strong> {{ $pr->displayBeneficiaryName() }}</p>
            <p style="margin: 0 0 0.5rem 0;"><strong>التاريخ:</strong> {{ $pr->request_date?->format('Y-m-d') }} | <strong>المبلغ الإجمالي:</strong> <span dir="ltr" style="text-align: left;">{{ number_format($pr->amount, 2) }}</span></p>
            <p style="margin: 0; color: var(--text-secondary); font-size: 0.9rem;">{{ $pr->description }}</p>
        </div>

        @if($pr->beneficiaryServices && $pr->beneficiaryServices->count() > 0)
            <div class="table-container">
                <table style="direction: rtl; width: 100%;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>رقم المستفيد</th>
                            <th>الاسم</th>
                            <th>المبلغ</th>
                            <th>ملف المستفيد</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pr->beneficiaryServices as $idx => $bs)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>{{ $bs->beneficiary?->beneficiary_no }}</td>
                            <td>{{ $bs->beneficiary?->displayNameForPortal() }}</td>
                            <td dir="ltr" style="text-align: left;">{{ $bs->amount ? number_format($bs->amount, 2) : '—' }}</td>
                            <td>
                                @if($bs->beneficiary_id)
                                    <a href="{{ route('wesal.beneficiaries.show', ['section' => 'profile', 'sub' => $bs->beneficiary_id]) }}" class="btn btn-secondary" style="padding: 0.35rem 0.7rem; font-size: 0.85rem;">ملف المستفيد</a>
                                @else — @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p style="margin-top: 1rem; color: var(--text-secondary);">الإجمالي: {{ $pr->beneficiaryServices->count() }} مستفيد.</p>
        @else
            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد سجلات مستفيدين مرتبطة بهذا الطلب.</p>
        @endif
    @else
        <p style="color: var(--text-secondary);">طلب الصرف غير موجود.</p>
    @endif
</div>
