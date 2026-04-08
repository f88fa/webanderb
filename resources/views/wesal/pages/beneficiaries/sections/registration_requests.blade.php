<div class="content-card">
    <div class="page-header" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
        <div>
            <h1 class="page-title">
                <i class="fas fa-user-plus" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
                طلبات التسجيل من بوابة المستفيدين
            </h1>
            <p class="page-subtitle">طلبات التسجيل الذاتي — افتح التفاصيل أولاً ثم اعتمد أو ارفض من صفحة الطلب</p>
        </div>
        <a href="{{ url('/beneficiary-portal') }}" target="_blank" class="btn btn-secondary">
            <i class="fas fa-external-link-alt"></i>
            <span>فتح بوابة المستفيدين</span>
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <div style="background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; border: 2px solid var(--border-color);">
        <p style="margin: 0; color: var(--text-secondary);">
            <i class="fas fa-info-circle"></i>
            رابط بوابة المستفيدين: <strong><a href="{{ url('/beneficiary-portal') }}" target="_blank" style="color: var(--primary-color);">{{ url('/beneficiary-portal') }}</a></strong>
            — يمكن مشاركته مع المستفيدين للتسجيل أو تسجيل الدخول.
        </p>
    </div>

    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
        @if(isset($registrationRequests) && $registrationRequests->count() > 0)
            <div class="table-container">
                <table style="direction: rtl;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>البريد</th>
                            <th>الجوال</th>
                            <th>تاريخ التقديم</th>
                            <th style="text-align: center;">إجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($registrationRequests as $r)
                        <tr>
                            <td>{{ $r->id }}</td>
                            <td>{{ $r->name_ar }}</td>
                            <td>{{ $r->email }}</td>
                            <td>{{ $r->phone ?? '-' }}</td>
                            <td>{{ $r->created_at->format('Y-m-d H:i') }}</td>
                            <td style="text-align: center;">
                                <a href="{{ route('wesal.beneficiaries.registration-requests.show', ['registration_request' => $r->id]) }}" class="btn btn-primary" style="padding: 0.45rem 0.9rem; font-size: 0.9rem;">
                                    <i class="fas fa-eye"></i> عرض التفاصيل والقرار
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد طلبات تسجيل قيد الانتظار.</p>
        @endif
    </div>
</div>
