<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-file-alt"></i> إدارة الملفات
        </h1>
        <p class="page-subtitle">رفع وإدارة الملفات (PDF, DOC, وغيرها)</p>
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

    <!-- نموذج إضافة/تعديل ملف -->
    <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-bottom: 2rem;">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
            {{ $editFile ? 'تعديل ملف' : 'رفع ملف جديد' }}
        </h2>
        
        @if($editFile)
            <form method="POST" action="{{ route('dashboard.files.update', $editFile->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
        @else
            <form method="POST" action="{{ route('dashboard.files.store') }}" enctype="multipart/form-data">
                @csrf
        @endif
            
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-heading"></i> اسم الملف
                </label>
                <input type="text" name="name" class="form-control" 
                       value="{{ old('name', $editFile->name ?? '') }}" 
                       placeholder="أدخل اسم الملف" required>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-file"></i> الملف
                </label>
                @if($editFile && !empty($editFile->file_path))
                    <div style="margin-bottom: 1rem; padding: 1rem; background: rgba(0, 0, 0, 0.2); border-radius: 8px;">
                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                            <i class="fas fa-file-pdf" style="font-size: 2rem; color: var(--primary-color);"></i>
                            <div style="flex: 1;">
                                <p style="color: var(--text-primary); margin: 0; font-weight: 600;">{{ $editFile->name }}</p>
                                <p style="color: var(--text-secondary); margin: 0.25rem 0 0; font-size: 0.9rem;">
                                    {{ strtoupper($editFile->file_type) }} - {{ $editFile->file_size_human ?? 'غير محدد' }}
                                </p>
                            </div>
                            <a href="{{ image_asset_url($editFile->file_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                <i class="fas fa-external-link-alt"></i> فتح
                            </a>
                        </div>
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="file_remove" value="1">
                            <span style="color: rgba(255, 255, 255, 0.7);">حذف الملف الحالي</span>
                        </label>
                    </div>
                @endif
                <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar" {{ $editFile ? '' : 'required' }}>
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    اختر الملف من جهازك (PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, ZIP, RAR) - حجم أقصى 50MB
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-align-right"></i> الوصف (اختياري)
                </label>
                <textarea name="description" class="form-control" rows="3"
                          placeholder="أدخل وصفاً للملف">{{ old('description', $editFile->description ?? '') }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-sort-numeric-down"></i> الترتيب
                    </label>
                    <input type="number" name="order" class="form-control" 
                           value="{{ old('order', $editFile->order ?? 0) }}" 
                           placeholder="0" min="0">
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-top: 2rem;">
                        <input type="checkbox" name="is_active" value="1" 
                               {{ ($editFile->is_active ?? true) ? 'checked' : '' }}>
                        <span>نشط</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ $editFile ? 'تحديث' : 'رفع' }}
            </button>
            
            @if($editFile)
                <a href="{{ website_page_url('files') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            @endif
        </form>
    </div>

    <!-- قائمة الملفات -->
    <div class="content-card" style="background: rgba(255, 255, 255, 0.08);">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
            <i class="fas fa-list"></i> قائمة الملفات
        </h2>

        @if($files->count() > 0)
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>النوع</th>
                            <th>الحجم</th>
                            <th>الرابط المباشر</th>
                            <th>الترتيب</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($files as $file)
                            <tr>
                                <td>{{ $file->id }}</td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <i class="fas fa-file-pdf" style="color: var(--primary-color); font-size: 1.25rem;"></i>
                                        <div>
                                            <div style="font-weight: 600; color: var(--text-primary);">{{ $file->name }}</div>
                                            @if($file->description)
                                            <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 0.25rem;">{{ Str::limit($file->description, 50) }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span style="padding: 0.25rem 0.75rem; background: rgba(95, 179, 142, 0.2); border-radius: 12px; font-size: 0.85rem; font-weight: 600; color: var(--primary-color);">
                                        {{ strtoupper($file->file_type ?? 'غير محدد') }}
                                    </span>
                                </td>
                                <td>{{ $file->file_size_human ?? 'غير محدد' }}</td>
                                <td>
                                    @if($file->file_path)
                                    <a href="{{ image_asset_url($file->file_path) }}" target="_blank" class="btn btn-sm btn-primary" style="text-decoration: none;">
                                        <i class="fas fa-external-link-alt"></i> فتح
                                    </a>
                                    @else
                                    <span style="color: var(--text-secondary);">-</span>
                                    @endif
                                </td>
                                <td>{{ $file->order }}</td>
                                <td>
                                    @if($file->is_active)
                                        <span style="color: #4ade80; font-weight: 600;">نشط</span>
                                    @else
                                        <span style="color: #f87171; font-weight: 600;">غير نشط</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <a href="{{ website_page_url('files', ['edit' => $file->id]) }}" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> تعديل
                                        </a>
                                        <form method="POST" action="{{ route('dashboard.files.destroy', $file->id) }}" 
                                              style="display: inline-block;" 
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا الملف؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i> حذف
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                <i class="fas fa-file-alt" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                <p>لا توجد ملفات حالياً</p>
            </div>
        @endif
    </div>
</div>

