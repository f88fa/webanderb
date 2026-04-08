@php $sm = $editStaffMeeting ?? null; @endphp
@if(!$sm)
<div class="content-card"><div class="alert alert-error">الاجتماع غير موجود.</div><a href="{{ route('wesal.meetings.show', ['section' => 'staff-meetings']) }}" class="btn btn-secondary">العودة</a></div>
@else
<div class="content-card">
    <div class="page-header"><h1 class="page-title"><i class="fas fa-edit" style="color: var(--primary-color);"></i> تعديل: {{ $sm->title }}</h1></div>
    @if($errors->any())<div class="alert alert-error">@foreach($errors->all() as $e)<p style="margin:0;">{{ $e }}</p>@endforeach</div>@endif
    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color);">
        <form method="POST" action="{{ route('wesal.meetings.staff-meetings.update', $sm) }}" id="edit-staff-meeting-form" style="display: flex; flex-direction: column; gap: 1rem;">
            @csrf @method('PUT')
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem; align-items: end;">
                <div><label class="form-label">العنوان <span style="color:#dc3545">*</span></label><input type="text" name="title" class="form-control" value="{{ old('title', $sm->title) }}" required></div>
                <div><label class="form-label">التاريخ <span style="color:#dc3545">*</span></label><input type="date" name="meeting_date" class="form-control" value="{{ old('meeting_date', $sm->meeting_date?->format('Y-m-d')) }}" required></div>
                <div><label class="form-label">المكان</label><input type="text" name="location" class="form-control" value="{{ old('location', $sm->location) }}"></div>
                <div><label class="form-label">نوع الاجتماع</label><select name="meeting_type_id" class="form-control"><option value="">-- اختر --</option>@foreach($meetingTypes ?? [] as $mt)<option value="{{ $mt->id }}" {{ ($sm->meeting_type_id ?? 0) == $mt->id ? 'selected' : '' }}>{{ $mt->name_ar }}</option>@endforeach</select></div>
                <div><label class="form-label">الحالة</label><select name="status" class="form-control"><option value="scheduled" {{ ($sm->status ?? '') === 'scheduled' ? 'selected' : '' }}>مجدول</option><option value="held" {{ ($sm->status ?? '') === 'held' ? 'selected' : '' }}>منعقد</option><option value="postponed" {{ ($sm->status ?? '') === 'postponed' ? 'selected' : '' }}>مؤجل</option><option value="cancelled" {{ ($sm->status ?? '') === 'cancelled' ? 'selected' : '' }}>ملغي</option></select></div>
            </div>
            <div class="form-group">
                <label class="form-label"><i class="fas fa-users" style="color: var(--primary-color); margin-left: 0.35rem;"></i> اختيار الموظفين المدعوين</label>
                <div class="mail-multiselect" data-name="employee_ids" data-required="0">
                    <div class="mail-multiselect-input-wrap" style="position: relative;">
                        <input type="text" class="mail-multiselect-search" placeholder="ابحث بالاسم واختر الموظفين..." autocomplete="off" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; background: rgba(255,255,255,0.1); color: var(--text-primary); font-size: 0.9rem;">
                        <div class="mail-multiselect-dropdown" style="display: none; position: absolute; top: 100%; right: 0; left: 0; margin-top: 2px; max-height: 220px; overflow-y: auto; background: var(--sidebar-bg); border: 1px solid var(--border-color); border-radius: 8px; z-index: 100; box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
                            @foreach($employeesList ?? [] as $emp)
                                <div class="mail-multiselect-option" data-id="{{ $emp->id }}" data-name="{{ $emp->name_ar }}" style="padding: 0.5rem 0.75rem; cursor: pointer; color: var(--text-primary); font-size: 0.9rem; border-bottom: 1px solid var(--border-color);" tabindex="0">{{ $emp->name_ar }}@if($emp->job_title) <span style="color: var(--text-secondary); font-size: 0.8rem;">— {{ $emp->job_title }}</span>@endif</div>
                            @endforeach
                        </div>
                    </div>
                    <div class="mail-multiselect-tags" style="display: flex; flex-wrap: wrap; gap: 0.35rem; margin-top: 0.5rem;"></div>
                    <div class="mail-multiselect-hidden"></div>
                </div>
                <small style="color: var(--text-secondary); font-size: 0.75rem;">ابحث واختر أسماء الموظفين المدعوين للاجتماع</small>
            </div>
            <div class="form-group">
                <label class="form-label">محاور الاجتماع <span style="color: var(--text-secondary); font-weight: 400;">(قبل بداية الاجتماع)</span></label>
                @include('wesal.partials.meeting-rich-editor', ['name' => 'agenda', 'placeholder' => 'أضف محاور وجدول الأعمال...', 'content' => old('agenda', $sm->agenda ?? '')])
            </div>
            <div class="form-group">
                <label class="form-label">محضر الاجتماع <span style="color: var(--text-secondary); font-weight: 400;">(بعد نهاية الاجتماع)</span></label>
                @include('wesal.partials.meeting-rich-editor', ['name' => 'minutes', 'placeholder' => 'سجّل ملخص الاجتماع والنتائج...', 'content' => old('minutes', $sm->minutes ?? '')])
            </div>
            <div style="grid-column: 1 / -1;"><label class="form-label">ملاحظات</label><textarea name="notes" class="form-control" rows="1">{{ old('notes', $sm->notes) }}</textarea></div>
            <div><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ</button><a href="{{ route('wesal.meetings.show', ['section' => 'staff-meetings']) }}" class="btn btn-secondary">إلغاء</a></div>
        </form>
    </div>
</div>
@php $employeesArray = collect($employeesList ?? [])->map(fn($e) => ['id' => $e->id, 'name' => $e->name_ar])->values()->all(); @endphp
@php $initialEmployeeIds = old('employee_ids', $sm->employees->pluck('id')->toArray()); @endphp
<script>
(function() {
    var options = @json($employeesArray);
    var initialIds = @json($initialEmployeeIds);
    function initMultiselect(container, opts, initialIds) {
        var search = container.querySelector('.mail-multiselect-search');
        var dropdown = container.querySelector('.mail-multiselect-dropdown');
        var optionsAll = container.querySelectorAll('.mail-multiselect-option');
        var tagsEl = container.querySelector('.mail-multiselect-tags');
        var hiddenEl = container.querySelector('.mail-multiselect-hidden');
        var name = container.getAttribute('data-name');
        var selected = {};
        (initialIds || []).forEach(function(id) { var opt = opts.find(function(o) { return o.id == id; }); if (opt) selected[opt.id] = opt.name; });
        function renderHidden() { hiddenEl.innerHTML = ''; Object.keys(selected).forEach(function(id) { var inp = document.createElement('input'); inp.type = 'hidden'; inp.name = name + '[]'; inp.value = id; hiddenEl.appendChild(inp); }); }
        function renderTags() { tagsEl.innerHTML = ''; Object.keys(selected).forEach(function(id) { var tag = document.createElement('span'); tag.className = 'mail-multiselect-tag'; tag.style.cssText = 'display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.5rem; background: rgba(var(--primary-rgb, 95, 179, 142), 0.3); color: var(--text-primary); border-radius: 6px; font-size: 0.8rem;'; tag.textContent = selected[id]; var btn = document.createElement('button'); btn.type = 'button'; btn.innerHTML = '&times;'; btn.style.cssText = 'background: none; border: none; color: inherit; cursor: pointer; padding: 0; font-size: 1rem; line-height: 1;'; btn.addEventListener('click', function(e) { e.preventDefault(); delete selected[id]; renderTags(); renderHidden(); }); tag.appendChild(btn); tagsEl.appendChild(tag); }); }
        function filterList(q) { q = (q || '').trim().toLowerCase(); for (var i = 0; i < optionsAll.length; i++) { var opt = optionsAll[i]; if (selected[opt.getAttribute('data-id')]) { opt.style.display = 'none'; continue; } var nameVal = (opt.getAttribute('data-name') || '').toLowerCase(); opt.style.display = !q || nameVal.indexOf(q) !== -1 ? '' : 'none'; } }
        optionsAll.forEach(function(opt) { opt.addEventListener('click', function() { var id = this.getAttribute('data-id'); var n = this.getAttribute('data-name'); if (selected[id]) delete selected[id]; else selected[id] = n; renderTags(); renderHidden(); filterList(search.value); }); });
        search.addEventListener('focus', function() { dropdown.style.display = 'block'; filterList(search.value); });
        search.addEventListener('input', function() { dropdown.style.display = 'block'; filterList(search.value); });
        search.addEventListener('blur', function() { setTimeout(function() { dropdown.style.display = 'none'; }, 200); });
        dropdown.addEventListener('mousedown', function(e) { e.preventDefault(); });
        renderTags(); renderHidden();
    }
    var container = document.querySelector('#edit-staff-meeting-form .mail-multiselect');
    if (container) initMultiselect(container, options, initialIds);
})();
</script>
@include('wesal.partials.meeting-rich-editor-script')
@endif
