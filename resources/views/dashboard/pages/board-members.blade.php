<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-users"></i> مجلس الإدارة
        </h1>
        <p class="page-subtitle">إدارة أعضاء مجلس الإدارة</p>
    </div>

    <!-- رابط الصفحة -->
    <div class="content-card" style="background: linear-gradient(135deg, rgba(95, 179, 142, 0.2) 0%, rgba(31, 107, 79, 0.2) 100%); border: 2px solid rgba(95, 179, 142, 0.3); margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h3 style="color: var(--text-primary); margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-link" style="color: var(--primary-color);"></i>
                    رابط الصفحة المباشر
                </h3>
                <p style="color: var(--text-secondary); margin: 0; font-size: 0.95rem;">
                    استخدم هذا الرابط لإضافة الصفحة في القائمة العلوية
                </p>
            </div>
            <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                <code style="background: rgba(0, 0, 0, 0.3); padding: 0.75rem 1.5rem; border-radius: 8px; color: var(--primary-color); font-size: 1.1rem; font-weight: 600; border: 1px solid rgba(95, 179, 142, 0.3);">
                    /board-members
                </code>
                <a href="{{ url('/board-members') }}" target="_blank" class="btn btn-primary" style="text-decoration: none;">
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

    <!-- نموذج إضافة/تعديل عضو -->
    <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-bottom: 2rem;">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
            {{ $editBoardMember ? 'تعديل عضو' : 'إضافة عضو جديد' }}
        </h2>
        
        @if($editBoardMember)
            <form method="POST" action="{{ route('dashboard.board-members.update', $editBoardMember->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
        @else
            <form method="POST" action="{{ route('dashboard.board-members.store') }}" enctype="multipart/form-data">
                @csrf
        @endif
            
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-user"></i> الاسم
                </label>
                <input type="text" name="name" class="form-control" 
                       value="{{ old('name', $editBoardMember->name ?? '') }}" 
                       placeholder="أدخل اسم العضو" required>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-briefcase"></i> المنصب
                </label>
                <input type="text" name="position" class="form-control" 
                       value="{{ old('position', $editBoardMember->position ?? '') }}" 
                       placeholder="أدخل المنصب" required>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-image"></i> الصورة
                </label>
                @if($editBoardMember && !empty($editBoardMember->image) && strpos($editBoardMember->image, 'storage/') !== false)
                    <div style="margin-bottom: 1rem;">
                        <img src="{{ image_asset_url($editBoardMember->image) }}" alt="الصورة الحالية" 
                             style="max-width: 200px; max-height: 200px; border-radius: 50%; border: 2px solid rgba(255, 255, 255, 0.1); object-fit: cover;">
                    </div>
                @endif
                <input type="file" name="image" class="form-control" accept="image/*" {{ $editBoardMember ? '' : 'required' }}>
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    اختر صورة العضو من جهازك (JPG, PNG, GIF, SVG) - يفضل صورة مربعة (حجم أقصى 10MB)
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-sort-numeric-up"></i> ترتيب العرض
                </label>
                <input type="number" name="order" class="form-control" 
                       value="{{ old('order', $editBoardMember->order ?? 0) }}" 
                       placeholder="0" min="0">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-toggle-on"></i> الحالة
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" 
                           {{ ($editBoardMember->is_active ?? true) ? 'checked' : '' }}>
                    <span>نشط</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ $editBoardMember ? 'تحديث' : 'إضافة' }}
            </button>
            
            @if($editBoardMember)
                <a href="{{ website_page_url('board-members') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            @endif
        </form>
    </div>

    <!-- قائمة الأعضاء -->
    <div class="content-card" style="background: rgba(255, 255, 255, 0.08);">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
            <i class="fas fa-list"></i> قائمة الأعضاء
        </h2>

        @if($boardMembers->count() > 0)
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
                        @foreach($boardMembers as $member)
                            <tr>
                                <td>{{ $member->id }}</td>
                                <td>
                                    @if($member->image && strpos($member->image, 'storage/') !== false)
                                        <img src="{{ image_asset_url($member->image) }}" alt="{{ $member->name }}" 
                                             style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255, 255, 255, 0.1);">
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
                                    <a href="{{ website_page_url('board-members', ['edit' => $member->id]) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> تعديل
                                    </a>
                                    <form method="POST" action="{{ route('dashboard.board-members.destroy', $member->id) }}" 
                                          style="display: inline-block;" 
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا العضو؟');">
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
                <i class="fas fa-users"></i>
                <p>لا توجد أعضاء مضافة بعد</p>
            </div>
        @endif
    </div>
</div>

