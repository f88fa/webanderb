@php $bd = $editBoardDecision ?? null; @endphp
@if(!$bd)
<div class="content-card"><div class="alert alert-error">القرار غير موجود.</div><a href="{{ route('wesal.meetings.show', ['section' => 'board-decisions']) }}" class="btn btn-secondary">العودة</a></div>
@else
<div class="content-card">
    <div class="page-header"><h1 class="page-title"><i class="fas fa-edit" style="color: var(--primary-color);"></i> تعديل: {{ $bd->title }}</h1></div>
    @if($errors->any())<div class="alert alert-error">@foreach($errors->all() as $e)<p style="margin:0;">{{ $e }}</p>@endforeach</div>@endif
    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color);">
        <form method="POST" action="{{ route('wesal.meetings.board-decisions.update', $bd) }}" style="display: flex; flex-direction: column; gap: 1rem;">
            @csrf @method('PUT')
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem; align-items: end;">
                <div><label class="form-label">العنوان <span style="color:#dc3545">*</span></label><input type="text" name="title" class="form-control" value="{{ old('title', $bd->title) }}" required></div>
                <div><label class="form-label">رقم القرار</label><input type="text" name="decision_no" class="form-control" value="{{ old('decision_no', $bd->decision_no) }}"></div>
                <div><label class="form-label">التاريخ <span style="color:#dc3545">*</span></label><input type="date" name="decision_date" class="form-control" value="{{ old('decision_date', $bd->decision_date?->format('Y-m-d')) }}" required></div>
                <div><label class="form-label">الاجتماع</label><select name="board_meeting_id" class="form-control"><option value="">-- اختر --</option>@foreach($boardMeetingsList ?? [] as $bml)<option value="{{ $bml->id }}" {{ ($bd->board_meeting_id ?? 0) == $bml->id ? 'selected' : '' }}>{{ $bml->meeting_no }} - {{ $bml->title }}</option>@endforeach</select></div>
            </div>
            <div class="form-group" style="margin-top: 0.5rem;">
                <label class="form-label"><i class="fas fa-align-right" style="color: var(--primary-color); margin-left: 0.35rem;"></i> الوصف</label>
                @include('wesal.partials.meeting-rich-editor', ['name' => 'description', 'placeholder' => 'اكتب نص القرار والتفاصيل...', 'content' => old('description', $bd->description ?? ''), 'minHeight' => 240])
            </div>
            <div><label class="form-label">ملاحظات</label><textarea name="notes" class="form-control" rows="1">{{ old('notes', $bd->notes) }}</textarea></div>
            <div><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ</button><a href="{{ route('wesal.meetings.show', ['section' => 'board-decisions']) }}" class="btn btn-secondary">إلغاء</a></div>
        </form>
    </div>
</div>
@include('wesal.partials.meeting-rich-editor-script')
@endif
