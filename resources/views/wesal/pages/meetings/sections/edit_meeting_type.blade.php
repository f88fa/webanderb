@php $t = $editMeetingType ?? null; @endphp
@if(!$t)
<div class="content-card"><div class="alert alert-error">النوع غير موجود.</div><a href="{{ route('wesal.meetings.show', ['section' => 'meeting-types']) }}" class="btn btn-secondary">العودة</a></div>
@else
<div class="content-card">
    <div class="page-header"><h1 class="page-title"><i class="fas fa-edit" style="color: var(--primary-color);"></i> تعديل: {{ $t->name_ar }}</h1></div>
    @if($errors->any())<div class="alert alert-error">@foreach($errors->all() as $e)<p style="margin:0;">{{ $e }}</p>@endforeach</div>@endif
    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color);">
        <form method="POST" action="{{ route('wesal.meetings.meeting-types.update', $t) }}" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
            @csrf @method('PUT')
            <div><label class="form-label">الاسم بالعربي <span style="color:#dc3545">*</span></label><input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $t->name_ar) }}" required></div>
            <div><label class="form-label">الاسم بالإنجليزي</label><input type="text" name="name_en" class="form-control" value="{{ old('name_en', $t->name_en) }}"></div>
            <div style="grid-column: 1 / -1;"><label class="form-label">الوصف</label><textarea name="description" class="form-control" rows="2">{{ old('description', $t->description) }}</textarea></div>
            <div><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ</button><a href="{{ route('wesal.meetings.show', ['section' => 'meeting-types']) }}" class="btn btn-secondary">إلغاء</a></div>
        </form>
    </div>
</div>
@endif
