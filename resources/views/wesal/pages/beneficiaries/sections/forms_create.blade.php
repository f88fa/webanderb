<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-plus-circle" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            إنشاء نموذج مستفيدين
        </h1>
        <p class="page-subtitle">بعد الإنشاء ستضيف الحقول (الاسم، الهوية، الجوال، حقول مخصصة...)</p>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            @foreach($errors->all() as $err) <p style="margin: 0;">{{ $err }}</p> @endforeach
        </div>
    @endif

    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; max-width: 500px; border: 1px solid var(--border-color);">
        <form method="POST" action="{{ route('wesal.beneficiaries.forms.store') }}">
            @csrf
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">اسم النموذج بالعربي <span style="color: #ff8a80;">*</span></label>
                <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar') }}" required placeholder="مثال: مستفيدين أشخاص / جهات / منشآت">
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">المعرّف (slug) — اختياري، يُولَّد تلقائياً من الاسم</label>
                <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" placeholder="مثال: persons أو entities">
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> إنشاء ثم إضافة الحقول</button>
                <a href="{{ route('wesal.beneficiaries.forms.index') }}" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
