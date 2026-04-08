<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-check-double" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            موافقات الإجازات
        </h1>
        <p class="page-subtitle">الموافقة أو الرفض على طلبات الإجازة</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif

    @if(isset($pendingRequests) && $pendingRequests->count() > 0)
        <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color);">
            <div class="table-container">
                <table style="direction: rtl;">
                    <thead><tr><th>الموظف</th><th>نوع الإجازة</th><th style="text-align: center;">من</th><th style="text-align: center;">إلى</th><th style="text-align: center;">الأيام</th><th style="text-align: center;">الإجراء</th></tr></thead>
                    <tbody>
                        @foreach($pendingRequests as $req)
                        <tr style="cursor: pointer;" onclick="window.location='{{ route('wesal.hr.leave.show', $req) }}'">
                            <td><a href="{{ route('wesal.hr.leave.show', $req) }}" style="color: inherit; text-decoration: none;">{{ $req->employee->name_ar }}</a></td>
                            <td>{{ $req->leaveType->name_ar }}</td>
                            <td style="text-align: center;">{{ $req->start_date->format('Y-m-d') }}</td>
                            <td style="text-align: center;">{{ $req->end_date->format('Y-m-d') }}</td>
                            <td style="text-align: center;">{{ $req->days }}</td>
                            <td style="text-align: center;" onclick="event.stopPropagation();">
                                @can('hr.leave.approve')
                                <form method="POST" action="{{ route('wesal.hr.leave.approve', $req) }}" style="display: inline;">@csrf<button type="submit" class="btn btn-success" style="padding: 0.4rem 0.8rem;"><i class="fas fa-check"></i></button></form>
                                <form method="POST" action="{{ route('wesal.hr.leave.reject', $req) }}" style="display: inline;" onsubmit="return confirm('رفض الطلب؟');">@csrf<input type="text" name="rejection_reason" class="form-control" placeholder="سبب الرفض (اختياري)" style="width: 140px; display: inline-block; padding: 0.4rem; margin-left: 0.25rem;"><button type="submit" class="btn btn-danger" style="padding: 0.4rem 0.8rem;"><i class="fas fa-times"></i></button></form>
                                @else
                                <span style="color: var(--text-secondary); font-size: 0.85rem;">—</span>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد طلبات إجازة معلقة.</p>
    @endif
</div>
