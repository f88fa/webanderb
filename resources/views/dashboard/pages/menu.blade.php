<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-bars"></i> القائمة العلوية
        </h1>
        <p class="page-subtitle">إدارة أزرار القائمة العلوية</p>
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

    <!-- نموذج إضافة/تعديل عنصر القائمة -->
    <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-bottom: 2rem;">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
            {{ $editMenuItem ? 'تعديل عنصر القائمة' : 'إضافة عنصر قائمة جديد' }}
        </h2>
        
        @if($editMenuItem)
            <form method="POST" action="{{ route('dashboard.menu.update', $editMenuItem->id) }}" id="menuForm">
                @csrf
                @method('PUT')
        @else
            <form method="POST" action="{{ route('dashboard.menu.store') }}" id="menuForm">
                @csrf
        @endif
            
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-heading"></i> عنوان الزر
                </label>
                <input type="text" name="title" class="form-control" 
                       value="{{ old('title', $editMenuItem->title ?? '') }}" 
                       placeholder="أدخل عنوان الزر" required>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-tag"></i> نوع الزر
                </label>
                <select name="type" id="menuType" class="form-control" required>
                    <option value="link" {{ old('type', $editMenuItem->type ?? '') == 'link' ? 'selected' : '' }}>رابط مباشر</option>
                    <option value="dropdown" {{ old('type', $editMenuItem->type ?? '') == 'dropdown' ? 'selected' : '' }}>قائمة منسدلة</option>
                    <option value="page" {{ old('type', $editMenuItem->type ?? '') == 'page' ? 'selected' : '' }}>صفحة HTML</option>
                </select>
            </div>

            <!-- حقل الرابط (للنوع link) -->
            <div class="form-group" id="urlField">
                <label class="form-label">
                    <i class="fas fa-link"></i> الرابط
                </label>
                <input type="text" name="url" id="urlInput" class="form-control" 
                       value="{{ old('url', $editMenuItem->url ?? '') }}" 
                       placeholder="مثال: #about أو https://example.com">
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    للروابط الداخلية استخدم # (مثال: #about) أو للروابط الخارجية استخدم https://
                </small>
            </div>

            <!-- حقل القائمة المنسدلة (للعناصر الفرعية) -->
            <div class="form-group" id="parentField">
                <label class="form-label">
                    <i class="fas fa-sitemap"></i> إضافة تحت (قائمة منسدلة)
                </label>
                <select name="parent_id" id="parentInput" class="form-control">
                    <option value="">-- عنصر رئيسي (ليس ضمن قائمة منسدلة) --</option>
                    @foreach($menuItems->where('type', 'dropdown') as $dropdown)
                        @if(!$editMenuItem || $dropdown->id != $editMenuItem->id)
                            <option value="{{ $dropdown->id }}" {{ old('parent_id', $editMenuItem->parent_id ?? '') == $dropdown->id ? 'selected' : '' }}>
                                {{ $dropdown->title }}
                            </option>
                        @endif
                    @endforeach
                </select>
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    اختر قائمة منسدلة لإضافة هذا العنصر كعنصر فرعي داخلها
                </small>
            </div>

            <!-- حقل محتوى الصفحة (للنوع page) -->
            <div class="form-group" id="pageContentField">
                <label class="form-label">
                    <i class="fas fa-code"></i> محتوى الصفحة (HTML)
                </label>
                <textarea name="page_content" id="pageContentInput" class="form-control" rows="15"
                          placeholder="أدخل محتوى HTML للصفحة...">{{ old('page_content', $editMenuItem->page_content ?? '') }}</textarea>
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    يمكنك كتابة HTML كامل أو نص عادي. سيتم عرضه كصفحة منفصلة.
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-sort-numeric-up"></i> ترتيب العرض
                </label>
                <input type="number" name="order" class="form-control" 
                       value="{{ old('order', $editMenuItem->order ?? 0) }}" 
                       placeholder="0" min="0">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-toggle-on"></i> الحالة
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" 
                           {{ ($editMenuItem->is_active ?? true) ? 'checked' : '' }}>
                    <span>نشط</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ $editMenuItem ? 'تحديث' : 'إضافة' }}
            </button>
            
            @if($editMenuItem)
                <a href="{{ website_page_url('menu') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            @endif
        </form>
    </div>

    <!-- قائمة عناصر القائمة -->
    <div class="content-card" style="background: rgba(255, 255, 255, 0.08);">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
            <i class="fas fa-list"></i> عناصر القائمة
        </h2>

        @if($menuItems->count() > 0)
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>العنوان</th>
                            <th>النوع</th>
                            <th>الرابط/المحتوى</th>
                            <th>الأب</th>
                            <th>الترتيب</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($menuItems->whereNull('parent_id') as $item)
                            @include('dashboard.pages.menu-item-row', ['item' => $item, 'level' => 0, 'menuItems' => $menuItems])
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-bars"></i>
                <p>لا توجد عناصر قائمة مضافة بعد</p>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuType = document.getElementById('menuType');
    const urlField = document.getElementById('urlField');
    const parentField = document.getElementById('parentField');
    const pageContentField = document.getElementById('pageContentField');
    const urlInput = document.getElementById('urlInput');
    const parentInput = document.getElementById('parentInput');
    const pageContentInput = document.getElementById('pageContentInput');

    function toggleFields() {
        const type = menuType.value;
        
        // إخفاء جميع الحقول أولاً
        urlField.style.display = 'none';
        parentField.style.display = 'none';
        pageContentField.style.display = 'none';
        
        // إظهار الحقول المناسبة حسب النوع
        if (type === 'link') {
            urlField.style.display = 'block';
            parentField.style.display = 'block';
            urlInput.required = true;
            pageContentInput.required = false;
        } else if (type === 'dropdown') {
            parentField.style.display = 'none'; // القائمة المنسدلة لا يمكن أن تكون فرعية
            urlInput.required = false;
            pageContentInput.required = false;
        } else if (type === 'page') {
            pageContentField.style.display = 'block';
            parentField.style.display = 'block';
            urlInput.required = false;
            pageContentInput.required = false;
        }
    }

    // استدعاء الدالة عند تحميل الصفحة
    toggleFields();

    // استدعاء الدالة عند تغيير النوع
    menuType.addEventListener('change', toggleFields);
});
</script>

