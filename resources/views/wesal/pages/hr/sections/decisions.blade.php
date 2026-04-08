<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-gavel" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            القرارات
        </h1>
        <p class="page-subtitle">قرارات التعيين والنقل وإنهاء الخدمة</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif

    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">إضافة قرار</h3>
        <form method="POST" action="{{ route('wesal.hr.decisions.store') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; align-items: end;">
            @csrf
            <div><label class="form-label">الموظف <span style="color: #ff8a80;">*</span></label><select name="employee_id" class="form-control" required><option value="">-- اختر --</option>@foreach($employees ?? [] as $e)<option value="{{ $e->id }}">{{ $e->name_ar }}</option>@endforeach</select></div>
            <div><label class="form-label">نوع القرار <span style="color: #ff8a80;">*</span></label><input type="text" name="decision_type" class="form-control" placeholder="تعيين / نقل / إنهاء" required></div>
            <div><label class="form-label">تاريخ القرار <span style="color: #ff8a80;">*</span></label><input type="date" name="decision_date" class="form-control" required></div>
            <div><label class="form-label">المرجع</label><input type="text" name="reference" class="form-control"></div>
            <div><button type="submit" class="btn btn-primary">إضافة</button></div>
        </form>
    </div>

    @if(isset($decisions) && $decisions->count() > 0)
        <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color);">
            <div class="table-container">
                <table style="direction: rtl;">
                    <thead><tr><th>الموظف</th><th>النوع</th><th style="text-align: center;">التاريخ</th><th style="text-align: center;">الإجراء</th></tr></thead>
                    <tbody>@foreach($decisions as $d)<tr><td>{{ $d->employee->name_ar }}</td><td>{{ $d->decision_type }}</td><td style="text-align: center;">{{ $d->decision_date->format('Y-m-d') }}</td><td style="text-align: center;"><form method="POST" action="{{ route('wesal.hr.decisions.destroy', $d) }}" style="display: inline;" onsubmit="return confirm('حذف؟');">@csrf @method('DELETE')<button type="submit" class="btn btn-danger" style="padding: 0.4rem 0.8rem;"><i class="fas fa-trash"></i></button></form></td></tr>@endforeach</tbody>
                </table>
            </div>
        </div>
    @else
        <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد قرارات.</p>
    @endif
</div>
