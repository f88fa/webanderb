<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-umbrella-beach" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            طلب إجازة
        </h1>
        <p class="page-subtitle">تقديم طلب إجازة — خاص بك فقط</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> @foreach($errors->all() as $e)<p style="margin: 0;">{{ $e }}</p>@endforeach</div>
    @endif

    @if(!$employee)
        <div class="alert" style="background: rgba(255,152,0,0.2); border: 1px solid rgba(255,152,0,0.5); color: #fff;">
            <i class="fas fa-info-circle"></i> يجب ربط حسابك بموظف في قسم الموارد البشرية لتمكين طلب الإجازة. تواصل مع المدير.
        </div>
    @else
        @if(!empty($approvalSequence) && $approvalSequence->isNotEmpty())
            <div style="background: rgba(95, 179, 142, 0.12); padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1.5rem; border: 1px solid var(--primary-color);">
                <h4 style="color: var(--primary-color); margin: 0 0 0.75rem 0; font-size: 0.95rem;"><i class="fas fa-list-ol" style="margin-left: 0.35rem;"></i> تسلسل الموافقات</h4>
                <ol style="margin: 0; padding-right: 1.5rem; color: var(--text-primary); font-size: 0.9rem;">
                    @foreach($approvalSequence as $step)
                        <li style="margin-bottom: 0.25rem;">{{ $step->approver_display }}</li>
                    @endforeach
                </ol>
            </div>
        @endif

        <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
            <h3 style="color: var(--text-primary); margin-bottom: 1rem;">تقديم طلب جديد</h3>
            <form method="POST" action="{{ route('wesal.requests.leave.store') }}" style="max-width: 600px;">
                @csrf
                <div style="display: grid; gap: 1rem;">
                    <div><label class="form-label">نوع الإجازة <span style="color: #ff8a80;">*</span></label><select name="leave_type_id" class="form-control" required><option value="">-- اختر --</option>@foreach($leaveTypes ?? [] as $lt)<option value="{{ $lt->id }}">{{ $lt->name_ar }} ({{ $lt->days_per_year }} يوم/سنة)</option>@endforeach</select></div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;"><div><label class="form-label">من تاريخ <span style="color: #ff8a80;">*</span></label><input type="date" name="start_date" class="form-control" required></div><div><label class="form-label">إلى تاريخ <span style="color: #ff8a80;">*</span></label><input type="date" name="end_date" class="form-control" required></div></div>
                    <div><label class="form-label">ملاحظات</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
                    <div><button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> تقديم الطلب</button></div>
                </div>
            </form>
        </div>

        <h3 style="color: var(--text-primary); margin-bottom: 0.5rem;">طلباتي</h3>
        <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1rem;">اضغط على أي طلب لعرض التفاصيل وتسلسل الموافقات (عند من يقف الطلب حالياً).</p>
        @if(isset($myLeaveRequests) && $myLeaveRequests->count() > 0)
            <div class="table-container">
                <table style="direction: rtl;">
                    <thead><tr><th>نوع الإجازة</th><th>من</th><th>إلى</th><th>الأيام</th><th>الحالة</th><th>الموافق / الرافض</th></tr></thead>
                    <tbody>
                        @foreach($myLeaveRequests as $req)
                        <tr style="cursor: pointer;" onclick="window.location='{{ route('wesal.requests.leave.show', $req) }}'">
                            <td><a href="{{ route('wesal.requests.leave.show', $req) }}" style="color: inherit; text-decoration: none;">{{ $req->leaveType->name_ar ?? '-' }}</a></td>
                            <td>{{ $req->start_date->format('Y-m-d') }}</td>
                            <td>{{ $req->end_date->format('Y-m-d') }}</td>
                            <td>{{ $req->days }}</td>
                            <td>@if($req->status === 'pending')<span style="color: #ff9800;">قيد الانتظار</span>@elseif($req->status === 'approved')<span style="color: #4caf50;">معتمد</span>@else<span style="color: #f44336;">مرفوض</span>@endif</td>
                            <td>
                                @if($req->status === 'approved')
                                    <span style="color: #4caf50;">وافق {{ $req->approvedByUser->name ?? '-' }}</span>
                                @elseif($req->status === 'rejected')
                                    <span style="color: #f44336;">رفض {{ $req->approvedByUser->name ?? '-' }}</span>
                                    @if(!empty($req->rejection_reason))<br><small style="color: var(--text-secondary);">{{ $req->rejection_reason }}</small>@endif
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 1rem;">{{ $myLeaveRequests->links() }}</div>
        @else
            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد طلبات إجازة.</p>
        @endif
    @endif
</div>
