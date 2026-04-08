<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-file-alt"></i> اللوائح والسياسات
        </h1>
        <p class="page-subtitle">إدارة اللوائح والسياسات والتصنيفات</p>
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
                    /policies
                </code>
                <a href="{{ url('/policies') }}" target="_blank" class="btn btn-primary" style="text-decoration: none;">
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

    <!-- نموذج إضافة/تعديل تصنيف -->
    <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-bottom: 2rem;">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
            {{ $editCategory ? 'تعديل تصنيف' : 'إضافة تصنيف جديد' }}
        </h2>
        
        @if($editCategory)
            <form method="POST" action="{{ route('dashboard.policies.category.update', $editCategory->id) }}">
                @csrf
                @method('PUT')
        @else
            <form method="POST" action="{{ route('dashboard.policies.category.store') }}">
                @csrf
        @endif
            
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-folder"></i> اسم التصنيف
                </label>
                <input type="text" name="name" class="form-control" 
                       value="{{ old('name', $editCategory->name ?? '') }}" 
                       placeholder="أدخل اسم التصنيف" required>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-sort-numeric-up"></i> ترتيب العرض
                </label>
                <input type="number" name="order" class="form-control" 
                       value="{{ old('order', $editCategory->order ?? 0) }}" 
                       placeholder="0" min="0">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-toggle-on"></i> الحالة
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" 
                           {{ ($editCategory->is_active ?? true) ? 'checked' : '' }}>
                    <span>نشط</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ $editCategory ? 'تحديث' : 'إضافة' }}
            </button>
            
            @if($editCategory)
                <a href="{{ website_page_url('policies') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            @endif
        </form>
    </div>

    <!-- نموذج إضافة/تعديل لائحة/سياسة -->
    <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-bottom: 2rem;">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
            {{ $editPolicy ? 'تعديل لائحة/سياسة' : 'إضافة لائحة/سياسة جديدة' }}
        </h2>
        
        @if($editPolicy)
            <form method="POST" action="{{ route('dashboard.policies.policy.update', $editPolicy->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
        @else
            <form method="POST" action="{{ route('dashboard.policies.policy.store') }}" enctype="multipart/form-data">
                @csrf
        @endif
            
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-folder"></i> التصنيف
                </label>
                <select name="category_id" class="form-control" required>
                    <option value="">-- اختر التصنيف --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $editPolicy->category_id ?? '') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-heading"></i> العنوان
                </label>
                <input type="text" name="title" class="form-control" 
                       value="{{ old('title', $editPolicy->title ?? '') }}" 
                       placeholder="أدخل عنوان اللائحة/السياسة" required>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-file-pdf"></i> ملف PDF
                </label>
                @if($editPolicy && !empty($editPolicy->file) && strpos($editPolicy->file, 'storage/') !== false)
                    <div style="margin-bottom: 1rem;">
                        <a href="{{ image_asset_url($editPolicy->file) }}" target="_blank" style="color: var(--primary-color); display: inline-flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-file-pdf"></i> الملف الحالي: {{ basename($editPolicy->file) }}
                        </a>
                    </div>
                @endif
                <input type="file" name="file" class="form-control" accept=".pdf" {{ $editPolicy ? '' : 'required' }}>
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    اختر ملف PDF (حجم أقصى 50MB)
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-sort-numeric-up"></i> ترتيب العرض
                </label>
                <input type="number" name="order" class="form-control" 
                       value="{{ old('order', $editPolicy->order ?? 0) }}" 
                       placeholder="0" min="0">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-toggle-on"></i> الحالة
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" 
                           {{ ($editPolicy->is_active ?? true) ? 'checked' : '' }}>
                    <span>نشط</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ $editPolicy ? 'تحديث' : 'إضافة' }}
            </button>
            
            @if($editPolicy)
                <a href="{{ website_page_url('policies') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            @endif
        </form>
    </div>

    <!-- قائمة التصنيفات واللوائح -->
    <div class="content-card" style="background: rgba(255, 255, 255, 0.08);">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
            <i class="fas fa-list"></i> التصنيفات واللوائح
        </h2>

        @if($categories->count() > 0)
            @foreach($categories as $category)
                <div style="background: rgba(255, 255, 255, 0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h3 style="color: var(--text-primary); margin: 0;">
                            <i class="fas fa-folder"></i> {{ $category->name }}
                            @if(!$category->is_active)
                                <span class="badge badge-danger" style="margin-right: 0.5rem;">غير نشط</span>
                            @endif
                        </h3>
                        <div>
                            <a href="{{ website_page_url('policies', ['edit_category' => $category->id]) }}" 
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> تعديل
                            </a>
                            <form method="POST" action="{{ route('dashboard.policies.category.destroy', $category->id) }}" 
                                  style="display: inline-block;" 
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا التصنيف؟ سيتم حذف جميع اللوائح المرتبطة به.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i> حذف
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    @php
                        $categoryPolicies = $policies->where('category_id', $category->id);
                    @endphp
                    
                    @if($categoryPolicies->count() > 0)
                        <table class="data-table" style="margin-top: 1rem;">
                            <thead>
                                <tr>
                                    <th>العنوان</th>
                                    <th>الملف</th>
                                    <th>الترتيب</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categoryPolicies as $policy)
                                    <tr>
                                        <td>{{ $policy->title }}</td>
                                        <td>
                                            @if($policy->file && strpos($policy->file, 'storage/') !== false)
                                                <a href="{{ image_asset_url($policy->file) }}" target="_blank" style="color: var(--primary-color);">
                                                    <i class="fas fa-file-pdf"></i> عرض الملف
                                                </a>
                                            @else
                                                <span style="color: rgba(255, 255, 255, 0.5);">لا يوجد</span>
                                            @endif
                                        </td>
                                        <td>{{ $policy->order }}</td>
                                        <td>
                                            @if($policy->is_active)
                                                <span class="badge badge-success">نشط</span>
                                            @else
                                                <span class="badge badge-danger">غير نشط</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ website_page_url('policies', ['edit_policy' => $policy->id]) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> تعديل
                                            </a>
                                            <form method="POST" action="{{ route('dashboard.policies.policy.destroy', $policy->id) }}" 
                                                  style="display: inline-block;" 
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذه اللائحة/السياسة؟');">
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
                    @else
                        <p style="color: rgba(255, 255, 255, 0.5); margin: 0;">لا توجد لوائح/سياسات في هذا التصنيف</p>
                    @endif
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <i class="fas fa-file-alt"></i>
                <p>لا توجد تصنيفات مضافة بعد</p>
            </div>
        @endif
    </div>
</div>

