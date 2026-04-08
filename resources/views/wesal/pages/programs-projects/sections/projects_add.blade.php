<div class="content-card">
    <div class="page-header">
        <h1 class="page-title"><i class="fas fa-plus-circle" style="color: var(--primary-color); margin-left: 0.5rem;"></i> إضافة مشروع</h1>
        <p class="page-subtitle">تسجيل مشروع جديد (رقم المشروع يُولَّد تلقائياً إن تُرك فارغاً)</p>
    </div>
    @if($errors->any())<div class="alert alert-error">@foreach($errors->all() as $e)<p style="margin:0;">{{ $e }}</p>@endforeach</div>@endif
    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color);">
        <form method="POST" action="{{ route('wesal.programs-projects.projects.store') }}" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
            @csrf
            <div><label class="form-label">رقم المشروع (اختياري)</label><input type="text" name="project_no" class="form-control" value="{{ old('project_no') }}" placeholder="PP-2026-0001"></div>
            <div><label class="form-label">الاسم بالعربي <span style="color:#dc3545">*</span></label><input type="text" name="name_ar" class="form-control" value="{{ old('name_ar') }}" required></div>
            <div><label class="form-label">الاسم بالإنجليزي</label><input type="text" name="name_en" class="form-control" value="{{ old('name_en') }}"></div>
            <div><label class="form-label">الجهة المانحة</label><select name="donor_id" class="form-control"><option value="">-- اختر --</option>@foreach($donors ?? [] as $d)<option value="{{ $d->id }}" {{ old('donor_id') == $d->id ? 'selected' : '' }}>{{ $d->name_ar }}</option>@endforeach</select></div>
            <div><label class="form-label">تاريخ البداية</label><input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}"></div>
            <div><label class="form-label">تاريخ النهاية</label><input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}"></div>
            <div><label class="form-label">الميزانية</label><input type="number" name="budget_amount" class="form-control" step="0.01" min="0" value="{{ old('budget_amount') }}"></div>
            <div style="grid-column: 1 / -1;"><label class="form-label">الوصف</label><textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea></div>
            <div style="grid-column: 1 / -1;"><label class="form-label">ملاحظات</label><textarea name="notes" class="form-control" rows="1">{{ old('notes') }}</textarea></div>
            <div><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ</button><a href="{{ route('wesal.programs-projects.show', ['section' => 'projects', 'sub' => 'list']) }}" class="btn btn-secondary">إلغاء</a></div>
        </form>
    </div>
</div>
