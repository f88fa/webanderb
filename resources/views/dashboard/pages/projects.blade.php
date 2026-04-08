<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-project-diagram"></i> المشاريع
        </h1>
        <p class="page-subtitle">إدارة مشاريع الموقع</p>
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
                    #projects
                </code>
                <a href="{{ url('/#projects') }}" target="_blank" class="btn btn-primary" style="text-decoration: none;">
                    <i class="fas fa-external-link-alt"></i> عرض القسم
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Quill Editor - محرر نصوص بسيط ومجاني -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<style>
    #project-description-editor {
        display: none;
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
    var textarea = document.getElementById('project-description-editor');
    var editorDiv = document.createElement('div');
    editorDiv.id = 'project-description-quill-editor';
    textarea.parentNode.insertBefore(editorDiv, textarea);
    
    var quill = new Quill('#project-description-quill-editor', {
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
        placeholder: 'أدخل تفاصيل المشروع...',
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

<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-project-diagram"></i> مشاريعنا
        </h1>
        <p class="page-subtitle">إدارة مشاريع الموقع</p>
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

    <!-- نموذج إضافة/تعديل مشروع -->
    <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-bottom: 2rem;">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
            {{ $editProject ? 'تعديل مشروع' : 'إضافة مشروع جديد' }}
        </h2>
        
        @if($editProject)
            <form method="POST" action="{{ route('dashboard.projects.update', $editProject->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
        @else
            <form method="POST" action="{{ route('dashboard.projects.store') }}" enctype="multipart/form-data">
                @csrf
        @endif
            
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-heading"></i> اسم المشروع
                </label>
                <input type="text" name="name" class="form-control" 
                       value="{{ old('name', $editProject->name ?? '') }}" 
                       placeholder="أدخل اسم المشروع" required>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-align-right"></i> تفاصيل المشروع
                </label>
                <textarea id="project-description-editor" name="description" class="form-control" rows="5"
                          placeholder="أدخل تفاصيل المشروع">{{ old('description', $editProject->description ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-image"></i> صورة المشروع
                </label>
                @if($editProject && !empty($editProject->image) && strpos($editProject->image, 'storage/') !== false)
                    <div style="margin-bottom: 1rem;">
                        <img src="{{ image_asset_url($editProject->image) }}" alt="الصورة الحالية" 
                             style="max-width: 300px; max-height: 200px; border-radius: 12px; border: 2px solid rgba(255, 255, 255, 0.1); object-fit: cover;">
                    </div>
                @endif
                <input type="file" name="image" class="form-control" accept="image/*" {{ $editProject ? '' : 'required' }}>
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    اختر صورة المشروع من جهازك (JPG, PNG, GIF, SVG) - حجم أقصى 10MB
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-link"></i> رابط التبرع/المتجر
                </label>
                <input type="url" name="donate_link" class="form-control" 
                       value="{{ old('donate_link', $editProject->donate_link ?? '') }}" 
                       placeholder="https://example.com/donate">
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    رابط صفحة التبرع أو المتجر للمشروع
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-font"></i> نص زر التبرع
                </label>
                <input type="text" name="donate_button_text" class="form-control" 
                       value="{{ old('donate_button_text', $editProject->donate_button_text ?? 'تبرع الآن') }}" 
                       placeholder="تبرع الآن">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-sort-numeric-up"></i> ترتيب العرض
                </label>
                <input type="number" name="order" class="form-control" 
                       value="{{ old('order', $editProject->order ?? 0) }}" 
                       placeholder="0" min="0">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-toggle-on"></i> الحالة
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" 
                           {{ ($editProject->is_active ?? true) ? 'checked' : '' }}>
                    <span>نشط</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ $editProject ? 'تحديث' : 'إضافة' }}
            </button>
            
            @if($editProject)
                <a href="{{ website_page_url('projects') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            @endif
        </form>
    </div>

    <!-- قائمة المشاريع -->
    <div class="content-card" style="background: rgba(255, 255, 255, 0.08);">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
            <i class="fas fa-list"></i> قائمة المشاريع
        </h2>

        @if($projects->count() > 0)
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الصورة</th>
                            <th>اسم المشروع</th>
                            <th>الوصف</th>
                            <th>رابط التبرع</th>
                            <th>الترتيب</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projects as $project)
                            <tr>
                                <td>{{ $project->id }}</td>
                                <td>
                                    @if($project->image && strpos($project->image, 'storage/') !== false)
                                        <img src="{{ image_asset_url($project->image) }}" alt="{{ $project->name }}" 
                                             style="width: 80px; height: 60px; border-radius: 8px; object-fit: cover; border: 1px solid rgba(255, 255, 255, 0.1);">
                                    @else
                                        <span style="color: rgba(255, 255, 255, 0.5);">لا يوجد</span>
                                    @endif
                                </td>
                                <td>{{ $project->name }}</td>
                                <td>
                                    <span style="color: rgba(255, 255, 255, 0.7);">
                                        {{ mb_substr($project->description ?? '', 0, 50) }}{{ mb_strlen($project->description ?? '') > 50 ? '...' : '' }}
                                    </span>
                                </td>
                                <td>
                                    @if($project->donate_link)
                                        <a href="{{ $project->donate_link }}" target="_blank" style="color: var(--primary-color);">
                                            {{ mb_substr($project->donate_link, 0, 30) }}{{ mb_strlen($project->donate_link) > 30 ? '...' : '' }}
                                        </a>
                                    @else
                                        <span style="color: rgba(255, 255, 255, 0.5);">-</span>
                                    @endif
                                </td>
                                <td>{{ $project->order }}</td>
                                <td>
                                    @if($project->is_active)
                                        <span class="badge badge-success">نشط</span>
                                    @else
                                        <span class="badge badge-danger">غير نشط</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ website_page_url('projects', ['edit' => $project->id]) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> تعديل
                                    </a>
                                    <form method="POST" action="{{ route('dashboard.projects.destroy', $project->id) }}" 
                                          style="display: inline-block;" 
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا المشروع؟');">
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
                <i class="fas fa-project-diagram"></i>
                <p>لا توجد مشاريع مضافة بعد</p>
            </div>
        @endif
    </div>
</div>

