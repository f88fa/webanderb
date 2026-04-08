<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-quote-left"></i> ماذا قالوا عنا
        </h1>
        <p class="page-subtitle">إدارة شهادات العملاء</p>
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
                    #testimonials
                </code>
                <a href="{{ url('/#testimonials') }}" target="_blank" class="btn btn-primary" style="text-decoration: none;">
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

    <!-- نموذج إضافة/تعديل شهادة -->
    <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-bottom: 2rem;">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
            {{ $editTestimonial ? 'تعديل شهادة' : 'إضافة شهادة جديدة' }}
        </h2>
        
        @if($editTestimonial)
            <form method="POST" action="{{ route('dashboard.testimonials.update', $editTestimonial->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
        @else
            <form method="POST" action="{{ route('dashboard.testimonials.store') }}" enctype="multipart/form-data">
                @csrf
        @endif
            
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-user"></i> الاسم
                </label>
                <input type="text" name="name" class="form-control" 
                       value="{{ old('name', $editTestimonial->name ?? '') }}" 
                       placeholder="أدخل اسم صاحب الشهادة" required>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-align-right"></i> نص الشهادة
                </label>
                <textarea name="text" class="form-control" rows="5"
                          placeholder="أدخل نص الشهادة" required>{{ old('text', $editTestimonial->text ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-image"></i> الصورة
                </label>
                @if($editTestimonial && !empty($editTestimonial->image) && strpos($editTestimonial->image, 'storage/') !== false)
                    <div style="margin-bottom: 1rem;">
                        <img src="{{ image_asset_url($editTestimonial->image) }}" alt="الصورة الحالية" 
                             style="max-width: 150px; max-height: 150px; border-radius: 50%; border: 3px solid rgba(255, 255, 255, 0.2); object-fit: cover;">
                    </div>
                @endif
                <input type="file" name="image" class="form-control" accept="image/*">
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    اختر صورة صاحب الشهادة (JPG, PNG, GIF, SVG) - حجم أقصى 10MB (اختياري - سيتم استخدام أيقونة افتراضية إذا لم يتم رفع صورة)
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-sort-numeric-up"></i> ترتيب العرض
                </label>
                <input type="number" name="order" class="form-control" 
                       value="{{ old('order', $editTestimonial->order ?? 0) }}" 
                       placeholder="0" min="0">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-toggle-on"></i> الحالة
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" 
                           {{ ($editTestimonial->is_active ?? true) ? 'checked' : '' }}>
                    <span>نشط</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ $editTestimonial ? 'تحديث' : 'إضافة' }}
            </button>
            
            @if($editTestimonial)
                <a href="{{ website_page_url('testimonials') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            @endif
        </form>
    </div>

    <!-- قائمة الشهادات -->
    <div class="content-card" style="background: rgba(255, 255, 255, 0.08);">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
            <i class="fas fa-list"></i> قائمة الشهادات
        </h2>

        @if($testimonials->count() > 0)
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الصورة</th>
                            <th>الاسم</th>
                            <th>النص</th>
                            <th>الترتيب</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($testimonials as $testimonial)
                            <tr>
                                <td>{{ $testimonial->id }}</td>
                                <td>
                                    @if($testimonial->image && strpos($testimonial->image, 'storage/') !== false)
                                        <img src="{{ image_asset_url($testimonial->image) }}" alt="{{ $testimonial->name }}" 
                                             style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255, 255, 255, 0.2);">
                                    @else
                                        <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(95, 179, 142, 0.2); display: flex; align-items: center; justify-content: center; border: 2px solid rgba(95, 179, 142, 0.3);">
                                            <i class="fas fa-user" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $testimonial->name }}</td>
                                <td>
                                    <span style="color: rgba(255, 255, 255, 0.7);">
                                        {{ mb_substr($testimonial->text, 0, 80) }}{{ mb_strlen($testimonial->text) > 80 ? '...' : '' }}
                                    </span>
                                </td>
                                <td>{{ $testimonial->order }}</td>
                                <td>
                                    @if($testimonial->is_active)
                                        <span class="badge badge-success">نشط</span>
                                    @else
                                        <span class="badge badge-danger">غير نشط</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ website_page_url('testimonials', ['edit' => $testimonial->id]) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> تعديل
                                    </a>
                                    <form method="POST" action="{{ route('dashboard.testimonials.destroy', $testimonial->id) }}" 
                                          style="display: inline-block;" 
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذه الشهادة؟');">
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
                <i class="fas fa-quote-left"></i>
                <p>لا توجد شهادات مضافة بعد</p>
            </div>
        @endif
    </div>
</div>

