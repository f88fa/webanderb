<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-money-bill-wave" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            طلب مالي
        </h1>
        <p class="page-subtitle">تقديم طلب مالي — خاص بك فقط</p>
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
            <i class="fas fa-info-circle"></i> يجب ربط حسابك بموظف في قسم الموارد البشرية لتمكين الطلبات المالية. تواصل مع المدير.
        </div>
    @else
        <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
            <h3 style="color: var(--text-primary); margin-bottom: 1rem;">تقديم طلب مالي جديد</h3>
            <form method="POST" action="{{ route('wesal.requests.financial.store') }}" style="max-width: 600px;">
                @csrf
                <div style="display: grid; gap: 1rem;">
                    <div><label class="form-label">عنوان الطلب <span style="color: #ff8a80;">*</span></label><input type="text" name="title" class="form-control" value="{{ old('title') }}" required maxlength="255" placeholder="مثال: طلب صرف مبلغ مقابل دورة"></div>
                    <div><label class="form-label">التفاصيل / المبلغ أو السبب</label><textarea name="body" class="form-control" rows="4" placeholder="اشرح تفاصيل الطلب المالي">{{ old('body') }}</textarea></div>
                    <div><button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> تقديم الطلب</button></div>
                </div>
            </form>
        </div>

        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">طلباتي المالية</h3>
        @if(isset($myRequests) && $myRequests->count() > 0)
            <div class="table-container">
                <table style="direction: rtl;">
                    <thead><tr><th>العنوان</th><th>التاريخ</th><th>الحالة</th></tr></thead>
                    <tbody>
                        @foreach($myRequests as $req)
                        <tr>
                            <td>{{ $req->title }}</td>
                            <td>{{ $req->created_at->format('Y-m-d H:i') }}</td>
                            <td>@if($req->status === 'pending')<span style="color: #ff9800;">قيد الانتظار</span>@elseif($req->status === 'approved')<span style="color: #4caf50;">معتمد</span>@else<span style="color: #f44336;">مرفوض</span>@endif</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 1rem;">{{ $myRequests->links() }}</div>
        @else
            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد طلبات مالية.</p>
        @endif
    @endif
</div>
