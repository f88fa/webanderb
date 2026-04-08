<div class="content-card">
    <div class="page-header" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
        <div><h1 class="page-title"><i class="fas fa-handshake" style="color: var(--primary-color);"></i> قائمة الجهات المانحة</h1></div>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('addDonorForm').style.display = document.getElementById('addDonorForm').style.display === 'none' ? 'block' : 'none';">
            <i class="fas fa-plus"></i> إضافة جهة
        </button>
    </div>
    @if(session('success'))<div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>@endif
    <div id="addDonorForm" style="display: none; background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; border: 2px solid var(--border-color);">
        <form method="POST" action="{{ route('wesal.programs-projects.donors.store') }}" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
            @csrf
            <div><label class="form-label">الاسم بالعربي <span style="color:#dc3545">*</span></label><input type="text" name="name_ar" class="form-control" required></div>
            <div><label class="form-label">الاسم بالإنجليزي</label><input type="text" name="name_en" class="form-control"></div>
            <div><label class="form-label">اسم جهة الاتصال</label><input type="text" name="contact_name" class="form-control"></div>
            <div><label class="form-label">الجوال</label><input type="text" name="phone" class="form-control"></div>
            <div><label class="form-label">البريد</label><input type="email" name="email" class="form-control"></div>
            <div><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ</button></div>
        </form>
    </div>
    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color);">
        @if(isset($donorsList) && $donorsList->count() > 0)
            <div class="table-container">
                <table style="direction: rtl;"><thead><tr><th>الاسم</th><th>جهة الاتصال</th><th>الجوال</th><th>البريد</th><th>المشاريع</th><th style="text-align: center;">الإجراءات</th></tr></thead><tbody>
                    @foreach($donorsList as $d)
                    <tr><td>{{ $d->name_ar }}</td><td>{{ $d->contact_name ?? '-' }}</td><td>{{ $d->phone ?? '-' }}</td><td>{{ $d->email ?? '-' }}</td><td>{{ $d->projects_count ?? 0 }}</td><td style="text-align: center;"><a href="{{ route('wesal.programs-projects.show', ['section' => 'edit-donor', 'sub' => $d->id]) }}" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; margin-left: 0.25rem;"><i class="fas fa-edit"></i></a><form method="POST" action="{{ route('wesal.programs-projects.donors.destroy', $d) }}" style="display: inline;" onsubmit="return confirm('حذف الجهة؟');">@csrf @method('DELETE')<button type="submit" class="btn btn-danger" style="padding: 0.4rem 0.8rem;"><i class="fas fa-trash"></i></button></form></td></tr>
                    @endforeach
                </tbody></table>
            </div>
        @else
            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد جهات مانحة.</p>
        @endif
    </div>
</div>
