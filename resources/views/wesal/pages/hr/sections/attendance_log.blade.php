<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-calendar-day" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            السجل اليومي
        </h1>
        <p class="page-subtitle">سجل الحضور حسب التاريخ</p>
    </div>

    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
        <form method="GET" action="{{ route('wesal.hr.show', ['section' => 'attendance', 'sub' => 'log']) }}" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
            <label class="form-label" style="margin: 0;">التاريخ</label>
            <input type="date" name="date" class="form-control" value="{{ $date ?? now()->toDateString() }}" style="width: auto; min-width: 160px;">
            <button type="submit" class="btn btn-primary">عرض</button>
            <a href="{{ route('wesal.hr.attendance.log.export', ['date' => $date ?? now()->toDateString()]) }}" class="btn" style="background: #2e7d32; color: white;">
                <i class="fas fa-file-excel"></i> تصدير Excel
            </a>
        </form>
    </div>

    @if(isset($records) && $records->count() > 0)
        <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color);">
            <div class="table-container">
                <table style="direction: rtl;">
                    <thead><tr><th>الموظف</th><th style="text-align: center;">وقت الحضور</th><th style="text-align: center;">وقت الانصراف</th><th style="text-align: center;">مدة الحضور</th><th style="text-align: center;">الوردية</th></tr></thead>
                    <tbody>
                        @foreach($records as $r)
                        @php
                            $ci = $r->check_in ? \Carbon\Carbon::parse($r->date->format('Y-m-d') . ' ' . $r->check_in) : null;
                            $co = $r->check_out ? \Carbon\Carbon::parse($r->date->format('Y-m-d') . ' ' . $r->check_out) : null;
                            $dur = ($ci && $co) ? $ci->diffInMinutes($co) : null;
                            $durFormatted = $dur !== null ? (floor($dur/60) > 0 ? floor($dur/60) . ' س ' : '') . ($dur%60) . ' د' : '-';
                        @endphp
                        <tr>
                            <td>{{ $r->employee->name_ar }}</td>
                            <td style="text-align: center;">{{ $r->check_in ? $ci->format('H:i') : '-' }}</td>
                            <td style="text-align: center;">{{ $r->check_out ? $co->format('H:i') : '-' }}</td>
                            <td style="text-align: center;">{{ $durFormatted }}</td>
                            <td style="text-align: center;">{{ $r->shift?->name_ar ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد تسجيلات لهذا التاريخ.</p>
    @endif
</div>
