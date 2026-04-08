<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-calculator" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            مسير الرواتب
        </h1>
        <p class="page-subtitle">إدارة مسير الرواتب الشهرية</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif

    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">إنشاء مسير راتب جديد</h3>
        <form method="POST" action="{{ route('wesal.hr.payroll.run') }}" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
            @csrf
            <div><label class="form-label">الشهر</label><select name="month" class="form-control" required style="width: auto; min-width: 100px;">@for($m=1;$m<=12;$m++)<option value="{{ $m }}">{{ $m }}</option>@endfor</select></div>
            <div><label class="form-label">السنة</label><input type="number" name="year" class="form-control" value="{{ now()->year }}" min="2020" required style="width: 120px;"></div>
            <div><button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> إنشاء مسير</button></div>
        </form>
    </div>

    @if(isset($payrollRuns) && $payrollRuns->count() > 0)
        <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color);">
            <div class="table-container">
                <table style="direction: rtl;">
                    <thead><tr><th>الشهر/السنة</th><th style="text-align: center;">الحالة</th></tr></thead>
                    <tbody>@foreach($payrollRuns as $pr)<tr><td>{{ $pr->year }}-{{ str_pad($pr->month, 2, '0', STR_PAD_LEFT) }}</td><td style="text-align: center;">{{ $pr->status === 'draft' ? 'مسودة' : ($pr->status === 'paid' ? 'مدفوع' : 'معتمد') }}</td></tr>@endforeach</tbody>
                </table>
            </div>
        </div>
    @else
        <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا يوجد مسير رواتب.</p>
    @endif
</div>
