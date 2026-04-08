@php
    $req = $leaveRequest ?? null;
    $seq = $approvalSequence ?? collect();
@endphp
@if(!$req)
    <p style="color: var(--text-secondary);">الطلب غير موجود.</p>
@else
<div class="content-card">
    <div class="page-header" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
        <div>
            <h1 class="page-title">
                <i class="fas fa-file-alt" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
                تفاصيل طلب إجازة
            </h1>
            <p class="page-subtitle">{{ $req->employee->name_ar ?? '-' }} — {{ $req->leaveType->name_ar ?? '-' }}</p>
        </div>
        @if(!empty($fromRequests))
            <a href="{{ route('wesal.requests.show', ['section' => 'leave']) }}" class="btn" style="background: rgba(255,255,255,0.1); color: var(--text-primary); text-decoration: none; padding: 0.5rem 1rem; border-radius: 8px;"><i class="fas fa-arrow-right"></i> رجوع لطلباتي</a>
        @else
            <a href="{{ route('wesal.hr.show', ['section' => 'leave', 'sub' => 'record']) }}" class="btn" style="background: rgba(255,255,255,0.1); color: var(--text-primary); text-decoration: none; padding: 0.5rem 1rem; border-radius: 8px;"><i class="fas fa-arrow-right"></i> رجوع لسجل الطلبات</a>
        @endif
    </div>

    {{-- بيانات الطلب --}}
    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; border: 2px solid var(--border-color);">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">بيانات الطلب</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem;">
            <div><span style="color: var(--text-secondary);">الموظف:</span> {{ $req->employee->name_ar ?? '-' }}</div>
            <div><span style="color: var(--text-secondary);">نوع الإجازة:</span> {{ $req->leaveType->name_ar ?? '-' }}</div>
            <div><span style="color: var(--text-secondary);">من:</span> {{ $req->start_date->format('Y-m-d') }}</div>
            <div><span style="color: var(--text-secondary);">إلى:</span> {{ $req->end_date->format('Y-m-d') }}</div>
            <div><span style="color: var(--text-secondary);">الأيام:</span> {{ $req->days }}</div>
            <div><span style="color: var(--text-secondary);">الحالة:</span>
                @if($req->status === 'pending')<span style="color: #ff9800;">قيد الانتظار</span>
                @elseif($req->status === 'approved')<span style="color: #4caf50;">معتمد</span>
                @else<span style="color: #f44336;">مرفوض</span>@endif
            </div>
            <div><span style="color: var(--text-secondary);">تاريخ الطلب:</span> {{ $req->created_at->format('Y-m-d H:i') }}</div>
            @if($req->notes)<div style="grid-column: 1/-1;"><span style="color: var(--text-secondary);">ملاحظات:</span> {{ $req->notes }}</div>@endif
        </div>
    </div>

    {{-- تسلسل الموافقات ومن بانتظاره الطلب --}}
    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color);">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;"><i class="fas fa-list-ol" style="color: var(--primary-color); margin-left: 0.35rem;"></i> تسلسل الموافقات</h3>
        @if($seq->isNotEmpty())
            <ol style="margin: 0; padding-right: 1.5rem;">
                @foreach($seq as $idx => $step)
                    @php $stepNum = $idx + 1; @endphp
                    <li style="margin-bottom: 1rem; padding: 0.75rem; background: rgba(255,255,255,0.03); border-radius: 8px; border-right: 3px solid var(--border-color);">
                        <strong>{{ $step->approver_display }}</strong>
                        @if($req->status === 'pending')
                            @if($stepNum === 1)
                                <span style="color: #ff9800; margin-right: 0.5rem;">← الطلب <strong>عند هذه الخطوة</strong> بانتظار القبول أو الرفض</span>
                            @else
                                <span style="color: var(--text-secondary); font-size: 0.9rem;">— لم يصل بعد</span>
                            @endif
                        @elseif($req->status === 'approved')
                            @if($stepNum === 1)
                                <span style="color: #4caf50;">وافق {{ $req->approvedByUser->name ?? '-' }}</span>
                            @else
                                <span style="color: var(--text-secondary);">—</span>
                            @endif
                        @else
                            @if($stepNum === 1)
                                <span style="color: #f44336;">رفض {{ $req->approvedByUser->name ?? '-' }}</span>
                                @if(!empty($req->rejection_reason))
                                    <div style="margin-top: 0.5rem; padding: 0.5rem; background: rgba(244,67,54,0.1); border-radius: 6px;"><strong>سبب الرفض:</strong> {{ $req->rejection_reason }}</div>
                                @endif
                            @else
                                <span style="color: var(--text-secondary);">—</span>
                            @endif
                        @endif
                    </li>
                @endforeach
            </ol>
            @if($req->status === 'pending')
                <p style="margin-top: 1rem; color: #ff9800; font-weight: 600;"><i class="fas fa-clock"></i> الطلب واقف عند: <strong>{{ $seq->first()->approver_display }}</strong> بانتظار قبوله أو رفضه</p>
            @endif
        @else
            <p style="color: var(--text-secondary);">لم يُعرّف تسلسل موافقات. <a href="{{ route('wesal.hr.show', ['section' => 'request-settings']) }}" style="color: var(--primary-color);">إعدادات الطلبات</a></p>
        @endif
    </div>
</div>
@endif
