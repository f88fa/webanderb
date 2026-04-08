@php $p = $editProject ?? null; @endphp
@if(!$p)
<div class="content-card"><div class="alert alert-error">المشروع غير موجود.</div><a href="{{ route('wesal.programs-projects.show', ['section' => 'projects', 'sub' => 'list']) }}" class="btn btn-secondary">العودة</a></div>
@else
<div class="content-card">
    <div class="page-header">
        <h1 class="page-title"><i class="fas fa-edit" style="color: var(--primary-color);"></i> تعديل المشروع: {{ $p->name_ar }}</h1>
    </div>
    @if($errors->any())<div class="alert alert-error">@foreach($errors->all() as $e)<p style="margin:0;">{{ $e }}</p>@endforeach</div>@endif
    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color);">
        <form method="POST" action="{{ route('wesal.programs-projects.projects.update', $p) }}" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
            @csrf @method('PUT')
            <div><label class="form-label">رقم المشروع <span style="color:#dc3545">*</span></label><input type="text" name="project_no" class="form-control" value="{{ old('project_no', $p->project_no) }}" required></div>
            <div><label class="form-label">الاسم بالعربي <span style="color:#dc3545">*</span></label><input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $p->name_ar) }}" required></div>
            <div><label class="form-label">الاسم بالإنجليزي</label><input type="text" name="name_en" class="form-control" value="{{ old('name_en', $p->name_en) }}"></div>
            <div><label class="form-label">الجهة المانحة</label><select name="donor_id" class="form-control"><option value="">-- اختر --</option>@foreach($donors ?? [] as $d)<option value="{{ $d->id }}" {{ old('donor_id', $p->donor_id) == $d->id ? 'selected' : '' }}>{{ $d->name_ar }}</option>@endforeach</select></div>
            <div><label class="form-label">تاريخ البداية</label><input type="date" name="start_date" class="form-control" value="{{ old('start_date', $p->start_date?->format('Y-m-d')) }}"></div>
            <div><label class="form-label">تاريخ النهاية</label><input type="date" name="end_date" class="form-control" value="{{ old('end_date', $p->end_date?->format('Y-m-d')) }}"></div>
            <div><label class="form-label">الميزانية</label><input type="number" name="budget_amount" class="form-control" step="0.01" min="0" value="{{ old('budget_amount', $p->budget_amount) }}"></div>
            <div><label class="form-label">الحالة</label><select name="status" class="form-control"><option value="active" {{ old('status', $p->status) === 'active' ? 'selected' : '' }}>نشط</option><option value="completed" {{ old('status', $p->status) === 'completed' ? 'selected' : '' }}>مكتمل</option><option value="archived" {{ old('status', $p->status) === 'archived' ? 'selected' : '' }}>مؤرشف</option></select></div>
            <div style="grid-column: 1 / -1;"><label class="form-label">الوصف</label><textarea name="description" class="form-control" rows="2">{{ old('description', $p->description) }}</textarea></div>
            <div style="grid-column: 1 / -1;"><label class="form-label">ملاحظات</label><textarea name="notes" class="form-control" rows="1">{{ old('notes', $p->notes) }}</textarea></div>
            <div><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ</button><a href="{{ route('wesal.programs-projects.show', ['section' => 'projects', 'sub' => 'list']) }}" class="btn btn-secondary">إلغاء</a></div>
        </form>
    </div>
</div>
@endif
