<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-user-plus" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            إضافة موظف
        </h1>
        <p class="page-subtitle">تسجيل موظف جديد</p>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            @foreach($errors->all() as $err) <p style="margin: 0;">{{ $err }}</p> @endforeach
        </div>
    @endif

    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">بيانات الموظف</h3>
        <form method="POST" action="{{ route('wesal.hr.employees.store') }}" id="employee-create-form" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1rem; align-items: end;">
            @csrf
            <div>
                <label class="form-label">رقم الموظف</label>
                <input type="text" class="form-control" value="يُولَّد تلقائياً (تسلسلي)" readonly style="background: rgba(255,255,255,0.06); color: var(--text-secondary);">
            </div>
            <div>
                <label class="form-label">الاسم بالعربي <span style="color: #ff8a80;">*</span></label>
                <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar') }}" required>
            </div>
            <div>
                <label class="form-label">الاسم بالإنجليزي</label>
                <input type="text" name="name_en" class="form-control" value="{{ old('name_en') }}">
            </div>
            <div>
                <label class="form-label">القسم</label>
                <select name="department_id" class="form-control">
                    <option value="">-- اختر --</option>
                    @foreach($departments ?? [] as $d)
                        <option value="{{ $d->id }}" {{ old('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name_ar }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">المدير المباشر</label>
                <select name="direct_manager_id" class="form-control">
                    <option value="">-- لا يوجد --</option>
                    @foreach($employeesForManager ?? [] as $m)
                        <option value="{{ $m->id }}" {{ old('direct_manager_id') == $m->id ? 'selected' : '' }}>{{ $m->name_ar }} ({{ $m->employee_no }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">البريد</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
            </div>
            <div>
                <label class="form-label">الجوال</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
            </div>
            <div>
                <label class="form-label">رقم الهوية</label>
                <input type="text" name="national_id" class="form-control" value="{{ old('national_id') }}">
            </div>
            <div>
                <label class="form-label">تاريخ التعيين</label>
                <input type="date" name="hire_date" class="form-control" value="{{ old('hire_date') }}">
            </div>
            <div>
                <label class="form-label">المسمى الوظيفي</label>
                <input type="text" name="job_title" class="form-control" value="{{ old('job_title') }}">
            </div>
            <div>
                <label class="form-label">الراتب الأساسي</label>
                <input type="number" name="base_salary" class="form-control" value="{{ old('base_salary') }}" step="0.01" min="0">
            </div>

            {{-- ربط بحساب مستخدم --}}
            <div style="grid-column: 1 / -1; margin-top: 0.5rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                <h4 style="color: var(--text-primary); margin-bottom: 0.75rem;"><i class="fas fa-link" style="margin-left: 0.35rem; color: var(--primary-color);"></i> ربط بحساب مستخدم</h4>
                <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-start;">
                    <div>
                        <label class="form-label">الخيار</label>
                        <select name="link_user" id="link-user-select" class="form-control" style="min-width: 180px;">
                            <option value="none" {{ old('link_user', 'none') === 'none' ? 'selected' : '' }}>بدون ربط</option>
                            <option value="existing" {{ old('link_user') === 'existing' ? 'selected' : '' }}>ربط بمستخدم موجود</option>
                            <option value="create" {{ old('link_user') === 'create' ? 'selected' : '' }}>إنشاء مستخدم جديد</option>
                        </select>
                    </div>
                    <div id="user-existing-wrap" style="display: {{ old('link_user') === 'existing' ? 'block' : 'none' }};">
                        <label class="form-label">اختر المستخدم</label>
                        <select name="user_id" class="form-control" style="min-width: 220px;">
                            <option value="">-- اختر --</option>
                            @foreach($users ?? [] as $u)
                                <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="user-create-wrap" style="display: {{ old('link_user') === 'create' ? 'flex' : 'none' }}; flex-wrap: wrap; gap: 1rem; align-items: end;">
                        <div>
                            <label class="form-label">اسم المستخدم <span style="color: #ff8a80;">*</span></label>
                            <input type="text" name="new_user_name" class="form-control" value="{{ old('new_user_name') }}" placeholder="الاسم">
                        </div>
                        <div>
                            <label class="form-label">البريد الإلكتروني <span style="color: #ff8a80;">*</span></label>
                            <input type="email" name="new_user_email" class="form-control" value="{{ old('new_user_email') }}" placeholder="email@example.com">
                        </div>
                        <div>
                            <label class="form-label">كلمة المرور</label>
                            <input type="password" name="new_user_password" class="form-control" placeholder="كلمة المرور">
                        </div>
                        <div>
                            <label class="form-label">تأكيد كلمة المرور</label>
                            <input type="password" name="new_user_password_confirmation" class="form-control" placeholder="تأكيد">
                        </div>
                    </div>
                </div>
                @if(!empty($roles ?? []))
                <div id="roles-wrap" style="margin-top: 1rem; display: {{ in_array(old('link_user'), ['existing','create']) ? 'block' : 'none' }};">
                    <label class="form-label" style="margin-bottom: 0.5rem;">الأدوار (الصلاحيات)</label>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.75rem 1.5rem;">
                        @foreach($roles as $role)
                            <label style="display: flex; align-items: center; gap: 0.35rem; cursor: pointer; color: var(--text-secondary);">
                                <input type="checkbox" name="roles[]" value="{{ $role->name }}" {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }}>
                                <span>{{ $role->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> حفظ
                </button>
                <a href="{{ route('wesal.hr.show', ['section' => 'employees']) }}" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
<script>
(function() {
    var sel = document.getElementById('link-user-select');
    var wrapExisting = document.getElementById('user-existing-wrap');
    var wrapCreate = document.getElementById('user-create-wrap');
    var wrapRoles = document.getElementById('roles-wrap');
    function toggle() {
        var v = sel ? sel.value : 'none';
        if (wrapExisting) wrapExisting.style.display = v === 'existing' ? 'block' : 'none';
        if (wrapCreate) wrapCreate.style.display = v === 'create' ? 'flex' : 'none';
        if (wrapRoles) wrapRoles.style.display = (v === 'existing' || v === 'create') ? 'block' : 'none';
    }
    if (sel) sel.addEventListener('change', toggle);
    toggle();
})();
</script>
