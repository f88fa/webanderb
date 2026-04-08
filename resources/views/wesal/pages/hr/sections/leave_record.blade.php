<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-history" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            سجل الإجازات
        </h1>
        <p class="page-subtitle">عرض سجل الطلبات — اختر موظفاً أو اتركه فارغاً لعرض الكل. يظهر من وافق ومن رفض مع سبب الرفض</p>
    </div>

    <form method="GET" action="{{ route('wesal.hr.show', ['section' => 'leave', 'sub' => 'record']) }}" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end; margin-bottom: 1.5rem;">
        <div style="min-width: 260px;">
            <label class="form-label">اختر الموظف</label>
            <select name="employee_id" class="form-control" onchange="this.form.submit()">
                <option value="">-- اختر موظف --</option>
                @foreach($employees ?? [] as $emp)
                    <option value="{{ $emp->id }}" {{ ($selectedEmployeeId ?? '') == $emp->id ? 'selected' : '' }}>{{ $emp->name_ar }} ({{ $emp->employee_no }})</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">عرض</button>
    </form>

    @if(isset($leaveRecords) && $leaveRecords->count() > 0)
            <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color);">
                <div class="table-container">
                    <table style="direction: rtl;">
                        <thead>
                            <tr>
                                @if(!$selectedEmployeeId)<th>الموظف</th>@endif
                                <th>نوع الإجازة</th>
                                <th>من</th>
                                <th>إلى</th>
                                <th style="text-align: center;">الأيام</th>
                                <th>الحالة</th>
                                <th>الموافق / الرافض</th>
                                <th>تاريخ الطلب</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaveRecords as $req)
                            <tr style="cursor: pointer;" onclick="window.location='{{ route('wesal.hr.leave.show', $req) }}'">
                                @if(!$selectedEmployeeId)<td>{{ $req->employee->name_ar ?? '-' }}</td>@endif
                                <td><a href="{{ route('wesal.hr.leave.show', $req) }}" style="color: inherit; text-decoration: none;">{{ $req->leaveType->name_ar ?? '-' }}</a></td>
                                <td>{{ $req->start_date->format('Y-m-d') }}</td>
                                <td>{{ $req->end_date->format('Y-m-d') }}</td>
                                <td style="text-align: center;">{{ $req->days }}</td>
                                <td>
                                    @if($req->status === 'pending')
                                        <span style="color: #ff9800;">قيد الانتظار</span>
                                    @elseif($req->status === 'approved')
                                        <span style="color: #4caf50;">معتمد</span>
                                    @else
                                        <span style="color: #f44336;">مرفوض</span>
                                    @endif
                                </td>
                                <td>
                                    @if($req->status === 'approved')
                                        <span style="color: #4caf50;">وافق {{ $req->approvedByUser->name ?? '-' }}</span>
                                    @elseif($req->status === 'rejected')
                                        <span style="color: #f44336;">رفض {{ $req->approvedByUser->name ?? '-' }}</span>
                                        @if(!empty($req->rejection_reason))
                                            <br><small style="color: var(--text-secondary);" title="سبب الرفض">{{ $req->rejection_reason }}</small>
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $req->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div style="margin-top: 1rem;">{{ $leaveRecords->links() }}</div>
            </div>
        @else
            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">@if($selectedEmployeeId)لا توجد إجازات مسجلة لهذا الموظف.@elseلا توجد طلبات إجازة.@endif</p>
        @endif
</div>
