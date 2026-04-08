<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-concierge-bell"></i> خدماتنا
        </h1>
        <p class="page-subtitle">إدارة خدمات الموقع</p>
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
                    #services
                </code>
                <a href="{{ url('/#services') }}" target="_blank" class="btn btn-primary" style="text-decoration: none;">
                    <i class="fas fa-external-link-alt"></i> عرض القسم
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

    <!-- إعدادات القسم -->
    <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-bottom: 2rem;">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.75rem;">
            <i class="fas fa-cog" style="color: var(--primary-color);"></i>
            إعدادات قسم الخدمات
        </h2>
        
        <form method="POST" action="{{ route('dashboard.settings.update') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-heading"></i> عنوان القسم
                </label>
                <input type="text" name="section_services_title" class="form-control" 
                       value="{{ old('section_services_title', $settings['section_services_title'] ?? 'خدماتنا المميزة') }}" 
                       placeholder="خدماتنا المميزة">
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    يمكنك تعديله أيضاً من صفحة "إعدادات الموقع"
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-align-right"></i> وصف القسم
                </label>
                <textarea name="section_services_description" class="form-control" rows="3"
                          placeholder="نقدم لكم أفضل الخدمات بجودة عالية">{{ old('section_services_description', $settings['section_services_description'] ?? 'نقدم لكم أفضل الخدمات بجودة عالية') }}</textarea>
            </div>

            <button type="submit" name="save_settings" class="btn btn-primary">
                <i class="fas fa-save"></i> حفظ إعدادات القسم
            </button>
        </form>
    </div>

    <!-- نموذج إضافة/تعديل خدمة -->
    <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-bottom: 2rem;">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
            {{ $editService ? 'تعديل خدمة' : 'إضافة خدمة جديدة' }}
        </h2>
        
        @if($editService)
            <form method="POST" action="{{ route('dashboard.services.update', $editService->id) }}">
                @csrf
                @method('PUT')
        @else
            <form method="POST" action="{{ route('dashboard.services.store') }}">
                @csrf
        @endif
            
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-heading"></i> عنوان الخدمة
                </label>
                <input type="text" name="title" class="form-control" 
                       value="{{ old('title', $editService->title ?? '') }}" 
                       placeholder="أدخل عنوان الخدمة" required>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-align-right"></i> وصف الخدمة
                </label>
                <textarea name="description" class="form-control" rows="4"
                          placeholder="أدخل وصف الخدمة">{{ old('description', $editService->description ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-icons"></i> أيقونة الخدمة
                </label>
                <input type="text" name="icon" class="form-control" 
                       value="{{ old('icon', $editService->icon ?? 'fas fa-star') }}" 
                       placeholder="مثال: fas fa-heart">
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    استخدم أيقونات Font Awesome (مثال: fas fa-heart, fas fa-hand-holding-heart)
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-sort-numeric-up"></i> ترتيب العرض
                </label>
                <input type="number" name="order" class="form-control" 
                       value="{{ old('order', $editService->order ?? 0) }}" 
                       placeholder="0" min="0">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-toggle-on"></i> الحالة
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" 
                           {{ ($editService->is_active ?? true) ? 'checked' : '' }}>
                    <span>نشط</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ $editService ? 'تحديث' : 'إضافة' }}
            </button>
            
            @if($editService)
                <a href="{{ website_page_url('services') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            @endif
        </form>
    </div>

    <!-- قائمة الخدمات -->
    <div class="content-card" style="background: rgba(255, 255, 255, 0.08);">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
            <i class="fas fa-list"></i> قائمة الخدمات
        </h2>

        @if($services->count() > 0)
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الأيقونة</th>
                            <th>العنوان</th>
                            <th>الوصف</th>
                            <th>الترتيب</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($services as $service)
                            <tr>
                                <td>{{ $service->id }}</td>
                                <td>
                                    <i class="{{ $service->icon }}" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                                </td>
                                <td>{{ $service->title }}</td>
                                <td>{{ mb_substr($service->description ?? '', 0, 50) }}{{ mb_strlen($service->description ?? '') > 50 ? '...' : '' }}</td>
                                <td>{{ $service->order }}</td>
                                <td>
                                    @if($service->is_active)
                                        <span class="badge badge-success">نشط</span>
                                    @else
                                        <span class="badge badge-danger">غير نشط</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ website_page_url('services', ['edit' => $service->id]) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> تعديل
                                    </a>
                                    <form method="POST" action="{{ route('dashboard.services.destroy', $service->id) }}" 
                                          style="display: inline-block;" 
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذه الخدمة؟');">
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
                <i class="fas fa-concierge-bell"></i>
                <p>لا توجد خدمات مضافة بعد</p>
            </div>
        @endif
    </div>
</div>

