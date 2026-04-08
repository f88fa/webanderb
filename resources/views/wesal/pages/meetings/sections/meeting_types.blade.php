<div class="content-card">
    <div class="page-header" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
        <h1 class="page-title"><i class="fas fa-list-alt" style="color: var(--primary-color);"></i> أنواع الاجتماعات</h1>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('addTypeForm').style.display = document.getElementById('addTypeForm').style.display === 'none' ? 'block' : 'none';">
            <i class="fas fa-plus"></i> إضافة نوع
        </button>
    </div>
    @if(session('success'))<div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>@endif
    <div id="addTypeForm" style="display: none; background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; border: 2px solid var(--border-color);">
        <form method="POST" action="{{ route('wesal.meetings.meeting-types.store') }}" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
            @csrf
            <div><label class="form-label">الاسم بالعربي <span style="color:#dc3545">*</span></label><input type="text" name="name_ar" class="form-control" required></div>
            <div><label class="form-label">الاسم بالإنجليزي</label><input type="text" name="name_en" class="form-control"></div>
            <div><label class="form-label">الوصف</label><textarea name="description" class="form-control" rows="1"></textarea></div>
            <div><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ</button></div>
        </form>
    </div>
    @if(isset($meetingTypes) && $meetingTypes->count() > 0)
        <div class="table-container">
            <table style="direction: rtl;"><thead><tr><th>الاسم</th><th>الوصف</th><th style="text-align: center;">الإجراءات</th></tr></thead><tbody>
                @foreach($meetingTypes as $t)
                <tr><td>{{ $t->name_ar }}</td><td>{{ Str::limit($t->description, 50) ?? '-' }}</td><td style="text-align: center;"><a href="{{ route('wesal.meetings.show', ['section' => 'edit-meeting-type', 'sub' => $t->id]) }}" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; margin-left: 0.25rem;"><i class="fas fa-edit"></i></a><form method="POST" action="{{ route('wesal.meetings.meeting-types.destroy', $t) }}" style="display: inline;" onsubmit="return confirm('حذف النوع؟');">@csrf @method('DELETE')<button type="submit" class="btn btn-danger" style="padding: 0.4rem 0.8rem;"><i class="fas fa-trash"></i></button></form></td></tr>
                @endforeach
            </tbody></table>
        </div>
    @else
        <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد أنواع اجتماعات.</p>
    @endif
</div>
