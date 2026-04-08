<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-archive" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            الأرشيف
        </h1>
        <p class="page-subtitle">أرشيف المستفيدين المؤرشفين</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
        @if(isset($archivedBeneficiaries) && $archivedBeneficiaries->count() > 0)
            <div class="table-container">
                <table style="direction: rtl;">
                    <thead>
                        <tr>
                            <th>رقم المستفيد</th>
                            <th>الاسم</th>
                            <th>الهوية</th>
                            <th>الجوال</th>
                            <th style="text-align: center;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($archivedBeneficiaries as $b)
                        <tr>
                            <td><strong>{{ $b->beneficiary_no }}</strong></td>
                            <td>{{ $b->name_ar }}</td>
                            <td>{{ $b->national_id ?? '-' }}</td>
                            <td>{{ $b->phone ?? '-' }}</td>
                            <td style="text-align: center;">
                                <form method="POST" action="{{ route('wesal.beneficiaries.beneficiaries.unarchive', $b) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;"><i class="fas fa-undo"></i> إعادة تفعيل</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا يوجد مستفيدون في الأرشيف.</p>
        @endif
    </div>
</div>
