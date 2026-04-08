<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-check-circle" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            الطلبات المعتمدة
        </h1>
        <p class="page-subtitle">الطلبات التي تم اعتمادها</p>
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
                            <th>تاريخ الاعتماد</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $r)
                        <tr>
                            <td>{{ $r->id }}</td>
                            <td>{{ $r->beneficiary?->name_ar }} ({{ $r->beneficiary?->beneficiary_no }})</td>
                            <td>{{ $r->request_type ?? '-' }}</td>
                            <td>{{ Str::limit($r->description, 50) ?? '-' }}</td>
                            <td>{{ $r->approved_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد طلبات معتمدة.</p>
        @endif
    </div>
</div>
