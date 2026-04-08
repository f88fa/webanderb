<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-plus-circle" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            البدلات والخصومات
        </h1>
        <p class="page-subtitle">إدارة أنواع البدلات والخصومات</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif

    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">إضافة نوع بدل أو خصم</h3>
        <form method="POST" action="{{ route('wesal.hr.allowance-deduction.store') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; align-items: end;">
            @csrf
            <div><label class="form-label">الاسم <span style="color: #ff8a80;">*</span></label><input type="text" name="name_ar" class="form-control" required></div>
            <div><label class="form-label">النوع <span style="color: #ff8a80;">*</span></label><select name="type" class="form-control" required><option value="allowance">بدل</option><option value="deduction">خصم</option></select></div>
            <div><label class="form-label">المبلغ الافتراضي</label><input type="number" name="default_amount" class="form-control" value="0" step="0.01" min="0" dir="ltr" style="text-align:left;"></div>
            <div><button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة</button></div>
        </form>
    </div>

    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color);">
        <p class="form-label" style="margin-bottom: 0.5rem;">البدلات:</p>
        <p style="color: var(--text-secondary); margin-bottom: 1rem;">@forelse($allowanceTypes ?? [] as $a){{ $a->name_ar }}@if(!$loop->last)، @endif @empty — @endforelse</p>
        <p class="form-label" style="margin-bottom: 0.5rem;">الخصومات:</p>
        <p style="color: var(--text-secondary);">@forelse($deductionTypes ?? [] as $d){{ $d->name_ar }}@if(!$loop->last)، @endif @empty — @endforelse</p>
    </div>
</div>
