<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-search" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            طلبات تحت الدراسة
        </h1>
        <p class="page-subtitle">الطلبات قيد المراجعة</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
        @if(isset($requests) && $requests->count() > 0)
            <div class="table-container">
                <table style="direction: rtl;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>المستفيد</th>
                            <th>نوع الطلب</th>
                            <th>الوصف</th>
                            <th>تاريخ الدراسة</th>
                            <th style="text-align: center;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $r)
                        <tr>
                            <td>{{ $r->id }}</td>
                            <td>{{ $r->beneficiary?->name_ar }} ({{ $r->beneficiary?->beneficiary_no }})</td>
                            <td>{{ $r->request_type ?? '-' }}</td>
                            <td>{{ Str::limit($r->description, 40) ?? '-' }}</td>
                            <td>{{ $r->studied_at?->format('Y-m-d H:i') ?? '-' }}</td>
                            <td style="text-align: center;">
                                <form method="POST" action="{{ route('wesal.beneficiaries.requests.approve', $r) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; margin-left: 0.25rem;"><i class="fas fa-check"></i> اعتماد</button>
                                </form>
                                <form method="POST" action="{{ route('wesal.beneficiaries.requests.reject', $r) }}" style="display: inline;" id="rejectForm{{ $r->id }}">
                                    @csrf
                                    <input type="text" name="rejection_reason" placeholder="سبب الرفض (اختياري)" class="form-control" style="display: inline-block; width: 180px; margin-left: 0.25rem; vertical-align: middle;">
                                    <button type="submit" class="btn btn-danger" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;"><i class="fas fa-times"></i> رفض</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد طلبات تحت الدراسة.</p>
        @endif
    </div>
</div>
