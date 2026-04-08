@php $m = $editBoardMember ?? null; @endphp
@if(!$m)
<div class="content-card"><div class="alert alert-error">العضو غير موجود.</div><a href="{{ route('wesal.meetings.show', ['section' => 'board-members']) }}" class="btn btn-secondary">العودة</a></div>
@else
<div class="content-card">
    <div class="page-header"><h1 class="page-title"><i class="fas fa-edit" style="color: var(--primary-color);"></i> تعديل: {{ $m->name_ar }}</h1></div>
    @if($errors->any())<div class="alert alert-error">@foreach($errors->all() as $e)<p style="margin:0;">{{ $e }}</p>@endforeach</div>@endif
    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color);">
        <form method="POST" action="{{ route('wesal.meetings.board-members.update', $m) }}" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
            @csrf @method('PUT')
            <div><label class="form-label">الاسم بالعربي <span style="color:#dc3545">*</span></label><input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $m->name_ar) }}" required></div>
            <div><label class="form-label">الاسم بالإنجليزي</label><input type="text" name="name_en" class="form-control" value="{{ old('name_en', $m->name_en) }}"></div>
            <div><label class="form-label">المنصب</label><input type="text" name="position_ar" class="form-control" value="{{ old('position_ar', $m->position_ar) }}"></div>
            <div><label class="form-label">الجوال</label><input type="text" name="phone" class="form-control" value="{{ old('phone', $m->phone) }}"></div>
            <div><label class="form-label">البريد</label><input type="email" name="email" class="form-control" value="{{ old('email', $m->email) }}"></div>
            <div><label class="form-label">نشط</label><select name="is_active" class="form-control"><option value="1" {{ ($m->is_active ?? true) ? 'selected' : '' }}>نعم</option><option value="0" {{ !($m->is_active ?? true) ? 'selected' : '' }}>لا</option></select></div>
            <div style="grid-column: 1 / -1;"><label class="form-label">ملاحظات</label><textarea name="notes" class="form-control" rows="1">{{ old('notes', $m->notes) }}</textarea></div>
            <div><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ</button><a href="{{ route('wesal.meetings.show', ['section' => 'board-members']) }}" class="btn btn-secondary">إلغاء</a></div>
        </form>
    </div>
</div>
@endif
