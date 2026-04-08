<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-wallet" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            رصيد الإجازات
        </h1>
        <p class="page-subtitle">عرض رصيد الإجازات السنوية حسب الموظف ونوع الإجازة</p>
    </div>

    <form method="GET" action="{{ route('wesal.hr.show', ['section' => 'leave', 'sub' => 'balance']) }}" style="margin-bottom: 1.5rem;">
        <label class="form-label">السنة الميلادية</label>
        <select name="year" class="form-control" style="max-width: 150px; display: inline-block;" onchange="this.form.submit()">
            @for($y = now()->year; $y >= now()->year - 5; $y--)
                <option value="{{ $y }}" {{ ($balanceYear ?? now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
    </form>

    @if(isset($employees) && $employees->count() > 0)
        <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color);">
            <div class="table-container">
                <table style="direction: rtl;">
                    <thead><tr><th>الموظف</th><th>نوع الإجازة</th><th style="text-align: center;">الرصيد السنوي</th><th style="text-align: center;">المستخدم</th><th style="text-align: center;">المتبقي</th></tr></thead>
                    <tbody>
                        @foreach($employees as $emp)
                            @if(isset($leaveTypes) && $leaveTypes->count() > 0)
                                @foreach($leaveTypes as $lt)
                                    @php
                                        $lb = $emp->leaveBalances->first(fn($b) => $b->leave_type_id == $lt->id);
                                        $bal = $lb ? (float)$lb->balance : (float)$lt->days_per_year;
                                        $used = $lb ? (float)$lb->used : 0;
                                        $rem = $bal - $used;
                                    @endphp
                                    <tr>
                                        <td>{{ $emp->name_ar }}</td>
                                        <td>{{ $lt->name_ar }}</td>
                                        <td style="text-align: center;">{{ $bal }}</td>
                                        <td style="text-align: center;">{{ $used }}</td>
                                        <td style="text-align: center;">{{ $rem }}</td>
                                    </tr>
                                @endforeach
                            @else
                                @forelse($emp->leaveBalances ?? [] as $lb)
                                    <tr><td>{{ $emp->name_ar }}</td><td>{{ $lb->leaveType->name_ar ?? '-' }}</td><td style="text-align: center;">{{ $lb->balance }}</td><td style="text-align: center;">{{ $lb->used }}</td><td style="text-align: center;">{{ $lb->remaining }}</td></tr>
                                @empty
                                    <tr><td>{{ $emp->name_ar }}</td><td colspan="4" style="color: var(--text-secondary);">لا يوجد رصيد مسجل</td></tr>
                                @endforelse
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا يوجد موظفون أو لا توجد أرصدة.</p>
    @endif
</div>
