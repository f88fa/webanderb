<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-user-tie"></i> الموظفين
        </h1>
        <p class="page-subtitle">إدارة موظفي الموقع</p>
    </div>

    <!-- رابط الصفحة -->
    <div class="content-card" style="background: linear-gradient(135deg, rgba(95, 179, 142, 0.2) 0%, rgba(31, 107, 79, 0.2) 100%); border: 2px solid rgba(95, 179, 142, 0.3); margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h3 style="color: var(--text-primary); margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-link" style="color: var(--primary-color);"></i>
                    رابط القسم المباشر
                </h3>
                <p style="color: var(--text-secondary); margin: 0; font-size: 0.95rem;">
                    استخدم هذا الرابط لإضافة القسم في القائمة العلوية
                </p>
            </div>
            <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                <code style="background: rgba(0, 0, 0, 0.3); padding: 0.75rem 1.5rem; border-radius: 8px; color: var(--primary-color); font-size: 1.1rem; font-weight: 600; border: 1px solid rgba(95, 179, 142, 0.3);">
                    /staff
                </code>
                <a href="{{ url('/staff') }}" target="_blank" class="btn btn-primary" style="text-decoration: none;">
                    <i class="fas fa-external-link-alt"></i> عرض الصفحة
                </a>
            </div>
        </div>
    </div>

    @if(session('success_message'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success_message') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- نموذج إضافة/تعديل موظف -->
    <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-bottom: 2rem;">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
            {{ $editStaff ? 'تعديل موظف' : 'إضافة موظف جديد' }}
        </h2>
        
        @if($editStaff)
            <form method="POST" action="{{ route('dashboard.staff.update', $editStaff->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
        @else
            <form method="POST" action="{{ route('dashboard.staff.store') }}" enctype="multipart/form-data">
                @csrf
        @endif
            
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-user"></i> اسم الموظف
                </label>
                <input type="text" name="name" class="form-control" 
                       value="{{ old('name', $editStaff->name ?? '') }}" 
                       placeholder="أدخل اسم الموظف" required>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-briefcase"></i> المنصب
                </label>
                <input type="text" name="position" class="form-control" 
                       value="{{ old('position', $editStaff->position ?? '') }}" 
                       placeholder="أدخل منصب الموظف" required>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-image"></i> الصورة
                </label>
                @if($editStaff && !empty($editStaff->image))
                    <div style="margin-bottom: 1rem;">
                        <img src="{{ image_asset_url($editStaff->image) }}" alt="الصورة الحالية" 
                             style="max-width: 200px; max-height: 200px; border-radius: 12px; border: 2px solid rgba(255, 255, 255, 0.2); object-fit: cover;">
                    </div>
                @endif
                <input type="file" name="image" class="form-control" accept="image/*" {{ $editStaff ? '' : 'required' }}>
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    اختر صورة الموظف (JPG, PNG, GIF, SVG) - حجم أقصى 10MB
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-sort-numeric-up"></i> ترتيب العرض
                </label>
                <input type="number" name="order" class="form-control" 
                       value="{{ old('order', $editStaff->order ?? 0) }}" 
                       placeholder="0" min="0">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-toggle-on"></i> الحالة
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" 
                           {{ ($editStaff->is_active ?? true) ? 'checked' : '' }}>
                    <span>نشط</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ $editStaff ? 'تحديث' : 'إضافة' }}
            </button>
            
            @if($editStaff)
                <a href="{{ website_page_url('staff') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            @endif
        </form>
    </div>

    <!-- قائمة الموظفين -->
    <div class="content-card" style="background: rgba(255, 255, 255, 0.08);">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
            <i class="fas fa-list"></i> قائمة الموظفين
        </h2>

        @if($staff->count() > 0)
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الصورة</th>
                            <th>الاسم</th>
                            <th>المنصب</th>
                            <th>الترتيب</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staff as $member)
                            <tr>
                                <td>{{ $member->id }}</td>
                                <td>
                                    @if($member->image)
                                        <img src="{{ image_asset_url($member->image) }}" alt="{{ $member->name }}" 
                                             style="width: 60px; height: 60px; border-radius: 8px; object-fit: cover; border: 1px solid rgba(255, 255, 255, 0.2);">
                                    @else
                                        <span style="color: rgba(255, 255, 255, 0.5);">لا يوجد</span>
                                    @endif
                                </td>
                                <td>{{ $member->name }}</td>
                                <td>{{ $member->position }}</td>
                                <td>{{ $member->order }}</td>
                                <td>
                                    @if($member->is_active)
                                        <span class="badge badge-success">نشط</span>
                                    @else
                                        <span class="badge badge-danger">غير نشط</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ website_page_url('staff', ['edit' => $member->id]) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> تعديل
                                    </a>
                                    <form method="POST" action="{{ route('dashboard.staff.destroy', $member->id) }}" 
                                          style="display: inline-block;" 
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا الموظف؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> حذف
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-user-tie"></i>
                <p>لا يوجد موظفين مضافة بعد</p>
            </div>
        @endif
    </div>

    <!-- رابط الصفحة الأمامية -->
    <div class="content-card" style="background: rgba(95, 179, 142, 0.1); margin-top: 2rem; padding: 1.5rem; border-radius: 12px; border: 1px solid rgba(95, 179, 142, 0.3);">
        <h3 style="color: var(--primary-color); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.75rem;">
            <i class="fas fa-link"></i> رابط الصفحة الأمامية
        </h3>
        <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
            <input type="text" readonly value="{{ url('/staff') }}" 
                   style="flex: 1; min-width: 300px; padding: 0.75rem; background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 8px; color: var(--text-primary); font-family: monospace;">
            <a href="{{ url('/staff') }}" target="_blank" class="btn btn-primary">
                <i class="fas fa-external-link-alt"></i> فتح الصفحة
            </a>
        </div>
    </div>
</div>

