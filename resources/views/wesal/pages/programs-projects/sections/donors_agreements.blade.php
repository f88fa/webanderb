<div class="content-card">
    <div class="page-header">
        <h1 class="page-title"><i class="fas fa-file-contract" style="color: var(--primary-color);"></i> الاتفاقيات</h1>
    </div>
    @if(session('success'))<div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>@endif
    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color); margin-bottom: 2rem;">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">إضافة اتفاقية</h3>
        <form method="POST" action="{{ route('wesal.programs-projects.agreements.store') }}" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem; align-items: end;">
            @csrf
            <div><label class="form-label">الجهة المانحة <span style="color:#dc3545">*</span></label><select name="donor_id" class="form-control" required><option value="">-- اختر --</option>@foreach($donors ?? [] as $dr)<option value="{{ $dr->id }}">{{ $dr->name_ar }}</option>@endforeach</select></div>
            <div><label class="form-label">المشروع</label><select name="project_id" class="form-control"><option value="">-- اختر --</option>@foreach($projects ?? [] as $pr)<option value="{{ $pr->id }}">{{ $pr->name_ar }}</option>@endforeach</select></div>
            <div><label class="form-label">رقم الاتفاقية</label><input type="text" name="agreement_no" class="form-control"></div>
            <div><label class="form-label">العنوان <span style="color:#dc3545">*</span></label><input type="text" name="title" class="form-control" required></div>
            <div><label class="form-label">المبلغ</label><input type="number" name="amount" step="0.01" min="0" class="form-control"></div>
            <div><label class="form-label">من تاريخ</label><input type="date" name="start_date" class="form-control"></div>
            <div><label class="form-label">إلى تاريخ</label><input type="date" name="end_date" class="form-control"></div>
            <div><label class="form-label">ملاحظات</label><input type="text" name="notes" class="form-control"></div>
            <div><button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة</button></div>
        </form>
    </div>
    <h3 style="color: var(--text-primary); margin-bottom: 1rem;">قائمة الاتفاقيات</h3>
    @if(isset($agreements) && $agreements->count() > 0)
        <div class="table-container">
            <table style="direction: rtl;"><thead><tr><th>الجهة المانحة</th><th>المشروع</th><th>العنوان</th><th>المبلغ</th><th>من</th><th>إلى</th><th style="text-align: center;">الإجراءات</th></tr></thead><tbody>
                @foreach($agreements as $a)
                <tr><td>{{ $a->donor?->name_ar }}</td><td>{{ $a->project?->name_ar ?? '-' }}</td><td>{{ $a->title }}</td><td>{{ $a->amount ? number_format($a->amount, 2) : '-' }}</td><td>{{ $a->start_date?->format('Y-m-d') ?? '-' }}</td><td>{{ $a->end_date?->format('Y-m-d') ?? '-' }}</td><td style="text-align: center;"><form method="POST" action="{{ route('wesal.programs-projects.agreements.destroy', $a) }}" style="display: inline;" onsubmit="return confirm('حذف الاتفاقية؟');">@csrf @method('DELETE')<button type="submit" class="btn btn-danger" style="padding: 0.4rem 0.8rem;"><i class="fas fa-trash"></i></button></form></td></tr>
                @endforeach
            </tbody></table>
        </div>
        {{ $agreements->links() }}
    @else
        <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد اتفاقيات.</p>
    @endif
</div>
