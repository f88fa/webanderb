@php
    $editableRoles = ($roles ?? collect())->filter(fn($r) => !\App\Services\PermissionsRegistry::isRoleProtected($r->name));
    $rolePermissions = ($roles ?? collect())->mapWithKeys(fn($r) => [$r->id => $r->permissions->pluck('name')->toArray()])->toArray();
    $firstEditable = $editableRoles->first();
    $selectedRoleId = old('selected_role_id', $firstEditable?->id);
    $selectedRole = $selectedRoleId ? ($roles ?? collect())->firstWhere('id', $selectedRoleId) : null;
@endphp
<div class="content-card">
    <div class="page-header" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
        <div>
            <h1 class="page-title">
                <i class="fas fa-key" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
                الأدوار ومصفوفة الصلاحيات
            </h1>
            <p class="page-subtitle">الصلاحيات تُقرأ وتُزامن تلقائياً من إعدادات النظام. اختر الدور ثم حدد الصلاحيات: يمكن تحديد قسم كامل (تحديد الكل) أو تفاصيل كل صلاحية داخل القسم.</p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('wesal.page', ['page' => 'users']) }}" class="btn btn-secondary">
                <i class="fas fa-users"></i> المستخدمين
            </a>
        </div>
    </div>

    @if(session('success'))
        <div style="padding: 0.75rem 1rem; background: rgba(95, 179, 142, 0.2); border: 1px solid var(--primary-color); border-radius: 8px; color: var(--text-primary); margin-bottom: 1rem;">
            <i class="fas fa-check-circle" style="margin-left: 0.5rem;"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="padding: 0.75rem 1rem; background: rgba(229, 115, 115, 0.2); border: 1px solid #e57373; border-radius: 8px; color: #ffabab; margin-bottom: 1rem;">
            <i class="fas fa-exclamation-circle" style="margin-left: 0.5rem;"></i>{{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div style="padding: 0.75rem 1rem; background: rgba(255, 100, 100, 0.15); border: 1px solid #e57373; border-radius: 8px; color: #ffabab; margin-bottom: 1rem;">
            <ul style="margin: 0; padding-right: 1.25rem;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- إضافة دور جديد --}}
    <div style="background: rgba(255,255,255,0.05); padding: 1.25rem; border-radius: 12px; border: 1px solid var(--border-color); margin-bottom: 1.5rem;">
        <h3 style="margin: 0 0 1rem 0; color: var(--text-primary); font-size: 1rem;">
            <i class="fas fa-plus-circle" style="margin-left: 0.35rem; color: var(--primary-color);"></i> إضافة دور جديد
        </h3>
        <form action="{{ route('wesal.roles.store') }}" method="post" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: end;">
            @csrf
            <div>
                <label style="display: block; margin-bottom: 0.35rem; color: var(--text-secondary); font-size: 0.85rem;">اسم الدور (للاتصال الداخلي) <span style="color: #ff8a80;">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required maxlength="100" placeholder="مثال: HRManager" style="width: 180px; padding: 0.5rem 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; background: rgba(0,0,0,0.2); color: var(--text-primary);">
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.35rem; color: var(--text-secondary); font-size: 0.85rem;">اسم الدور بالعربي</label>
                <input type="text" name="name_ar" value="{{ old('name_ar') }}" maxlength="100" placeholder="مثال: مدير الموارد البشرية" style="width: 200px; padding: 0.5rem 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; background: rgba(0,0,0,0.2); color: var(--text-primary);">
            </div>
            <div>
                <button type="submit" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: var(--primary-color); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    <i class="fas fa-plus"></i> إضافة دور
                </button>
            </div>
        </form>
    </div>

    {{-- تعديل صلاحيات دور: اختيار الدور ثم الأقسام مع تحديد الكل --}}
    <div style="background: rgba(255,255,255,0.05); padding: 1.25rem; border-radius: 12px; border: 1px solid var(--border-color); margin-bottom: 1.5rem;">
        <h3 style="margin: 0 0 1rem 0; color: var(--text-primary); font-size: 1rem;">
            <i class="fas fa-edit" style="margin-left: 0.35rem; color: var(--primary-color);"></i> تعديل صلاحيات دور
        </h3>
        <form action="{{ route('wesal.roles.update-permissions') }}" method="post" id="permissions-form">
            @csrf
            <input type="hidden" name="role_id" id="form-role-id" value="{{ $selectedRoleId }}">
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 0.5rem; color: var(--text-secondary); font-size: 0.9rem;">اختر الدور</label>
                <select id="role-select" style="padding: 0.6rem 1rem; border: 1px solid var(--border-color); border-radius: 8px; background: rgba(0,0,0,0.2); color: var(--text-primary); min-width: 260px; font-size: 1rem;">
                    <option value="">— اختر الدور —</option>
                    @foreach($roles ?? [] as $role)
                        @if(!\App\Services\PermissionsRegistry::isRoleProtected($role->name))
                            <option value="{{ $role->id }}" {{ (string)$selectedRoleId === (string)$role->id ? 'selected' : '' }}>
                                {{ \App\Services\PermissionsRegistry::getRoleLabelAr($role) }} ({{ $role->name }})
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div id="permissions-panel" style="display: {{ $selectedRoleId ? 'block' : 'none' }};">
                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1rem;">كل قسم يظهر مع إمكانية <strong>تحديد القسم بالكامل</strong> أو تحديد صلاحيات فردية داخل القسم. الصلاحيات مزامنة تلقائياً من الإعدادات.</p>
                @foreach($groupedPermissions ?? [] as $groupKey => $group)
                    @php $groupSlug = 'g' . $loop->index; @endphp
                    <div class="perm-group perm-section-card" data-group="{{ $groupSlug }}" style="background: rgba(0,0,0,0.15); border-radius: 12px; margin-bottom: 1rem; border: 1px solid var(--border-color); overflow: hidden;">
                        <button type="button" class="perm-section-toggle" data-target="{{ $groupSlug }}" style="width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; padding: 1rem 1.25rem; background: rgba(95, 179, 142, 0.12); border: none; cursor: pointer; color: var(--text-primary); font-size: 1rem; text-align: right;">
                            <i class="fas fa-chevron-down perm-section-icon" data-target="{{ $groupSlug }}" style="transition: transform 0.2s;"></i>
                            <label style="display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; font-weight: 600; color: var(--primary-color); margin: 0;" onclick="event.stopPropagation();">
                                <input type="checkbox" class="group-select-all" data-group="{{ $groupSlug }}" aria-label="تحديد الكل لهذا القسم">
                                <span>تحديد كل القسم — {{ $group['label'] ?? $groupSlug }}</span>
                            </label>
                        </button>
                        <div class="perm-section-body" id="section-{{ $groupSlug }}" style="padding: 1rem 1.25rem; border-top: 1px solid var(--border-color);">
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 0.5rem 1rem;">
                                @foreach($group['permissions'] ?? [] as $permName => $permLabel)
                                    <label style="display: inline-flex; align-items: center; gap: 0.35rem; cursor: pointer; font-size: 0.9rem; color: var(--text-secondary);">
                                        <input type="checkbox" name="permissions[]" value="{{ $permName }}" class="perm-cb" data-group="{{ $groupSlug }}"
                                            {{ $selectedRole && $selectedRole->permissions->contains('name', $permName) ? 'checked' : '' }}>
                                        <span>{{ $permLabel }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
                <div style="margin-top: 1.25rem;">
                    <button type="submit" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.5rem; background: var(--primary-color); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                        <i class="fas fa-save"></i> حفظ صلاحيات هذا الدور
                    </button>
                </div>
            </div>
            @if($editableRoles->isEmpty())
                <p style="color: var(--text-secondary);">لا توجد أدوار قابلة للتعديل (جميع الأدوار محمية). أضف دوراً جديداً أعلاه.</p>
            @endif
        </form>
    </div>

    {{-- قائمة الأدوار --}}
    <div style="margin-top: 1.5rem; background: rgba(255,255,255,0.05); padding: 1.25rem; border-radius: 12px; border: 1px solid var(--border-color);">
        <h3 style="margin: 0 0 1rem 0; color: var(--text-primary); font-size: 1rem;">
            <i class="fas fa-list" style="margin-left: 0.35rem; color: var(--primary-color);"></i> قائمة الأدوار
        </h3>
        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
            @foreach($roles ?? [] as $role)
                <div style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.35rem 0.75rem; background: rgba(95, 179, 142, 0.15); border: 1px solid var(--border-color); border-radius: 8px;">
                    <span style="color: var(--text-primary);">{{ \App\Services\PermissionsRegistry::getRoleLabelAr($role) }}</span>
                    <span style="color: var(--text-secondary); font-size: 0.8rem;">({{ $role->permissions->count() }} صلاحية)</span>
                    @if(!\App\Services\PermissionsRegistry::isRoleProtected($role->name))
                        <form action="{{ route('wesal.roles.destroy', $role) }}" method="post" style="display: inline;" onsubmit="return confirm('حذف الدور «{{ $role->name }}»؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="padding: 0.2rem 0.4rem; background: rgba(229,115,115,0.3); border: 1px solid #e57373; border-radius: 6px; color: #ffabab; cursor: pointer; font-size: 0.75rem;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    @else
                        <span style="font-size: 0.7rem; color: var(--primary-color);">(محمي)</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
(function() {
    window.rolePermissions = @json($rolePermissions);
    var form = document.getElementById('permissions-form');
    var roleSelect = document.getElementById('role-select');
    var formRoleId = document.getElementById('form-role-id');
    var panel = document.getElementById('permissions-panel');
    if (!form || !roleSelect) return;

    function applyRolePermissions(roleId) {
        var perms = (roleId && window.rolePermissions[roleId]) ? window.rolePermissions[roleId] : [];
        document.querySelectorAll('.perm-cb').forEach(function(cb) {
            cb.checked = perms.indexOf(cb.value) !== -1;
        });
        document.querySelectorAll('.group-select-all').forEach(function(cb) {
            var g = cb.dataset.group;
            var groupBoxes = form.querySelectorAll('.perm-cb[data-group="' + g + '"]');
            var allChecked = groupBoxes.length && Array.from(groupBoxes).every(function(b) { return b.checked; });
            cb.checked = allChecked;
        });
    }

    roleSelect.addEventListener('change', function() {
        var id = this.value;
        formRoleId.value = id || '';
        panel.style.display = id ? 'block' : 'none';
        applyRolePermissions(id);
    });

    document.querySelectorAll('.group-select-all').forEach(function(cb) {
        cb.addEventListener('change', function() {
            var g = this.dataset.group;
            form.querySelectorAll('.perm-cb[data-group="' + g + '"]').forEach(function(b) {
                b.checked = cb.checked;
            });
        });
    });

    document.querySelectorAll('.perm-cb').forEach(function(cb) {
        cb.addEventListener('change', function() {
            var g = this.dataset.group;
            var groupBoxes = form.querySelectorAll('.perm-cb[data-group="' + g + '"]');
            var allChecked = groupBoxes.length && Array.from(groupBoxes).every(function(b) { return b.checked; });
            var selectAll = form.querySelector('.group-select-all[data-group="' + g + '"]');
            if (selectAll) selectAll.checked = allChecked;
        });
    });

    form.addEventListener('submit', function(e) {
        if (!formRoleId.value) {
            e.preventDefault();
            alert('يرجى اختيار الدور أولاً.');
            return false;
        }
    });

    if (roleSelect.value) applyRolePermissions(roleSelect.value);

    document.querySelectorAll('.perm-section-toggle').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            if (e.target.tagName === 'INPUT' || e.target.closest('label')) return;
            var targetId = this.dataset.target;
            var body = document.getElementById('section-' + targetId);
            var icon = document.querySelector('.perm-section-icon[data-target="' + targetId + '"]');
            if (body) {
                var isHidden = body.style.display === 'none';
                body.style.display = isHidden ? 'block' : 'none';
                if (icon) icon.style.transform = isHidden ? 'rotate(0deg)' : 'rotate(-90deg)';
            }
        });
    });
})();
</script>
