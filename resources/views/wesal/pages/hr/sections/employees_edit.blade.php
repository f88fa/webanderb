<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-user-edit" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            تعديل موظف
        </h1>
        <p class="page-subtitle">تحديث بيانات الموظف: {{ $editEmployee->name_ar ?? $editEmployee->employee_no }}</p>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            @foreach($errors->all() as $err) <p style="margin: 0;">{{ $err }}</p> @endforeach
        </div>
    @endif

    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">بيانات الموظف</h3>
        <form method="POST" action="{{ route('wesal.hr.employees.update', $editEmployee) }}" id="employee-edit-form" enctype="multipart/form-data" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1rem; align-items: end;">
            @csrf
            @method('PUT')
            <div>
                <label class="form-label">رقم الموظف <span style="color: #ff8a80;">*</span></label>
                <input type="text" name="employee_no" class="form-control" value="{{ old('employee_no', $editEmployee->employee_no) }}" required>
            </div>
            <div>
                <label class="form-label">الاسم بالعربي <span style="color: #ff8a80;">*</span></label>
                <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $editEmployee->name_ar) }}" required>
            </div>
            <div>
                <label class="form-label">الاسم بالإنجليزي</label>
                <input type="text" name="name_en" class="form-control" value="{{ old('name_en', $editEmployee->name_en) }}">
            </div>
            <div>
                <label class="form-label">القسم</label>
                <select name="department_id" class="form-control">
                    <option value="">-- اختر --</option>
                    @foreach($departments ?? [] as $d)
                        <option value="{{ $d->id }}" {{ old('department_id', $editEmployee->department_id) == $d->id ? 'selected' : '' }}>{{ $d->name_ar }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">المدير المباشر</label>
                <select name="direct_manager_id" class="form-control">
                    <option value="">-- لا يوجد --</option>
                    @foreach($employeesForManager ?? [] as $m)
                        <option value="{{ $m->id }}" {{ old('direct_manager_id', $editEmployee->direct_manager_id) == $m->id ? 'selected' : '' }}>{{ $m->name_ar }} ({{ $m->employee_no }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">البريد</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $editEmployee->email) }}">
            </div>
            <div>
                <label class="form-label">الجوال</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $editEmployee->phone) }}">
            </div>
            <div>
                <label class="form-label">رقم الهوية</label>
                <input type="text" name="national_id" class="form-control" value="{{ old('national_id', $editEmployee->national_id) }}">
            </div>
            <div>
                <label class="form-label">تاريخ التعيين</label>
                <input type="date" name="hire_date" class="form-control" value="{{ old('hire_date', $editEmployee->hire_date?->format('Y-m-d')) }}">
            </div>
            <div>
                <label class="form-label">المسمى الوظيفي</label>
                <input type="text" name="job_title" class="form-control" value="{{ old('job_title', $editEmployee->job_title) }}">
            </div>
            <div>
                <label class="form-label">الراتب الأساسي</label>
                <input type="number" name="base_salary" class="form-control" value="{{ old('base_salary', $editEmployee->base_salary) }}" step="0.01" min="0">
            </div>
            <div>
                <label class="form-label">الحالة</label>
                <select name="status" class="form-control">
                    <option value="active" {{ old('status', $editEmployee->status) === 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="left" {{ old('status', $editEmployee->status) === 'left' ? 'selected' : '' }}>منتهي</option>
                    <option value="suspended" {{ old('status', $editEmployee->status) === 'suspended' ? 'selected' : '' }}>موقوف</option>
                </select>
            </div>

            {{-- التوقيع الإلكتروني (للمديرين وأعضاء مجلس الإدارة — يظهر عند الموافقة على الطلبات) --}}
            <div style="grid-column: 1 / -1; margin-top: 0.5rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                <h4 style="color: var(--text-primary); margin-bottom: 0.75rem;"><i class="fas fa-signature" style="margin-left: 0.35rem; color: var(--primary-color);"></i> التوقيع الإلكتروني</h4>
                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.75rem;">يُستخدم عند الموافقة على الطلبات (مثل طلب الصرف) ويظهر اسمك والمسمى والتوقيع على السندات.</p>
                @if($editEmployee->signature_path)
                    <div style="margin-bottom: 0.75rem;">
                        <span style="color: var(--text-secondary); font-size: 0.85rem;">التوقيع الحالي:</span>
                        <img src="{{ $editEmployee->signature_url }}" alt="توقيع" style="max-height: 60px; max-width: 180px; object-fit: contain; border: 1px solid var(--border-color); border-radius: 4px; padding: 4px; background: #fff;">
                    </div>
                @endif
                <div>
                    <label class="form-label">{{ $editEmployee->signature_path ? 'استبدال التوقيع' : 'رفع التوقيع' }}</label>
                    <input type="file" name="signature" class="form-control" accept="image/*,.png,.jpg,.jpeg,.gif,.webp" style="max-width: 320px;">
                    <small style="color: var(--text-secondary); font-size: 0.8rem;">صورة واضحة للتوقيع (PNG, JPG). الحجم الأقصى 2 ميجا.</small>
                </div>
            </div>

            {{-- ربط بحساب مستخدم --}}
            @php
                $currentLink = $editEmployee->user_id ? 'existing' : 'none';
                $currentLink = old('link_user', $currentLink);
            @endphp
            <div style="grid-column: 1 / -1; margin-top: 0.5rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                <h4 style="color: var(--text-primary); margin-bottom: 0.75rem;"><i class="fas fa-link" style="margin-left: 0.35rem; color: var(--primary-color);"></i> ربط بحساب مستخدم</h4>
                <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-start;">
                    <div>
                        <label class="form-label">الخيار</label>
                        <select name="link_user" id="link-user-select" class="form-control" style="min-width: 180px;">
                            <option value="none" {{ $currentLink === 'none' ? 'selected' : '' }}>بدون ربط</option>
                            <option value="existing" {{ $currentLink === 'existing' ? 'selected' : '' }}>ربط بمستخدم موجود</option>
                            <option value="create" {{ $currentLink === 'create' ? 'selected' : '' }}>إنشاء مستخدم جديد</option>
                        </select>
                    </div>
                    <div id="user-existing-wrap" style="display: {{ $currentLink === 'existing' ? 'block' : 'none' }};">
                        <label class="form-label">اختر المستخدم</label>
                        <select name="user_id" class="form-control" style="min-width: 220px;">
                            <option value="">-- اختر --</option>
                            @foreach($users ?? [] as $u)
                                <option value="{{ $u->id }}" {{ old('user_id', $editEmployee->user_id) == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="user-create-wrap" style="display: {{ $currentLink === 'create' ? 'flex' : 'none' }}; flex-wrap: wrap; gap: 1rem; align-items: end;">
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
                <div id="roles-wrap" style="margin-top: 1rem; display: {{ in_array($currentLink, ['existing','create']) ? 'block' : 'none' }};">
                    <label class="form-label" style="margin-bottom: 0.5rem;">الأدوار (الصلاحيات)</label>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.75rem 1.5rem;">
                        @foreach($roles as $role)
                            <label style="display: flex; align-items: center; gap: 0.35rem; cursor: pointer; color: var(--text-secondary);">
                                <input type="checkbox" name="roles[]" value="{{ $role->name }}" {{ in_array($role->name, old('roles', $editEmployee->user?->getRoleNames()->toArray() ?? [])) ? 'checked' : '' }}>
                                <span>{{ $role->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> حفظ التعديلات
                </button>
                <a href="{{ route('wesal.hr.show', ['section' => 'employees']) }}" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>

    {{-- رصيد الإجازات السنوية --}}
    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;"><i class="fas fa-wallet" style="color: var(--primary-color); margin-left: 0.35rem;"></i> رصيد الإجازات السنوية</h3>
        <form method="GET" action="{{ route('wesal.hr.employees.edit', $editEmployee) }}" style="margin-bottom: 1rem;">
            <label class="form-label">السنة الميلادية</label>
            <select name="balance_year" class="form-control" style="max-width: 150px; display: inline-block;" onchange="this.form.submit()">
                @for($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" {{ ($balanceYear ?? now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </form>
        @if(isset($leaveTypes) && $leaveTypes->count() > 0)
            <div class="table-container">
                <table style="direction: rtl;">
                    <thead><tr><th>نوع الإجازة</th><th style="text-align: center;">الرصيد السنوي</th><th style="text-align: center;">المستخدم</th><th style="text-align: center;">المتبقي</th></tr></thead>
                    <tbody>
                        @foreach($leaveTypes as $lt)
                            @php
                                $lb = $editEmployee->leaveBalances->first(fn($b) => $b->leave_type_id == $lt->id && $b->year == ($balanceYear ?? now()->year));
                                $balance = $lb ? (float)$lb->balance : (float)$lt->days_per_year;
                                $used = $lb ? (float)$lb->used : 0;
                                $remaining = $balance - $used;
                            @endphp
                            <tr>
                                <td>{{ $lt->name_ar }}</td>
                                <td style="text-align: center;">{{ $balance }}</td>
                                <td style="text-align: center;">{{ $used }}</td>
                                <td style="text-align: center;"><strong>{{ $remaining }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 0.75rem;">* الرصيد ينقص تلقائياً عند الموافقة على طلب إجازة.</p>
        @else
            <p style="color: var(--text-secondary);">لا توجد أنواع إجازات معرّفة. <a href="{{ route('wesal.hr.show', ['section' => 'leave-types']) }}" style="color: var(--primary-color);">إضافة أنواع الإجازات</a></p>
        @endif
    </div>

    {{-- سجل الإجازات --}}
    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color);">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;"><i class="fas fa-history" style="color: var(--primary-color); margin-left: 0.35rem;"></i> سجل الإجازات</h3>
        @if($editEmployee->leaveRequests && $editEmployee->leaveRequests->count() > 0)
            <div class="table-container">
                <table style="direction: rtl;">
                    <thead><tr><th>نوع الإجازة</th><th>من</th><th>إلى</th><th style="text-align: center;">الأيام</th><th>الحالة</th><th>الموافق / الرافض</th><th>تاريخ الطلب</th></tr></thead>
                    <tbody>
                        @foreach($editEmployee->leaveRequests as $req)
                        <tr style="cursor: pointer;" onclick="window.location='{{ route('wesal.hr.leave.show', $req) }}'">
                            <td><a href="{{ route('wesal.hr.leave.show', $req) }}" style="color: inherit; text-decoration: none;">{{ $req->leaveType->name_ar ?? '-' }}</a></td>
                            <td>{{ $req->start_date->format('Y-m-d') }}</td>
                            <td>{{ $req->end_date->format('Y-m-d') }}</td>
                            <td style="text-align: center;">{{ $req->days }}</td>
                            <td>@if($req->status === 'pending')<span style="color: #ff9800;">قيد الانتظار</span>@elseif($req->status === 'approved')<span style="color: #4caf50;">معتمد</span>@else<span style="color: #f44336;">مرفوض</span>@endif</td>
                            <td>
                                @if($req->status === 'approved')
                                    <span style="color: #4caf50;">وافق {{ $req->approvedByUser->name ?? '-' }}</span>
                                @elseif($req->status === 'rejected')
                                    <span style="color: #f44336;">رفض {{ $req->approvedByUser->name ?? '-' }}</span>
                                    @if(!empty($req->rejection_reason))<br><small style="color: var(--text-secondary);">{{ $req->rejection_reason }}</small>@endif
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $req->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="text-align: center; color: var(--text-secondary); padding: 1rem;">لا توجد إجازات مسجلة.</p>
        @endif
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
