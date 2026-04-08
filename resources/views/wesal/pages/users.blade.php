<div class="content-card">
    <div class="page-header" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
        <div>
            <h1 class="page-title">
                <i class="fas fa-users-cog"></i> مستخدمين النظام
            </h1>
            <p class="page-subtitle">إدارة المستخدمين وتعيين الأدوار</p>
        </div>
        <a href="{{ route('wesal.page', ['page' => 'roles-permissions']) }}" class="btn btn-secondary">
            <i class="fas fa-key"></i> الأدوار ومصفوفة الصلاحيات
        </a>
    </div>

    @if(session('success'))
        <div style="padding: 0.75rem 1rem; background: rgba(95, 179, 142, 0.2); border: 1px solid var(--primary-color); border-radius: 8px; color: var(--text-primary); margin-bottom: 1rem;">
            <i class="fas fa-check-circle" style="margin-left: 0.5rem;"></i>{{ session('success') }}
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

    {{-- إضافة مستخدم جديد --}}
    <div style="background: rgba(255,255,255,0.05); padding: 1.25rem; border-radius: 12px; border: 1px solid var(--border-color); margin-bottom: 1.5rem;">
        <h2 style="margin: 0 0 1rem 0; font-size: 1rem; color: var(--text-primary);">
            <i class="fas fa-user-plus" style="margin-left: 0.35rem; color: var(--primary-color);"></i> إضافة مستخدم جديد
        </h2>
        <form action="{{ route('wesal.users.store') }}" method="post" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem; align-items: end;">
            @csrf
            <div>
                <label style="display: block; margin-bottom: 0.35rem; color: var(--text-secondary); font-size: 0.85rem;">الاسم <span style="color: #ff8a80;">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required maxlength="255" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; background: rgba(0,0,0,0.2); color: var(--text-primary);">
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.35rem; color: var(--text-secondary); font-size: 0.85rem;">البريد الإلكتروني <span style="color: #ff8a80;">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; background: rgba(0,0,0,0.2); color: var(--text-primary);">
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.35rem; color: var(--text-secondary); font-size: 0.85rem;">كلمة المرور <span style="color: #ff8a80;">*</span></label>
                <input type="password" name="password" required autocomplete="new-password" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; background: rgba(0,0,0,0.2); color: var(--text-primary);">
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.35rem; color: var(--text-secondary); font-size: 0.85rem;">تأكيد كلمة المرور <span style="color: #ff8a80;">*</span></label>
                <input type="password" name="password_confirmation" required autocomplete="new-password" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; background: rgba(0,0,0,0.2); color: var(--text-primary);">
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.35rem; color: var(--text-secondary); font-size: 0.85rem;">الدور</label>
                <select name="role" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; background: rgba(0,0,0,0.2); color: var(--text-primary);">
                    <option value="">— لا دور —</option>
                    @foreach($roles ?? [] as $role)
                        <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: var(--primary-color); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    <i class="fas fa-plus"></i> إضافة
                </button>
            </div>
        </form>
    </div>

    {{-- قائمة المستخدمين --}}
    <div style="overflow-x: auto; margin-top: 1rem;">
        <table style="width: 100%; border-collapse: collapse; background: rgba(255, 255, 255, 0.05); border-radius: 12px; overflow: hidden;">
            <thead>
                <tr style="background: rgba(95, 179, 142, 0.2);">
                    <th style="padding: 1rem 1.25rem; text-align: right; color: var(--text-primary); font-weight: 600;">#</th>
                    <th style="padding: 1rem 1.25rem; text-align: right; color: var(--text-primary); font-weight: 600;">الاسم</th>
                    <th style="padding: 1rem 1.25rem; text-align: right; color: var(--text-primary); font-weight: 600;">البريد الإلكتروني</th>
                    <th style="padding: 1rem 1.25rem; text-align: right; color: var(--text-primary); font-weight: 600;">الأدوار</th>
                    <th style="padding: 1rem 1.25rem; text-align: right; color: var(--text-primary); font-weight: 600;">تاريخ التسجيل</th>
                    <th style="padding: 1rem 1.25rem; text-align: right; color: var(--text-primary); font-weight: 600;">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($users ?? []) as $index => $user)
                    <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                        <td style="padding: 1rem 1.25rem; color: var(--text-secondary);">{{ $index + 1 }}</td>
                        <td style="padding: 1rem 1.25rem; color: var(--text-secondary);">{{ $user->name }}</td>
                        <td style="padding: 1rem 1.25rem; color: var(--text-secondary);">{{ $user->email }}</td>
                        <td style="padding: 1rem 1.25rem; color: var(--text-secondary);">
                            @foreach($user->roles as $role)
                                <span style="display: inline-block; padding: 0.2rem 0.5rem; background: rgba(95, 179, 142, 0.2); border-radius: 6px; font-size: 0.8rem; margin-left: 0.25rem;">{{ $role->name }}</span>
                            @endforeach
                            @if($user->roles->isEmpty())
                                <span style="color: var(--text-secondary); opacity: 0.7;">—</span>
                            @endif
                        </td>
                        <td style="padding: 1rem 1.25rem; color: var(--text-secondary);">{{ $user->created_at?->format('Y-m-d') ?? '-' }}</td>
                        <td style="padding: 1rem 1.25rem;">
                            <button type="button" class="wesal-btn-edit-user" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}" data-user-email="{{ $user->email }}" style="padding: 0.35rem 0.75rem; background: rgba(95, 179, 142, 0.3); border: 1px solid var(--primary-color); border-radius: 6px; color: var(--primary-color); cursor: pointer; font-size: 0.85rem; margin-left: 0.25rem;">
                                <i class="fas fa-edit"></i> تعديل
                            </button>
                            <button type="button" class="wesal-btn-roles-user" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}" data-user-roles="{{ $user->roles->pluck('name')->toJson() }}" style="padding: 0.35rem 0.75rem; background: rgba(95, 179, 142, 0.2); border: 1px solid var(--border-color); border-radius: 6px; color: var(--text-primary); cursor: pointer; font-size: 0.85rem;">
                                <i class="fas fa-key"></i> الأدوار
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 2rem; text-align: center; color: var(--text-secondary);">
                            <i class="fas fa-info-circle" style="margin-left: 0.5rem;"></i>
                            لا يوجد مستخدمين مسجلين
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- نافذة تعديل المستخدم --}}
<div id="wesalEditUserModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 2000; align-items: center; justify-content: center; padding: 1rem;">
    <div style="background: var(--sidebar-bg); border: 1px solid var(--border-color); border-radius: 12px; max-width: 420px; width: 100%; padding: 1.5rem; box-shadow: 0 8px 32px rgba(0,0,0,0.4);">
        <h3 style="margin: 0 0 1rem 0; color: var(--text-primary);"><i class="fas fa-user-edit" style="margin-left: 0.5rem; color: var(--primary-color);"></i> تعديل المستخدم</h3>
        <form id="wesalEditUserForm" method="post" style="display: flex; flex-direction: column; gap: 1rem;">
            @csrf
            @method('PUT')
            <div>
                <label style="display: block; margin-bottom: 0.35rem; color: var(--text-secondary); font-size: 0.85rem;">الاسم</label>
                <input type="text" name="name" id="wesalEditUserName" required style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; background: rgba(0,0,0,0.2); color: var(--text-primary);">
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.35rem; color: var(--text-secondary); font-size: 0.85rem;">البريد الإلكتروني</label>
                <input type="email" name="email" id="wesalEditUserEmail" required style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; background: rgba(0,0,0,0.2); color: var(--text-primary);">
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.35rem; color: var(--text-secondary); font-size: 0.85rem;">كلمة مرور جديدة (اتركها فارغة للإبقاء)</label>
                <input type="password" name="password" id="wesalEditUserPassword" autocomplete="new-password" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; background: rgba(0,0,0,0.2); color: var(--text-primary);">
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.35rem; color: var(--text-secondary); font-size: 0.85rem;">تأكيد كلمة المرور</label>
                <input type="password" name="password_confirmation" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; background: rgba(0,0,0,0.2); color: var(--text-primary);">
            </div>
            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                <button type="button" id="wesalEditUserModalClose" style="padding: 0.5rem 1rem; background: rgba(255,255,255,0.1); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); cursor: pointer;">إلغاء</button>
                <button type="submit" style="padding: 0.5rem 1rem; background: var(--primary-color); border: none; border-radius: 8px; color: white; font-weight: 600; cursor: pointer;">حفظ</button>
            </div>
        </form>
    </div>
</div>

{{-- نافذة إدارة الأدوار --}}
<div id="wesalRolesModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 2000; align-items: center; justify-content: center; padding: 1rem;">
    <div style="background: var(--sidebar-bg); border: 1px solid var(--border-color); border-radius: 12px; max-width: 420px; width: 100%; padding: 1.5rem; box-shadow: 0 8px 32px rgba(0,0,0,0.4);">
        <h3 style="margin: 0 0 1rem 0; color: var(--text-primary);"><i class="fas fa-key" style="margin-left: 0.5rem; color: var(--primary-color);"></i> أدوار المستخدم: <span id="wesalRolesUserName"></span></h3>
        <form id="wesalRolesForm" method="post" style="display: flex; flex-direction: column; gap: 0.75rem;">
            @csrf
            <div style="max-height: 200px; overflow-y: auto;">
                @foreach($roles ?? [] as $role)
                    <label style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0; cursor: pointer;">
                        <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="wesal-role-checkbox">
                        <span style="color: var(--text-primary);">{{ $role->name }}</span>
                    </label>
                @endforeach
            </div>
            <p style="margin: 0; font-size: 0.8rem; color: var(--text-secondary);">الدور يحدد الصلاحيات المعطاة للمستخدم (عبر القائمة الجانبية والمسارات).</p>
            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                <button type="button" id="wesalRolesModalClose" style="padding: 0.5rem 1rem; background: rgba(255,255,255,0.1); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); cursor: pointer;">إلغاء</button>
                <button type="submit" style="padding: 0.5rem 1rem; background: var(--primary-color); border: none; border-radius: 8px; color: white; font-weight: 600; cursor: pointer;">حفظ الأدوار</button>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    var editModal = document.getElementById('wesalEditUserModal');
    var rolesModal = document.getElementById('wesalRolesModal');
    if (!editModal || !rolesModal) return;

    document.querySelectorAll('.wesal-btn-edit-user').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = btn.getAttribute('data-user-id');
            var name = btn.getAttribute('data-user-name');
            var email = btn.getAttribute('data-user-email');
            if (!id) return;
            document.getElementById('wesalEditUserForm').action = '{{ url("wesal/users") }}/' + id;
            document.getElementById('wesalEditUserName').value = name || '';
            document.getElementById('wesalEditUserEmail').value = email || '';
            document.getElementById('wesalEditUserPassword').value = '';
            editModal.style.display = 'flex';
        });
    });

    document.querySelectorAll('.wesal-btn-roles-user').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = btn.getAttribute('data-user-id');
            var name = btn.getAttribute('data-user-name');
            var rolesJson = btn.getAttribute('data-user-roles');
            if (!id) return;
            document.getElementById('wesalRolesForm').action = '{{ url("wesal/users") }}/' + id + '/roles';
            document.getElementById('wesalRolesUserName').textContent = name || '';
            var roles = [];
            try { roles = JSON.parse(rolesJson || '[]'); } catch(e) {}
            document.querySelectorAll('.wesal-role-checkbox').forEach(function(cb) {
                cb.checked = roles.indexOf(cb.value) !== -1;
            });
            rolesModal.style.display = 'flex';
        });
    });

    document.getElementById('wesalEditUserModalClose')?.addEventListener('click', function() { editModal.style.display = 'none'; });
    document.getElementById('wesalRolesModalClose')?.addEventListener('click', function() { rolesModal.style.display = 'none'; });
    editModal.addEventListener('click', function(e) { if (e.target === editModal) editModal.style.display = 'none'; });
    rolesModal.addEventListener('click', function(e) { if (e.target === rolesModal) rolesModal.style.display = 'none'; });
})();
</script>
