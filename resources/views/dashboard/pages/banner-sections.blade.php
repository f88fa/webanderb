<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-image"></i> أقسام البانر
        </h1>
        <p class="page-subtitle">إدارة أقسام البانر (صورة ورابط)</p>
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

    <!-- نموذج إضافة/تعديل قسم بانر -->
    <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-bottom: 2rem;">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
            {{ $editBanner ? 'تعديل قسم بانر' : 'إضافة قسم بانر جديد' }}
        </h2>
        
        @if($editBanner)
            <form method="POST" action="{{ route('dashboard.banner-sections.update', $editBanner->id) }}" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')
        @else
            <form method="POST" action="{{ route('dashboard.banner-sections.store') }}" enctype="multipart/form-data" novalidate>
                @csrf
        @endif
            
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-heading"></i> عنوان القسم (اختياري)
                </label>
                <input type="text" name="title" class="form-control" 
                       value="{{ old('title', $editBanner->title ?? '') }}" 
                       placeholder="أدخل عنوان القسم">
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    العنوان اختياري - يمكنك تركه فارغاً
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-image"></i> الصورة
                </label>
                @if($editBanner && !empty($editBanner->image) && strpos($editBanner->image, 'storage/') !== false)
                    <div style="margin-bottom: 1rem;">
                        <img src="{{ image_asset_url($editBanner->image) }}" alt="الصورة الحالية" 
                             style="max-width: 400px; max-height: 250px; border-radius: 12px; border: 2px solid rgba(255, 255, 255, 0.2); object-fit: cover;">
                    </div>
                @endif
                <input type="file" name="image" class="form-control" accept="image/*" id="banner-image-input">
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">صورة أو فيديو مرفوع أو رابط يوتيوب — واحد على الأقل</small>
                <div style="background: rgba(95, 179, 142, 0.1); border: 1px solid rgba(95, 179, 142, 0.3); border-radius: 8px; padding: 1rem; margin-top: 0.75rem;">
                    <div style="display: flex; align-items: start; gap: 0.75rem; margin-bottom: 0.75rem;">
                        <i class="fas fa-info-circle" style="color: var(--primary-color); font-size: 1.1rem; margin-top: 0.2rem;"></i>
                        <div style="flex: 1;">
                            <strong style="color: rgba(255, 255, 255, 0.9); display: block; margin-bottom: 0.5rem;">تعليمات رفع الصورة:</strong>
                            <ul style="color: rgba(255, 255, 255, 0.8); margin: 0; padding-right: 1.25rem; line-height: 1.8;">
                                <li><strong>المقاس المناسب:</strong> 1920x1080 بكسل (أو نسبة 16:9)</li>
                                <li><strong>الصيغ المدعومة:</strong> JPG, PNG, GIF, SVG</li>
                                <li><strong>الحجم الأقصى:</strong> 10MB</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-video"></i> الفيديو (اختياري — يظهر تلقائياً مع التشغيل التلقائي)
                </label>
                @if($editBanner && !empty(optional($editBanner)->video))
                    <div style="margin-bottom: 1rem;">
                        <video src="{{ image_asset_url($editBanner->video) }}" controls style="max-width: 100%; max-height: 200px; border-radius: 12px; border: 2px solid rgba(255, 255, 255, 0.2);" preload="metadata"></video>
                        <label style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem; cursor: pointer; color: var(--primary-color);">
                            <input type="checkbox" name="video_remove" value="1">
                            <span>حذف الفيديو الحالي</span>
                        </label>
                    </div>
                @endif
                <input type="file" name="video" class="form-control" accept="video/mp4,video/webm,video/ogg,video/*">
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">إن وُجد فيديو يُعرض بدل الصورة مع تشغيل تلقائي (بدون صوت). الصيغ: MP4, WebM, OGG. الحجم الأقصى: 20 ميجا (لتفادي خطأ الذاكرة).</small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fab fa-youtube" style="color: #ff0000;"></i> رابط يوتيوب (بدون رفع — تشغيل تلقائي)
                </label>
                @if($editBanner && !empty(optional($editBanner)->video_url))
                    <div style="margin-bottom: 1rem;">
                        <p style="color: var(--text-secondary); margin: 0 0 0.5rem;">الرابط الحالي: {{ Str::limit($editBanner->video_url, 50) }}</p>
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--primary-color);">
                            <input type="checkbox" name="video_url_remove" value="1">
                            <span>إزالة رابط يوتيوب</span>
                        </label>
                    </div>
                @endif
                <input type="text" name="video_url" class="form-control" 
                       value="{{ old('video_url', $editBanner->video_url ?? '') }}" 
                       placeholder="https://www.youtube.com/watch?v=... أو https://youtu.be/...">
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">يمكنك لصق رابط فيديو يوتيوب وسيعمل تلقائياً بدون رفع ملف. أولوية العرض: يوتيوب ثم الفيديو المرفوع ثم الصورة.</small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-link"></i> الرابط (اختياري)
                </label>
                <input type="url" name="link" class="form-control" 
                       value="{{ old('link', $editBanner->link ?? '') }}" 
                       placeholder="https://example.com">
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    رابط يتم فتحه عند النقر على الصورة
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-sort-numeric-up"></i> ترتيب العرض
                </label>
                <input type="number" name="order" class="form-control" 
                       value="{{ old('order', $editBanner->order ?? 0) }}" 
                       placeholder="0" min="0">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-palette"></i> نوع الخلفية
                </label>
                <div style="display: flex; gap: 1rem; margin-top: 0.75rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 0.75rem 1.5rem; border-radius: 8px; background: rgba(255, 255, 255, 0.05); border: 2px solid rgba(255, 255, 255, 0.1); transition: all 0.3s ease; flex: 1;">
                        <input type="radio" name="background_type" value="white" 
                               {{ old('background_type', $editBanner->background_type ?? 'white') == 'white' ? 'checked' : '' }}
                               style="width: 18px; height: 18px; cursor: pointer;">
                        <span style="color: rgba(255, 255, 255, 0.9); font-weight: 500;">
                            <i class="fas fa-square" style="color: white; margin-left: 0.5rem;"></i>
                            خلفية بيضاء (مثل قسم من نحن)
                        </span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 0.75rem 1.5rem; border-radius: 8px; background: rgba(255, 255, 255, 0.05); border: 2px solid rgba(255, 255, 255, 0.1); transition: all 0.3s ease; flex: 1;">
                        <input type="radio" name="background_type" value="site" 
                               {{ old('background_type', $editBanner->background_type ?? 'white') == 'site' ? 'checked' : '' }}
                               style="width: 18px; height: 18px; cursor: pointer;">
                        <span style="color: rgba(255, 255, 255, 0.9); font-weight: 500;">
                            <i class="fas fa-globe" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
                            خلفية الموقع (الخلفية العامة)
                        </span>
                    </label>
                </div>
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    اختر نوع خلفية القسم - أبيض مثل قسم "من نحن" أو خلفية الموقع العامة
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-toggle-on"></i> الحالة
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" 
                           {{ ($editBanner->is_active ?? true) ? 'checked' : '' }}>
                    <span>نشط</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ $editBanner ? 'تحديث' : 'إضافة' }}
            </button>
            
            @if($editBanner)
                <a href="{{ website_page_url('banner-sections') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            @endif
        </form>
    </div>

    <!-- قائمة أقسام البانر -->
    <div class="content-card" style="background: rgba(255, 255, 255, 0.08);">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
            <i class="fas fa-list"></i> قائمة أقسام البانر
        </h2>

        @if($banners->count() > 0)
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
                        @foreach($banners as $banner)
                            <tr>
                                <td>{{ $banner->id }}</td>
                                <td>
                                    @if($banner->image && strpos($banner->image, 'storage/') !== false)
                                        <img src="{{ image_asset_url($banner->image) }}" alt="{{ $banner->title }}" 
                                             style="width: 120px; height: 80px; border-radius: 8px; object-fit: cover; border: 1px solid rgba(255, 255, 255, 0.2);">
                                    @else
                                        <span style="color: rgba(255, 255, 255, 0.5);">لا يوجد</span>
                                    @endif
                                </td>
                                <td>{{ $banner->title }}</td>
                                <td>
                                    @if($banner->link)
                                        <a href="{{ $banner->link }}" target="_blank" style="color: var(--primary-color);">
                                            {{ mb_substr($banner->link, 0, 30) }}{{ mb_strlen($banner->link) > 30 ? '...' : '' }}
                                        </a>
                                    @else
                                        <span style="color: rgba(255, 255, 255, 0.5);">-</span>
                                    @endif
                                </td>
                                <td>{{ $banner->order }}</td>
                                <td>
                                    @if($banner->is_active)
                                        <span class="badge badge-success">نشط</span>
                                    @else
                                        <span class="badge badge-danger">غير نشط</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ website_page_url('banner-sections', ['edit' => $banner->id]) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> تعديل
                                    </a>
                                    <form method="POST" action="{{ route('dashboard.banner-sections.destroy', $banner->id) }}" 
                                          style="display: inline-block;" 
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا القسم؟');">
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
                <i class="fas fa-image"></i>
                <p>لا توجد أقسام بانر مضافة بعد</p>
            </div>
        @endif
    </div>
</div>

