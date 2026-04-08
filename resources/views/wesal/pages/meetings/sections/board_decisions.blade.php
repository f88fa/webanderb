<div class="content-card">
    <div class="page-header">
        <h1 class="page-title"><i class="fas fa-file-signature" style="color: var(--primary-color);"></i> قرارات المجلس</h1>
    </div>
    @if(session('success'))<div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>@endif
    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color); margin-bottom: 2rem;">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">إضافة قرار</h3>
        <form method="POST" action="{{ route('wesal.meetings.board-decisions.store') }}" style="display: flex; flex-direction: column; gap: 1rem;">
            @csrf
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem; align-items: end;">
                <div><label class="form-label">العنوان <span style="color:#dc3545">*</span></label><input type="text" name="title" class="form-control" required></div>
                <div><label class="form-label">رقم القرار</label><input type="text" name="decision_no" class="form-control"></div>
                <div><label class="form-label">التاريخ <span style="color:#dc3545">*</span></label><input type="date" name="decision_date" class="form-control" required></div>
                <div><label class="form-label">الاجتماع</label><select name="board_meeting_id" class="form-control"><option value="">-- اختر --</option>@foreach($boardMeetingsList ?? [] as $bml)<option value="{{ $bml->id }}">{{ $bml->meeting_no }} - {{ $bml->title }}</option>@endforeach</select></div>
            </div>
            <div class="form-group" style="margin-top: 0.5rem;">
                <label class="form-label"><i class="fas fa-align-right" style="color: var(--primary-color); margin-left: 0.35rem;"></i> الوصف</label>
                @include('wesal.partials.meeting-rich-editor', ['name' => 'description', 'placeholder' => 'اكتب نص القرار والتفاصيل...', 'content' => old('description', ''), 'minHeight' => 240])
            </div>
            <div><button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة</button></div>
        </form>
    </div>
    <h3 style="color: var(--text-primary); margin-bottom: 1rem;">قائمة القرارات</h3>
    @if(isset($boardDecisions) && $boardDecisions->count() > 0)
        <div class="table-container">
            <table style="direction: rtl;"><thead><tr><th>رقم</th><th>العنوان</th><th>التاريخ</th><th>الاجتماع</th><th style="text-align: center;">الإجراءات</th></tr></thead><tbody>
                @foreach($boardDecisions as $bd)
                <tr><td>{{ $bd->decision_no ?? '-' }}</td><td>{{ $bd->title }}</td><td>{{ $bd->decision_date?->format('Y-m-d') }}</td><td>{{ $bd->boardMeeting?->meeting_no ?? '-' }}</td><td style="text-align: center;"><a href="{{ route('wesal.meetings.show', ['section' => 'edit-board-decision', 'sub' => $bd->id]) }}" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; margin-left: 0.25rem;"><i class="fas fa-edit"></i></a><form method="POST" action="{{ route('wesal.meetings.board-decisions.destroy', $bd) }}" style="display: inline;" onsubmit="return confirm('حذف القرار؟');">@csrf @method('DELETE')<button type="submit" class="btn btn-danger" style="padding: 0.4rem 0.8rem;"><i class="fas fa-trash"></i></button></form></td></tr>
                @endforeach
            </tbody></table>
        </div>
        {{ $boardDecisions->links() }}
    @else
        <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد قرارات.</p>
    @endif
</div>
@include('wesal.partials.meeting-rich-editor-script')
