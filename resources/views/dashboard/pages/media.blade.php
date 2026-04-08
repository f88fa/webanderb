<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-photo-video"></i> المركز الإعلامي
        </h1>
        <p class="page-subtitle">إدارة مقاطع الفيديو ومعرض الصور</p>
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
                    #media
                </code>
                <a href="{{ url('/#media') }}" target="_blank" class="btn btn-primary" style="text-decoration: none;">
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

    <!-- قسم مقاطع اليوتيوب -->
    <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-bottom: 2rem;">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.75rem;">
            <i class="fab fa-youtube" style="color: #FF0000;"></i>
            {{ $editVideo ? 'تعديل فيديو' : 'إضافة فيديو جديد' }}
        </h2>
        
        @if($editVideo)
            <form method="POST" action="{{ route('dashboard.media.videos.update', $editVideo->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
        @else
            <form method="POST" action="{{ route('dashboard.media.videos.store') }}" enctype="multipart/form-data">
                @csrf
        @endif
            
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-heading"></i> عنوان الفيديو
                </label>
                <input type="text" name="title" class="form-control" 
                       value="{{ old('title', $editVideo->title ?? '') }}" 
                       placeholder="أدخل عنوان الفيديو" required>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fab fa-youtube"></i> رابط YouTube
                </label>
                <input type="url" name="youtube_url" class="form-control" 
                       value="{{ old('youtube_url', $editVideo->youtube_url ?? '') }}" 
                       placeholder="https://www.youtube.com/watch?v=..." required>
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    أدخل رابط الفيديو من YouTube
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-image"></i> صورة مصغرة (اختياري)
                </label>
                @if($editVideo && !empty($editVideo->thumbnail) && strpos($editVideo->thumbnail, 'storage/') !== false)
                    <div style="margin-bottom: 1rem;">
                        <img src="{{ image_asset_url($editVideo->thumbnail) }}" alt="الصورة الحالية" 
                             style="max-width: 200px; max-height: 150px; border-radius: 8px; border: 2px solid rgba(255, 255, 255, 0.2); object-fit: cover;">
                    </div>
                @endif
                <input type="file" name="thumbnail" class="form-control" accept="image/*">
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    إذا لم يتم رفع صورة، سيتم استخدام الصورة الافتراضية من YouTube
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-sort-numeric-up"></i> ترتيب العرض
                </label>
                <input type="number" name="order" class="form-control" 
                       value="{{ old('order', $editVideo->order ?? 0) }}" 
                       placeholder="0" min="0">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-toggle-on"></i> الحالة
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" 
                           {{ ($editVideo->is_active ?? true) ? 'checked' : '' }}>
                    <span>نشط</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ $editVideo ? 'تحديث' : 'إضافة' }}
            </button>
            
            @if($editVideo)
                <a href="{{ website_page_url('media') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            @endif
        </form>
    </div>

    <!-- قائمة مقاطع الفيديو -->
    <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-bottom: 2rem;">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
            <i class="fab fa-youtube"></i> قائمة مقاطع الفيديو
        </h2>

        @if($videos->count() > 0)
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
                        @foreach($videos as $video)
                            <tr>
                                <td>{{ $video->id }}</td>
                                <td>
                                    @if($video->thumbnail && strpos($video->thumbnail, 'storage/') !== false)
                                        <img src="{{ image_asset_url($video->thumbnail) }}" alt="{{ $video->title }}" 
                                             style="width: 100px; height: 60px; border-radius: 8px; object-fit: cover; border: 1px solid rgba(255, 255, 255, 0.2);">
                                    @else
                                        <div style="width: 100px; height: 60px; border-radius: 8px; background: rgba(255, 0, 0, 0.2); display: flex; align-items: center; justify-content: center; border: 1px solid rgba(255, 0, 0, 0.3);">
                                            <i class="fab fa-youtube" style="font-size: 1.5rem; color: #FF0000;"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $video->title }}</td>
                                <td>
                                    <a href="{{ $video->youtube_url }}" target="_blank" style="color: var(--primary-color);">
                                        {{ mb_substr($video->youtube_url, 0, 40) }}{{ mb_strlen($video->youtube_url) > 40 ? '...' : '' }}
                                    </a>
                                </td>
                                <td>{{ $video->order }}</td>
                                <td>
                                    @if($video->is_active)
                                        <span class="badge badge-success">نشط</span>
                                    @else
                                        <span class="badge badge-danger">غير نشط</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ website_page_url('media', ['edit_video' => $video->id]) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> تعديل
                                    </a>
                                    <form method="POST" action="{{ route('dashboard.media.videos.destroy', $video->id) }}" 
                                          style="display: inline-block;" 
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا الفيديو؟');">
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
                <i class="fab fa-youtube"></i>
                <p>لا توجد مقاطع فيديو مضافة بعد</p>
            </div>
        @endif
    </div>

    <!-- قسم السلايدر -->
    <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-bottom: 2rem;">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.75rem;">
            <i class="fas fa-images"></i>
            {{ $editSlide ? 'تعديل شريحة' : 'إضافة شريحة جديدة' }}
        </h2>
        
        @if($editSlide)
            <form method="POST" action="{{ route('dashboard.media.slides.update', $editSlide->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
        @else
            <form method="POST" action="{{ route('dashboard.media.slides.store') }}" enctype="multipart/form-data">
                @csrf
        @endif
            
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-tag"></i> نوع الشريحة
                </label>
                <select name="type" class="form-control" id="slideType" required onchange="toggleSlideFields()">
                    <option value="image" {{ old('type', $editSlide->type ?? 'image') == 'image' ? 'selected' : '' }}>صورة</option>
                    <option value="video" {{ old('type', $editSlide->type ?? '') == 'video' ? 'selected' : '' }}>فيديو</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-heading"></i> العنوان (اختياري)
                </label>
                <input type="text" name="title" class="form-control" 
                       value="{{ old('title', $editSlide->title ?? '') }}" 
                       placeholder="أدخل عنوان الشريحة">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-align-right"></i> الوصف (اختياري)
                </label>
                <textarea name="description" class="form-control" rows="3"
                          placeholder="أدخل وصف الشريحة">{{ old('description', $editSlide->description ?? '') }}</textarea>
            </div>

            <!-- حقل الصورة -->
            <div class="form-group" id="imageField">
                <label class="form-label">
                    <i class="fas fa-image"></i> الصورة
                </label>
                @if($editSlide && !empty($editSlide->image) && strpos($editSlide->image, 'storage/') !== false)
                    <div style="margin-bottom: 1rem;">
                        <img src="{{ image_asset_url($editSlide->image) }}" alt="الصورة الحالية" 
                             style="max-width: 300px; max-height: 200px; border-radius: 8px; border: 2px solid rgba(255, 255, 255, 0.2); object-fit: cover;">
                    </div>
                @endif
                <input type="file" name="image" id="imageInput" class="form-control" accept="image/*">
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    اختر صورة الشريحة (JPG, PNG, GIF, SVG) - حجم أقصى 10MB
                </small>
            </div>

            <!-- حقل الفيديو -->
            <div class="form-group" id="videoField" style="display: none;">
                <label class="form-label">
                    <i class="fab fa-youtube"></i> رابط الفيديو
                </label>
                <input type="url" name="video_url" id="videoInput" class="form-control" 
                       value="{{ old('video_url', $editSlide->video_url ?? '') }}" 
                       placeholder="https://www.youtube.com/watch?v=... أو رابط مباشر للفيديو">
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    أدخل رابط YouTube أو رابط مباشر للفيديو (سيتم تشغيله تلقائياً ويتكرر)
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-link"></i> رابط (اختياري)
                </label>
                <input type="url" name="link" class="form-control" 
                       value="{{ old('link', $editSlide->link ?? '') }}" 
                       placeholder="https://example.com">
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    رابط يتم فتحه عند النقر على الشريحة
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-sort-numeric-up"></i> ترتيب العرض
                </label>
                <input type="number" name="order" class="form-control" 
                       value="{{ old('order', $editSlide->order ?? 0) }}" 
                       placeholder="0" min="0">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-toggle-on"></i> الحالة
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" 
                           {{ ($editSlide->is_active ?? true) ? 'checked' : '' }}>
                    <span>نشط</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ $editSlide ? 'تحديث' : 'إضافة' }}
            </button>
            
            @if($editSlide)
                <a href="{{ website_page_url('media') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            @endif
        </form>
    </div>

    <!-- قائمة الشرائح -->
    <div class="content-card" style="background: rgba(255, 255, 255, 0.08);">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
            <i class="fas fa-images"></i> قائمة الشرائح
        </h2>

        @if($slides->count() > 0)
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
                        @foreach($slides as $slide)
                            <tr>
                                <td>{{ $slide->id }}</td>
                                <td>
                                    @if($slide->type === 'video')
                                        <div style="width: 100px; height: 60px; border-radius: 8px; background: rgba(255, 0, 0, 0.2); display: flex; align-items: center; justify-content: center; border: 1px solid rgba(255, 0, 0, 0.3);">
                                            <i class="fas fa-video" style="font-size: 1.5rem; color: #FF0000;"></i>
                                        </div>
                                    @elseif($slide->image && strpos($slide->image, 'storage/') !== false)
                                        <img src="{{ image_asset_url($slide->image) }}" alt="{{ $slide->title ?? 'صورة' }}" 
                                             style="width: 100px; height: 60px; border-radius: 8px; object-fit: cover; border: 1px solid rgba(255, 255, 255, 0.2);">
                                    @else
                                        <span style="color: rgba(255, 255, 255, 0.5);">لا يوجد</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $slide->title ?? '-' }}
                                    @if($slide->type === 'video')
                                        <span class="badge badge-info" style="margin-right: 0.5rem;">فيديو</span>
                                    @else
                                        <span class="badge badge-secondary" style="margin-right: 0.5rem;">صورة</span>
                                    @endif
                                </td>
                                <td>
                                    @if($slide->link)
                                        <a href="{{ $slide->link }}" target="_blank" style="color: var(--primary-color);">
                                            {{ mb_substr($slide->link, 0, 30) }}{{ mb_strlen($slide->link) > 30 ? '...' : '' }}
                                        </a>
                                    @else
                                        <span style="color: rgba(255, 255, 255, 0.5);">-</span>
                                    @endif
                                </td>
                                <td>{{ $slide->order }}</td>
                                <td>
                                    @if($slide->is_active)
                                        <span class="badge badge-success">نشط</span>
                                    @else
                                        <span class="badge badge-danger">غير نشط</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ website_page_url('media', ['edit_slide' => $slide->id]) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> تعديل
                                    </a>
                                    <form method="POST" action="{{ route('dashboard.media.slides.destroy', $slide->id) }}" 
                                          style="display: inline-block;" 
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذه الشريحة؟');">
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
                <i class="fas fa-images"></i>
                <p>لا توجد شرائح مضافة بعد</p>
            </div>
        @endif
    </div>
</div>

<script>
function toggleSlideFields() {
    const type = document.getElementById('slideType');
    if (!type) return;
    
    const slideType = type.value;
    const imageField = document.getElementById('imageField');
    const videoField = document.getElementById('videoField');
    const imageInput = document.getElementById('imageInput');
    const videoInput = document.getElementById('videoInput');
    
    if (slideType === 'video') {
        if (imageField) imageField.style.display = 'none';
        if (videoField) videoField.style.display = 'block';
        if (imageInput) imageInput.removeAttribute('required');
        if (videoInput) videoInput.setAttribute('required', 'required');
    } else {
        if (imageField) imageField.style.display = 'block';
        if (videoField) videoField.style.display = 'none';
        if (imageInput) imageInput.setAttribute('required', 'required');
        if (videoInput) videoInput.removeAttribute('required');
    }
}

// تهيئة الحقول عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('slideType')) {
        toggleSlideFields();
        document.getElementById('slideType').addEventListener('change', toggleSlideFields);
    }
});
</script>

