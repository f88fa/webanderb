<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-business-time" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            الورديات
        </h1>
        <p class="page-subtitle">إدارة الورديات</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif

    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">إضافة وردية</h3>
        <form method="POST" action="{{ route('wesal.hr.shifts.store') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem; align-items: end;">
            @csrf
            <div><label class="form-label">الاسم <span style="color: #ff8a80;">*</span></label><input type="text" name="name_ar" class="form-control" required></div>
            <div><label class="form-label">بداية <span style="color: #ff8a80;">*</span></label><input type="time" name="start_time" class="form-control" required></div>
            <div><label class="form-label">نهاية <span style="color: #ff8a80;">*</span></label><input type="time" name="end_time" class="form-control" required></div>
            <div><label class="form-label">استراحة (د)</label><input type="number" name="break_minutes" class="form-control" value="0" min="0"></div>
            <div><button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة</button></div>
        </form>
    </div>

    @if(isset($shifts) && $shifts->count() > 0)
        <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color);">
            <div class="table-container">
                <table style="direction: rtl;">
                    <thead><tr><th>الاسم</th><th style="text-align: center;">من</th><th style="text-align: center;">إلى</th><th style="text-align: center;">الإجراءات</th></tr></thead>
                    <tbody>
                        @foreach($shifts as $s)
                        <tr>
                            <td>{{ $s->name_ar }}</td>
                            <td style="text-align: center;">{{ \Carbon\Carbon::parse($s->start_time)->format('H:i') }}</td>
                            <td style="text-align: center;">{{ \Carbon\Carbon::parse($s->end_time)->format('H:i') }}</td>
                            <td style="text-align: center;">
                                <form method="POST" action="{{ route('wesal.hr.shifts.destroy', $s) }}" style="display: inline;" onsubmit="return confirm('حذف الوردية؟');">@csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد ورديات.</p>
    @endif
</div>
