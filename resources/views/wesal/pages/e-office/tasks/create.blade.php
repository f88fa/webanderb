<div class="content-card" style="padding: 1.5rem;">
    <div class="page-header" style="margin-bottom: 1.5rem;">
        <h1 class="page-title">
            <i class="fas fa-plus-circle" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            مهمة جديدة
        </h1>
        <p class="page-subtitle">إنشاء مهمة وتحديد المسؤولين وموعد الإنجاز</p>
    </div>

    @if($errors->any())
        <div style="margin-bottom: 1rem; padding: 0.75rem 1rem; background: rgba(244,67,54,0.15); color: #ff8a80; border-radius: 8px; border: 1px solid rgba(244,67,54,0.4);">
            <ul style="margin: 0; padding-right: 1.25rem;">
                @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('wesal.e-office.tasks.store') }}" style="max-width: 700px;">
        @csrf
        <div style="background: rgba(255,255,255,0.05); padding: 1.25rem; border-radius: 12px; border: 1px solid var(--border-color); margin-bottom: 1rem;">
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.35rem; color: var(--text-primary); font-weight: 600; font-size: 0.9rem;">عنوان المهمة <span style="color: #ff8a80;">*</span></label>
                <input type="text" name="subject" value="{{ old('subject') }}" required maxlength="255" placeholder="عنوان المهمة" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; background: rgba(0,0,0,0.2); color: var(--text-primary);">
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.35rem; color: var(--text-primary); font-weight: 600; font-size: 0.9rem;">التفاصيل</label>
                <textarea name="description" rows="4" placeholder="تفاصيل المهمة..." style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; background: rgba(0,0,0,0.2); color: var(--text-primary); resize: vertical;">{{ old('description') }}</textarea>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.35rem; color: var(--text-primary); font-weight: 600; font-size: 0.9rem;">تاريخ الإنجاز <span style="color: #ff8a80;">*</span></label>
                    <input type="date" name="due_at" value="{{ old('due_at') }}" required style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; background: rgba(0,0,0,0.2); color: var(--text-primary);">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.35rem; color: var(--text-primary); font-weight: 600; font-size: 0.9rem;">وقت الإنجاز</label>
                    <input type="time" name="due_time" value="{{ old('due_time', '23:59') }}" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; background: rgba(0,0,0,0.2); color: var(--text-primary);">
                </div>
            </div>
        </div>

        <div style="background: rgba(255,255,255,0.05); padding: 1.25rem; border-radius: 12px; border: 1px solid var(--border-color); margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.35rem; color: var(--text-primary); font-weight: 600; font-size: 0.9rem;">المسؤولون عن المهمة <span style="color: #ff8a80;">*</span></label>
            <div class="task-assignees-wrap" data-name="assignees">
                <div class="mail-multiselect" data-name="assignees" data-required="1">
                    <div class="mail-multiselect-input-wrap" style="position: relative;">
                        <input type="text" class="mail-multiselect-search" placeholder="ابحث بالاسم واختر المسؤولين..." autocomplete="off" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; background: rgba(255,255,255,0.1); color: var(--text-primary); font-size: 0.9rem;">
                        <div class="mail-multiselect-dropdown" style="display: none; position: absolute; top: 100%; right: 0; left: 0; margin-top: 2px; max-height: 220px; overflow-y: auto; background: var(--sidebar-bg); border: 1px solid var(--border-color); border-radius: 8px; z-index: 100; box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
                            @foreach($users ?? [] as $u)
                                <div class="mail-multiselect-option" data-id="{{ $u->id }}" data-name="{{ $u->name }}" style="padding: 0.5rem 0.75rem; cursor: pointer; color: var(--text-primary); font-size: 0.9rem; border-bottom: 1px solid var(--border-color);" tabindex="0">{{ $u->name }}</div>
                            @endforeach
                        </div>
                    </div>
                    <div class="mail-multiselect-tags" style="display: flex; flex-wrap: wrap; gap: 0.35rem; margin-top: 0.5rem;"></div>
                    <div class="mail-multiselect-hidden"></div>
                </div>
            </div>
            <small style="color: var(--text-secondary); font-size: 0.75rem;">اختر مستخدمين مسؤولين عن تنفيذ المهمة (يمكن أكثر من واحد)</small>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            <button type="submit" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.5rem; background: var(--primary-color); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                <i class="fas fa-save"></i> إنشاء المهمة
            </button>
            <a href="{{ route('wesal.e-office.tasks.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.5rem; background: rgba(255,255,255,0.1); color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 8px; text-decoration: none; font-weight: 500;">إلغاء</a>
        </div>
    </form>
</div>

<script>
(function() {
    var form = document.querySelector('form[action="{{ route('wesal.e-office.tasks.store') }}"]');
    if (!form) return;
    var wrap = form.querySelector('.mail-multiselect[data-name="assignees"]');
    if (!wrap) return;
    var search = wrap.querySelector('.mail-multiselect-search');
    var dropdown = wrap.querySelector('.mail-multiselect-dropdown');
    var options = wrap.querySelectorAll('.mail-multiselect-option');
    var tagsEl = wrap.querySelector('.mail-multiselect-tags');
    var hiddenEl = wrap.querySelector('.mail-multiselect-hidden');
    var selected = {};
    function renderHidden() {
        hiddenEl.innerHTML = '';
        Object.keys(selected).forEach(function(id) {
            var inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'assignees[]';
            inp.value = id;
            hiddenEl.appendChild(inp);
        });
    }
    function renderTags() {
        tagsEl.innerHTML = '';
        Object.keys(selected).forEach(function(id) {
            var tag = document.createElement('span');
            tag.className = 'mail-multiselect-tag';
            tag.style.cssText = 'display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.5rem; background: rgba(95, 179, 142, 0.3); color: var(--text-primary); border-radius: 6px; font-size: 0.8rem;';
            tag.textContent = selected[id];
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.innerHTML = '&times;';
            btn.style.cssText = 'background: none; border: none; color: inherit; cursor: pointer; padding: 0; font-size: 1rem; line-height: 1;';
            btn.addEventListener('click', function(e) { e.preventDefault(); delete selected[id]; renderTags(); renderHidden(); });
            tag.appendChild(btn);
            tagsEl.appendChild(tag);
        });
    }
    function filterList(q) {
        q = (q || '').trim().toLowerCase();
        options.forEach(function(opt) {
            var name = (opt.getAttribute('data-name') || '').toLowerCase();
            var show = !q || name.indexOf(q) !== -1;
            opt.style.display = selected[opt.getAttribute('data-id')] ? 'none' : (show ? '' : 'none');
        });
    }
    options.forEach(function(opt) {
        opt.addEventListener('click', function() {
            var id = this.getAttribute('data-id');
            var name = this.getAttribute('data-name');
            if (selected[id]) delete selected[id]; else selected[id] = name;
            renderTags();
            renderHidden();
            filterList(search.value);
        });
    });
    search.addEventListener('focus', function() { dropdown.style.display = 'block'; filterList(search.value); });
    search.addEventListener('input', function() { filterList(this.value); dropdown.style.display = 'block'; });
    search.addEventListener('blur', function() { setTimeout(function() { dropdown.style.display = 'none'; }, 200); });
    dropdown.addEventListener('mousedown', function(e) { e.preventDefault(); });
    form.addEventListener('submit', function(e) {
        if (Object.keys(selected).length === 0) {
            e.preventDefault();
            alert('يرجى اختيار مسؤول واحد على الأقل عن المهمة');
        }
    });
})();
</script>
