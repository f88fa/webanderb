<div class="content-card">
    <div class="page-header" style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 class="page-title">
                <i class="fas fa-file-invoice-dollar" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
                سجل طلبات الصرف
            </h1>
            <p class="page-subtitle">عرض وإدارة طلبات الصرف</p>
        </div>
        <a href="{{ route('wesal.finance.payment-requests.create') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: var(--primary-color); color: white; border-radius: 8px; text-decoration: none; font-weight: 600;">
            <i class="fas fa-plus-circle"></i>
            <span>طلب صرف جديد</span>
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 1rem;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 1rem; background: rgba(244,67,54,0.15); border: 1px solid #f44336; color: #d32f2f; padding: 1rem; border-radius: 8px;">
            <i class="fas fa-exclamation-circle"></i>
            @foreach($errors->all() as $err) {{ $err }} @endforeach
        </div>
    @endif

    <!-- فلترة -->
    <div style="background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
        <form method="GET" action="{{ route('wesal.finance.payment-requests.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: end;">
            <div>
                <label style="display: block; margin-bottom: 0.25rem; color: var(--text-primary); font-size: 0.85rem;">الحالة</label>
                <select name="status" style="padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--border-color); background: rgba(255,255,255,0.1); color: var(--text-primary); min-width: 160px;">
                    <option value="">الكل</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>موافق عليه</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>تم الصرف</option>
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.25rem; color: var(--text-primary); font-size: 0.85rem;">السنة المالية</label>
                <select name="fiscal_year_id" style="padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--border-color); background: rgba(255,255,255,0.1); color: var(--text-primary); min-width: 200px;">
                    <option value="">الكل</option>
                    @foreach($fiscalYears ?? [] as $fy)
                        <option value="{{ $fy->id }}" {{ request('fiscal_year_id') == $fy->id ? 'selected' : '' }}>{{ $fy->year_name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" style="padding: 0.5rem 1rem; background: var(--primary-color); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                <i class="fas fa-filter"></i> تصفية
            </button>
        </form>
    </div>

    <!-- جدول الطلبات -->
    <div style="background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 12px; border: 1px solid var(--border-color);">
        @if($paymentRequests->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; direction: rtl;">
                    <thead>
                        <tr style="background: rgba(0,0,0,0.2);">
                            <th style="padding: 0.75rem; text-align: center; color: var(--text-primary);">رقم الطلب</th>
                            <th style="padding: 0.75rem; text-align: center; color: var(--text-primary);">التاريخ</th>
                            <th style="padding: 0.75rem; text-align: center; color: var(--text-primary);">المستفيد</th>
                            <th style="padding: 0.75rem; text-align: center; color: var(--text-primary);">المبلغ</th>
                            <th style="padding: 0.75rem; text-align: center; color: var(--text-primary);">الحالة</th>
                            <th style="padding: 0.75rem; text-align: center; color: var(--text-primary);">الفترة</th>
                            <th style="padding: 0.75rem; text-align: center; color: var(--text-primary);">سند الصرف</th>
                            <th style="padding: 0.75rem; text-align: center; color: var(--text-primary);">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paymentRequests as $pr)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem; text-align: center; color: var(--text-primary);"><strong>{{ $pr->request_no }}</strong></td>
                            <td style="padding: 0.75rem; text-align: center; color: var(--text-primary);">{{ $pr->request_date?->format('Y-m-d') }}</td>
                            <td style="padding: 0.75rem; text-align: right; color: var(--text-primary);">
                                @if($pr->beneficiary_id && $pr->beneficiaryBeneficiary)
                                    <a href="{{ route('wesal.beneficiaries.show', ['section' => 'profile', 'sub' => $pr->beneficiary_id]) }}" style="color: var(--primary-color);">{{ $pr->displayBeneficiaryName() }}</a>
                                    <span style="color: var(--text-secondary); font-size: 0.8rem;">(مستفيد)</span>
                                @else
                                    {{ $pr->displayBeneficiaryName() }}
                                    @if($pr->beneficiaryServices && $pr->beneficiaryServices->count() > 0)
                                        <a href="{{ route('wesal.finance.payment-requests.beneficiaries', $pr) }}" style="margin-right: 0.5rem; color: var(--primary-color); font-size: 0.85rem;" title="عرض قائمة المستفيدين">(عرض المستفيدين)</a>
                                    @endif
                                @endif
                            </td>
                            <td style="padding: 0.75rem; text-align: center; color: var(--primary-color); font-weight: 600;">{{ number_format($pr->amount, 2) }}</td>
                            <td style="padding: 0.75rem; text-align: center;">
                                @if($pr->status === 'pending')
                                    <span style="padding: 0.25rem 0.6rem; background: rgba(255,152,0,0.3); color: #ff9800; border-radius: 6px; font-size: 0.85rem;">قيد الانتظار</span>
                                @elseif($pr->status === 'approved')
                                    <span style="padding: 0.25rem 0.6rem; background: rgba(76,175,80,0.3); color: #4caf50; border-radius: 6px; font-size: 0.85rem;">موافق عليه</span>
                                @elseif($pr->status === 'rejected')
                                    <span style="padding: 0.25rem 0.6rem; background: rgba(244,67,54,0.3); color: #f44336; border-radius: 6px; font-size: 0.85rem;">مرفوض</span>
                                @else
                                    <span style="padding: 0.25rem 0.6rem; background: rgba(33,150,243,0.3); color: #2196f3; border-radius: 6px; font-size: 0.85rem;">تم الصرف</span>
                                @endif
                            </td>
                            <td style="padding: 0.75rem; text-align: center; color: var(--text-primary);">{{ $pr->period?->period_name ?? '-' }} {{ $pr->period?->fiscalYear?->year_name ? '(' . $pr->period->fiscalYear->year_name . ')' : '' }}</td>
                            <td style="padding: 0.75rem; text-align: center; color: var(--text-primary);">
                                @if($pr->journal_entry_id && $pr->journalEntry)
                                    <a href="{{ route('wesal.finance.journal-entries.show', $pr->journalEntry) }}" style="color: var(--primary-color); text-decoration: none; font-weight: 600;">
                                        {{ $pr->journalEntry->entry_no }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td style="padding: 0.75rem; text-align: center;">
                                @if($pr->status === 'pending')
                                    <form method="POST" action="{{ route('wesal.finance.payment-requests.approve', $pr) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" style="padding: 0.35rem 0.7rem; background: #4caf50; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;" title="اعتماد">اعتماد</button>
                                    </form>
                                    <form method="POST" action="{{ route('wesal.finance.payment-requests.reject', $pr) }}" style="display: inline;" onsubmit="return confirm('رفض طلب الصرف؟');">
                                        @csrf
                                        <button type="submit" style="padding: 0.35rem 0.7rem; background: #f44336; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;" title="رفض">رفض</button>
                                    </form>
                                @elseif($pr->status === 'approved' && !$pr->journal_entry_id)
                                    <a href="{{ route('wesal.finance.payment-voucher.create', ['payment_request_id' => $pr->id]) }}" style="padding: 0.35rem 0.7rem; background: var(--primary-color); color: white; border-radius: 4px; text-decoration: none; font-size: 0.8rem;">تنفيذ الصرف</a>
                                @elseif($pr->journal_entry_id && $pr->journalEntry)
                                    <a href="{{ route('wesal.finance.journal-entries.print', $pr->journalEntry) }}" style="padding: 0.35rem 0.7rem; background: #2196f3; color: white; border-radius: 4px; text-decoration: none; font-size: 0.8rem;"><i class="fas fa-print"></i></a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 1rem;">{{ $paymentRequests->links() }}</div>
        @else
            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد طلبات صرف. <a href="{{ route('wesal.finance.payment-requests.create') }}" style="color: var(--primary-color);">إنشاء طلب صرف جديد</a></p>
        @endif
    </div>
</div>
