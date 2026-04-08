<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-newspaper"></i> الأخبار
        </h1>
        <p class="page-subtitle">إدارة أخبار الموقع</p>
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
                    #news
                </code>
                <a href="{{ url('/#news') }}" target="_blank" class="btn btn-primary" style="text-decoration: none;">
                    <i class="fas fa-external-link-alt"></i> عرض القسم
                </a>
        </div>
    </div>
</div>

<!-- Quill Editor - محرر نصوص بسيط ومجاني -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<style>
    #news-content-editor {
        display: none;
    }
    /* منع اختفاء محرر الخبر خلف القائمة الجانبية */
    .news-editor-wrapper {
        position: relative;
        z-index: 1001;
    }
    #news-content-quill-editor {
        position: relative;
        z-index: 1001;
    }
    #news-content-quill-editor .ql-toolbar.ql-snow {
        z-index: 1002 !important;
        position: relative;
    }
    #news-content-quill-editor .ql-container.ql-snow {
        z-index: 1001;
        position: relative;
    }
    .ql-snow .ql-picker.ql-expanded .ql-picker-options {
        z-index: 1010 !important;
    }
    .ql-editor {
        min-height: 300px;
        font-family: 'Cairo', Arial, sans-serif;
        font-size: 16px;
        direction: rtl;
        text-align: right;
    }
    .ql-container {
        font-family: 'Cairo', Arial, sans-serif;
        direction: rtl;
    }
    .ql-toolbar {
        direction: rtl;
        text-align: right;
        background: white;
        border: 1px solid #ccc;
        border-radius: 4px 4px 0 0;
    }
    .ql-container {
        border: 1px solid #ccc;
        border-top: none;
        border-radius: 0 0 4px 4px;
    }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var textarea = document.getElementById('news-content-editor');
    var editorDiv = document.createElement('div');
    editorDiv.id = 'news-content-quill-editor';
    textarea.parentNode.insertBefore(editorDiv, textarea);
    
    var quill = new Quill('#news-content-quill-editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'align': [] }],
                ['link', 'image'],
                ['clean']
            ]
        },
        placeholder: 'أدخل محتوى الخبر...',
    });
    
    // تحميل المحتوى الموجود
    if (textarea.value) {
        quill.root.innerHTML = textarea.value;
    }
    
    // تحديث textarea عند تغيير المحتوى
    quill.on('text-change', function() {
        textarea.value = quill.root.innerHTML;
    });
    
    // تحديث textarea عند إرسال النموذج
    var form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            textarea.value = quill.root.innerHTML;
        });
    }
});
</script>

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

    <!-- نموذج إضافة/تعديل خبر -->
    <div class="content-card" style="background: rgba(255, 255, 255, 0.08);">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
            {{ $editNews ? 'تعديل خبر' : 'إضافة خبر جديد' }}
        </h2>
        
        @if($editNews)
            <form method="POST" action="{{ route('dashboard.news.update', $editNews->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
        @else
            <form method="POST" action="{{ route('dashboard.news.store') }}" enctype="multipart/form-data">
                @csrf
        @endif
            
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-heading"></i> عنوان الخبر
                </label>
                <input type="text" name="title" class="form-control" 
                       value="{{ old('title', $editNews->title ?? '') }}" 
                       placeholder="أدخل عنوان الخبر" required>
            </div>

            <div class="form-group news-editor-wrapper">
                <label class="form-label">
                    <i class="fas fa-align-right"></i> محتوى الخبر
                </label>
                <textarea id="news-content-editor" name="content" class="form-control" rows="6"
                          placeholder="أدخل محتوى الخبر" required>{{ old('content', $editNews->content ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-image"></i> صورة الخبر
                </label>
                @if($editNews && !empty($editNews->image))
                    <div style="margin-bottom: 1rem;">
                        <img src="{{ image_asset_url($editNews->image) }}" alt="صورة الخبر الحالية" 
                             style="max-width: 300px; max-height: 200px; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.1);">
                    </div>
                @endif
                <input type="file" name="image" class="form-control" accept="image/*">
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    اختر صورة الخبر من جهازك (JPG, PNG, GIF) - حجم أقصى 10MB
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-toggle-on"></i> الحالة
                </label>
                <select name="status" class="form-control">
                    <option value="active" {{ ($editNews->status ?? 'active') == 'active' ? 'selected' : '' }}>
                        نشط
                    </option>
                    <option value="inactive" {{ ($editNews->status ?? '') == 'inactive' ? 'selected' : '' }}>
                        غير نشط
                    </option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ $editNews ? 'تحديث' : 'إضافة' }}
            </button>
            
            @if($editNews)
                <a href="{{ website_page_url('news') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            @endif
        </form>
    </div>

    <!-- قائمة الأخبار -->
    <div style="margin-top: 3rem;">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
            <i class="fas fa-list"></i> قائمة الأخبار ({{ $news->count() }})
        </h2>

        @if($news->count() > 0)
            <div class="news-grid">
                @foreach($news as $item)
                    <div class="news-card">
                        <div class="news-card-header">
                            <div>
                                <h3 class="news-card-title">{{ $item->title }}</h3>
                                <span class="status-badge {{ $item->status == 'active' ? 'status-active' : 'status-inactive' }}">
                                    <i class="fas fa-circle"></i>
                                    {{ $item->status == 'active' ? 'نشط' : 'غير نشط' }}
                                </span>
                            </div>
                        </div>
                        
                        @if($item->image)
                            <img src="{{ $item->image }}" 
                                 alt="{{ $item->title }}" 
                                 style="width: 100%; border-radius: 8px; margin-bottom: 1rem; max-height: 200px; object-fit: cover;">
                        @endif
                        
                        <p class="news-card-content">{{ news_excerpt($item->content ?? '', 150) }}</p>
                        
                        <div style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 1rem;">
                            <i class="fas fa-calendar"></i> 
                            {{ $item->created_at->format('Y-m-d H:i') }}
                        </div>
                        
                        <div class="news-card-actions">
                            <a href="{{ website_page_url('news', ['edit' => $item->id]) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-edit"></i> تعديل
                            </a>
                            <form method="POST" action="{{ route('dashboard.news.destroy', $item->id) }}" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا الخبر؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i> حذف
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                <i class="fas fa-newspaper" style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                <p>لا توجد أخبار حتى الآن</p>
            </div>
        @endif
    </div>
</div>

