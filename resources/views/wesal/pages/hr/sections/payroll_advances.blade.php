<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-hand-holding-usd" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            السلف
        </h1>
        <p class="page-subtitle">إدارة سلف الموظفين</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif

    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">طلب سلفة</h3>
        <form method="POST" action="{{ route('wesal.hr.advances.store') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; align-items: end;">
            @csrf
            <div><label class="form-label">الموظف <span style="color: #ff8a80;">*</span></label><select name="employee_id" class="form-control" required><option value="">-- اختر --</option>@foreach($employees ?? [] as $e)<option value="{{ $e->id }}">{{ $e->name_ar }}</option>@endforeach</select></div>
            <div><label class="form-label">المبلغ <span style="color: #ff8a80;">*</span></label><input type="number" name="amount" class="form-control" step="0.01" min="0.01" required dir="ltr" style="text-align:left;"></div>
            <div><label class="form-label">التاريخ <span style="color: #ff8a80;">*</span></label><input type="date" name="request_date" class="form-control" value="{{ now()->toDateString() }}" required></div>
            <div><label class="form-label">عدد أشهر الخصم</label><input type="number" name="deduct_months" class="form-control" value="1" min="1"></div>
            <div><button type="submit" class="btn btn-primary">إضافة</button></div>
        </form>
    </div>

    @if(isset($advances) && $advances->count() > 0)
        <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color);">
            <div class="table-container">
                <table style="direction: rtl;">
                    <thead><tr><th>الموظف</th><th style="text-align: center;">المبلغ</th><th style="text-align: center;">التاريخ</th><th style="text-align: center;">الحالة</th><th style="text-align: center;">الإجراء</th></tr></thead>
                    <tbody>
                        @foreach($advances as $adv)
                        <tr>
                            <td>{{ $adv->employee->name_ar }}</td>
                            <td style="text-align: center;">{{ number_format($adv->amount, 2) }}</td>
                            <td style="text-align: center;">{{ $adv->request_date->format('Y-m-d') }}</td>
                            <td style="text-align: center;">{{ $adv->status === 'pending' ? 'معلق' : ($adv->status === 'approved' ? 'معتمد' : 'مخصوم') }}</td>
                            <td style="text-align: center;">@if($adv->status === 'pending')<form method="POST" action="{{ route('wesal.hr.advances.approve', $adv) }}" style="display: inline;">@csrf<button type="submit" class="btn btn-success" style="padding: 0.4rem 0.8rem;">موافقة</button></form>@endif</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد سلف.</p>
    @endif
</div>
