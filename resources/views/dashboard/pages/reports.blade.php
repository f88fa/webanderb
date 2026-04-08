<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-file-alt"></i> التقارير
        </h1>
        <p class="page-subtitle">إدارة تقارير الموقع</p>
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
                    /reports
                </code>
                <a href="{{ url('/reports') }}" target="_blank" class="btn btn-primary" style="text-decoration: none;">
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

    <!-- نموذج إضافة/تعديل تقرير -->
    <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-bottom: 2rem;">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
            {{ $editReport ? 'تعديل تقرير' : 'إضافة تقرير جديد' }}
        </h2>
        
        @if($editReport)
            <form method="POST" action="{{ route('dashboard.reports.update', $editReport->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
        @else
            <form method="POST" action="{{ route('dashboard.reports.store') }}" enctype="multipart/form-data">
                @csrf
        @endif
            
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-heading"></i> عنوان التقرير
                </label>
                <input type="text" name="title" class="form-control" 
                       value="{{ old('title', $editReport->title ?? '') }}" 
                       placeholder="أدخل عنوان التقرير" required>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-image"></i> صورة التقرير
                </label>
                @if($editReport && !empty($editReport->image))
                    <div style="margin-bottom: 1rem;">
                        <img src="{{ image_asset_url($editReport->image) }}" alt="الصورة الحالية" 
                             style="max-width: 300px; max-height: 300px; border-radius: 12px; border: 2px solid rgba(255, 255, 255, 0.2); object-fit: cover;">
                    </div>
                @endif
                <input type="file" name="image" class="form-control" accept="image/*" {{ $editReport ? '' : 'required' }}>
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    اختر صورة التقرير (JPG, PNG, GIF, SVG) - حجم أقصى 10MB<br>
                    <strong style="color: var(--primary-color);">المقاس الموصى به: 1080 × 1080 بكسل (مربع 1:1)</strong>
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-link"></i> رابط التقرير
                </label>
                <input type="text" name="link" class="form-control" 
                       value="{{ old('link', $editReport->link ?? '') }}" 
                       placeholder="أدخل رابط التقرير (PDF أو رابط خارجي)">
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    يمكن أن يكون رابط PDF أو رابط خارجي للتقرير
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-align-right"></i> الوصف (اختياري)
                </label>
                <textarea name="description" class="form-control" rows="3" 
                          placeholder="أدخل وصف التقرير">{{ old('description', $editReport->description ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-sort-numeric-up"></i> ترتيب العرض
                </label>
                <input type="number" name="order" class="form-control" 
                       value="{{ old('order', $editReport->order ?? 0) }}" 
                       placeholder="0" min="0">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-toggle-on"></i> الحالة
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" 
                           {{ ($editReport->is_active ?? true) ? 'checked' : '' }}>
                    <span>نشط</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ $editReport ? 'تحديث' : 'إضافة' }}
            </button>
            
            @if($editReport)
                <a href="{{ website_page_url('reports') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            @endif
        </form>
    </div>

    <!-- قائمة التقارير -->
    <div class="content-card" style="background: rgba(255, 255, 255, 0.08);">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
            <i class="fas fa-list"></i> قائمة التقارير
        </h2>

        @if($reports->count() > 0)
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الصورة</th>
                            <th>العنوان</th>
                            <th>الرابط</th>
                            <th>الترتيب</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                            <tr>
                                <td>{{ $report->id }}</td>
                                <td>
                                    @if($report->image)
                                        <img src="{{ image_asset_url($report->image) }}" alt="{{ $report->title }}" 
                                             style="width: 80px; height: 60px; border-radius: 8px; object-fit: cover; border: 1px solid rgba(255, 255, 255, 0.2);">
                                    @else
                                        <span style="color: rgba(255, 255, 255, 0.5);">لا يوجد</span>
                                    @endif
                                </td>
                                <td>{{ $report->title }}</td>
                                <td>
                                    @if($report->link)
                                        <a href="{{ $report->link }}" target="_blank" style="color: var(--primary-color); text-decoration: none;">
                                            <i class="fas fa-external-link-alt"></i> عرض
                                        </a>
                                    @else
                                        <span style="color: rgba(255, 255, 255, 0.5);">لا يوجد</span>
                                    @endif
                                </td>
                                <td>{{ $report->order }}</td>
                                <td>
                                    @if($report->is_active)
                                        <span class="badge badge-success">نشط</span>
                                    @else
                                        <span class="badge badge-danger">غير نشط</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ website_page_url('reports', ['edit' => $report->id]) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> تعديل
                                    </a>
                                    <form method="POST" action="{{ route('dashboard.reports.destroy', $report->id) }}" 
                                          style="display: inline-block;" 
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا التقرير؟');">
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
                <i class="fas fa-file-alt"></i>
                <p>لا يوجد تقارير مضافة بعد</p>
            </div>
        @endif
    </div>

    <!-- رابط الصفحة الأمامية -->
    <div class="content-card" style="background: rgba(95, 179, 142, 0.1); margin-top: 2rem; padding: 1.5rem; border-radius: 12px; border: 1px solid rgba(95, 179, 142, 0.3);">
        <h3 style="color: var(--primary-color); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.75rem;">
            <i class="fas fa-link"></i> رابط الصفحة الأمامية
        </h3>
        <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
            <input type="text" readonly value="{{ url('/reports') }}" 
                   style="flex: 1; min-width: 300px; padding: 0.75rem; background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 8px; color: var(--text-primary); font-family: monospace;">
            <a href="{{ url('/reports') }}" target="_blank" class="btn btn-primary">
                <i class="fas fa-external-link-alt"></i> فتح الصفحة
            </a>
        </div>
    </div>
</div>

