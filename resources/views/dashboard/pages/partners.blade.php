<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-handshake"></i> شركاؤنا
        </h1>
        <p class="page-subtitle">إدارة شركاء الموقع</p>
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
                    #partners
                </code>
                <a href="{{ url('/#partners') }}" target="_blank" class="btn btn-primary" style="text-decoration: none;">
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

    <!-- نموذج إضافة/تعديل شريك -->
    <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-bottom: 2rem;">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
            {{ $editPartner ? 'تعديل شريك' : 'إضافة شريك جديد' }}
        </h2>
        
        @if($editPartner)
            <form method="POST" action="{{ route('dashboard.partners.update', $editPartner->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
        @else
            <form method="POST" action="{{ route('dashboard.partners.store') }}" enctype="multipart/form-data">
                @csrf
        @endif
            
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-building"></i> اسم الشريك
                </label>
                <input type="text" name="name" class="form-control" 
                       value="{{ old('name', $editPartner->name ?? '') }}" 
                       placeholder="أدخل اسم الشريك" required>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-image"></i> شعار الشريك
                </label>
                @if($editPartner && !empty($editPartner->logo) && strpos($editPartner->logo, 'storage/') !== false)
                    <div style="margin-bottom: 1rem;">
                        <img src="{{ image_asset_url($editPartner->logo) }}" alt="الشعار الحالي" 
                             style="max-width: 200px; max-height: 100px; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.1); object-fit: contain; background: rgba(255, 255, 255, 0.05); padding: 1rem;">
                    </div>
                @endif
                <input type="file" name="logo" class="form-control" accept="image/*" {{ $editPartner ? '' : 'required' }}>
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    اختر شعار الشريك من جهازك (JPG, PNG, GIF, SVG) - يفضل شفاف الخلفية (حجم أقصى 10MB)
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-link"></i> رابط الموقع (اختياري)
                </label>
                <input type="url" name="website" class="form-control" 
                       value="{{ old('website', $editPartner->website ?? '') }}" 
                       placeholder="https://example.com">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-sort-numeric-up"></i> ترتيب العرض
                </label>
                <input type="number" name="order" class="form-control" 
                       value="{{ old('order', $editPartner->order ?? 0) }}" 
                       placeholder="0" min="0">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-toggle-on"></i> الحالة
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" 
                           {{ ($editPartner->is_active ?? true) ? 'checked' : '' }}>
                    <span>نشط</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ $editPartner ? 'تحديث' : 'إضافة' }}
            </button>
            
            @if($editPartner)
                <a href="{{ website_page_url('partners') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            @endif
        </form>
    </div>

    <!-- قائمة الشركاء -->
    <div class="content-card" style="background: rgba(255, 255, 255, 0.08);">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
            <i class="fas fa-list"></i> قائمة الشركاء
        </h2>

        @if($partners->count() > 0)
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الشعار</th>
                            <th>الاسم</th>
                            <th>الموقع</th>
                            <th>الترتيب</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($partners as $partner)
                            <tr>
                                <td>{{ $partner->id }}</td>
                                <td>
                                    @if($partner->logo && strpos($partner->logo, 'storage/') !== false)
                                        <img src="{{ image_asset_url($partner->logo) }}" alt="{{ $partner->name }}" 
                                             style="max-width: 80px; max-height: 50px; object-fit: contain; background: rgba(255, 255, 255, 0.05); padding: 0.5rem; border-radius: 4px;">
                                    @else
                                        <span style="color: rgba(255, 255, 255, 0.5);">لا يوجد</span>
                                    @endif
                                </td>
                                <td>{{ $partner->name }}</td>
                                <td>
                                    @if($partner->website)
                                        <a href="{{ $partner->website }}" target="_blank" style="color: var(--primary-color);">
                                            {{ mb_substr($partner->website, 0, 30) }}{{ mb_strlen($partner->website) > 30 ? '...' : '' }}
                                        </a>
                                    @else
                                        <span style="color: rgba(255, 255, 255, 0.5);">-</span>
                                    @endif
                                </td>
                                <td>{{ $partner->order }}</td>
                                <td>
                                    @if($partner->is_active)
                                        <span class="badge badge-success">نشط</span>
                                    @else
                                        <span class="badge badge-danger">غير نشط</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ website_page_url('partners', ['edit' => $partner->id]) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> تعديل
                                    </a>
                                    <form method="POST" action="{{ route('dashboard.partners.destroy', $partner->id) }}" 
                                          style="display: inline-block;" 
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا الشريك؟');">
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
                <i class="fas fa-handshake"></i>
                <p>لا توجد شركاء مضافة بعد</p>
            </div>
        @endif
    </div>
</div>

