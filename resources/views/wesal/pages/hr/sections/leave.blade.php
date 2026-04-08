<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-paper-plane" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            طلب إجازة
        </h1>
        <p class="page-subtitle">تقديم طلب إجازة</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> @foreach($errors->all() as $e)<p style="margin: 0;">{{ $e }}</p>@endforeach</div>
    @endif

    @if(!empty($approvalSequence) && $approvalSequence->isNotEmpty())
        <div style="background: rgba(95, 179, 142, 0.12); padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1.5rem; border: 1px solid var(--primary-color);">
            <h4 style="color: var(--primary-color); margin: 0 0 0.75rem 0; font-size: 0.95rem;"><i class="fas fa-list-ol" style="margin-left: 0.35rem;"></i> تسلسل الموافقات لطلب الإجازة</h4>
            <ol style="margin: 0; padding-right: 1.5rem; color: var(--text-primary); font-size: 0.9rem;">
                @foreach($approvalSequence as $step)
                    <li style="margin-bottom: 0.25rem;">{{ $step->approver_display }}</li>
                @endforeach
            </ol>
        </div>
    @else
        <div style="background: rgba(255,255,255,0.05); padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid var(--border-color); color: var(--text-secondary); font-size: 0.9rem;">
            <i class="fas fa-info-circle" style="margin-left: 0.35rem;"></i> الخطوة الأولى للموافقة: المدير المباشر (من ملف الموظف). يمكن تعيين تسلسل كامل من <a href="{{ route('wesal.hr.show', ['section' => 'request-settings']) }}" style="color: var(--primary-color);">إعدادات الطلبات</a>.
        </div>
    @endif

    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">تقديم طلب جديد</h3>
        <form method="POST" action="{{ route('wesal.hr.leave.request') }}" style="max-width: 600px;">
            @csrf
            <div style="display: grid; gap: 1rem;">
                <div><label class="form-label">الموظف <span style="color: #ff8a80;">*</span></label><select name="employee_id" class="form-control" required><option value="">-- اختر --</option>@foreach($employees ?? [] as $e)<option value="{{ $e->id }}">{{ $e->name_ar }}</option>@endforeach</select></div>
                <div><label class="form-label">نوع الإجازة <span style="color: #ff8a80;">*</span></label><select name="leave_type_id" class="form-control" required><option value="">-- اختر --</option>@foreach($leaveTypes ?? [] as $lt)<option value="{{ $lt->id }}">{{ $lt->name_ar }} ({{ $lt->days_per_year }} يوم/سنة)</option>@endforeach</select></div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;"><div><label class="form-label">من تاريخ <span style="color: #ff8a80;">*</span></label><input type="date" name="start_date" class="form-control" required></div><div><label class="form-label">إلى تاريخ <span style="color: #ff8a80;">*</span></label><input type="date" name="end_date" class="form-control" required></div></div>
                <div><label class="form-label">ملاحظات</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
                <div><button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> تقديم الطلب</button></div>
            </div>
        </form>
    </div>
</div>
