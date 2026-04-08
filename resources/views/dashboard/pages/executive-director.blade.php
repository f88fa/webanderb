<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-user-tie"></i> المدير التنفيذي
        </h1>
        <p class="page-subtitle">إدارة معلومات المدير التنفيذي</p>
    </div>

    <!-- رابط الصفحة -->
    <div class="content-card" style="background: linear-gradient(135deg, rgba(95, 179, 142, 0.2) 0%, rgba(31, 107, 79, 0.2) 100%); border: 2px solid rgba(95, 179, 142, 0.3); margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h3 style="color: var(--text-primary); margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-link" style="color: var(--primary-color);"></i>
                    رابط الصفحة المباشر
                </h3>
                <p style="color: var(--text-secondary); margin: 0; font-size: 0.95rem;">
                    استخدم هذا الرابط لإضافة الصفحة في القائمة العلوية
                </p>
            </div>
            <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                <code style="background: rgba(0, 0, 0, 0.3); padding: 0.75rem 1.5rem; border-radius: 8px; color: var(--primary-color); font-size: 1.1rem; font-weight: 600; border: 1px solid rgba(95, 179, 142, 0.3);">
                    /executive-director
                </code>
                <a href="{{ url('/executive-director') }}" target="_blank" class="btn btn-primary" style="text-decoration: none;">
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

    <!-- نموذج إدارة المدير التنفيذي -->
    <form method="POST" action="{{ route('dashboard.about.store') }}" enctype="multipart/form-data" novalidate>
        @csrf
        
        <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-bottom: 2rem;">
            <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
                <i class="fas fa-user"></i> المعلومات الأساسية
            </h2>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-user"></i> اسم المدير التنفيذي
                </label>
                <input type="text" name="executive_director_name" class="form-control" 
                       value="{{ old('executive_director_name', $settings['executive_director_name'] ?? '') }}" 
                       placeholder="أدخل اسم المدير التنفيذي">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-briefcase"></i> منصب المدير التنفيذي
                </label>
                <input type="text" name="executive_director_position" class="form-control" 
                       value="{{ old('executive_director_position', $settings['executive_director_position'] ?? '') }}" 
                       placeholder="أدخل منصب المدير التنفيذي">
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    إذا تركت هذا الحقل فارغاً، سيظهر "المدير التنفيذي" كقيمة افتراضية
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-image"></i> صورة المدير التنفيذي
                </label>
                @if(!empty($settings['executive_director_image']))
                    <div style="margin-bottom: 1rem;">
                        <img src="{{ image_asset_url($settings['executive_director_image']) }}" alt="صورة المدير الحالية" 
                             style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(95, 179, 142, 0.3); box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);">
                    </div>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-bottom: 1rem;">
                        <input type="checkbox" name="executive_director_image_remove" value="1">
                        <span style="color: rgba(255, 255, 255, 0.7);">حذف الصورة الحالية</span>
                    </label>
                @endif
                <input type="file" name="executive_director_image" class="form-control" accept="image/*">
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    اختر صورة المدير التنفيذي (JPG, PNG) - حجم أقصى 5MB
                </small>
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-bottom: 1rem;">
                    <input type="checkbox" name="executive_director_visible" value="1" 
                           {{ ($settings['executive_director_visible'] ?? '0') == '1' ? 'checked' : '' }}
                           style="width: 24px; height: 24px; cursor: pointer;">
                    <span style="color: rgba(255, 255, 255, 0.9); font-weight: 600;">إظهار المدير التنفيذي في قسم "من نحن"</span>
                </label>
            </div>
        </div>

        <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-bottom: 2rem;">
            <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
                <i class="fas fa-envelope"></i> معلومات التواصل
            </h2>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-envelope"></i> البريد الإلكتروني
                </label>
                <input type="email" name="executive_director_email" class="form-control" 
                       value="{{ old('executive_director_email', $settings['executive_director_email'] ?? '') }}" 
                       placeholder="example@email.com">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-phone"></i> رقم التواصل
                </label>
                <input type="text" name="executive_director_phone" class="form-control" 
                       value="{{ old('executive_director_phone', $settings['executive_director_phone'] ?? '') }}" 
                       placeholder="+966 50 123 4567">
            </div>
        </div>

        <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-bottom: 2rem;">
            <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
                <i class="fas fa-file-alt"></i> السيرة الذاتية
            </h2>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-file-alt"></i> السيرة الذاتية
                </label>
                <textarea name="executive_director_bio" class="form-control" rows="8" 
                          placeholder="أدخل السيرة الذاتية للمدير التنفيذي">{{ old('executive_director_bio', $settings['executive_director_bio'] ?? '') }}</textarea>
            </div>
        </div>

        <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-bottom: 2rem;">
            <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
                <i class="fas fa-share-alt"></i> حسابات التواصل الاجتماعي
            </h2>

            <div class="form-group">
                <label class="form-label">
                    <i class="fab fa-facebook"></i> فيسبوك
                </label>
                <input type="url" name="executive_director_facebook" class="form-control" 
                       value="{{ old('executive_director_facebook', $settings['executive_director_facebook'] ?? '') }}" 
                       placeholder="https://facebook.com/username">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="width: 1em; height: 1em; vertical-align: -0.125em; display: inline-block; margin-left: 0.5rem;">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg> X (تويتر)
                </label>
                <input type="url" name="executive_director_twitter" class="form-control" 
                       value="{{ old('executive_director_twitter', $settings['executive_director_twitter'] ?? '') }}" 
                       placeholder="https://twitter.com/username">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fab fa-instagram"></i> إنستقرام
                </label>
                <input type="url" name="executive_director_instagram" class="form-control" 
                       value="{{ old('executive_director_instagram', $settings['executive_director_instagram'] ?? '') }}" 
                       placeholder="https://instagram.com/username">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fab fa-linkedin"></i> لينكد إن
                </label>
                <input type="url" name="executive_director_linkedin" class="form-control" 
                       value="{{ old('executive_director_linkedin', $settings['executive_director_linkedin'] ?? '') }}" 
                       placeholder="https://linkedin.com/in/username">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fab fa-whatsapp"></i> واتساب
                </label>
                <input type="text" name="executive_director_whatsapp" class="form-control" 
                       value="{{ old('executive_director_whatsapp', $settings['executive_director_whatsapp'] ?? '') }}" 
                       placeholder="966501234567">
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    أدخل رقم الواتساب بدون رموز (مثال: 966501234567)
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fab fa-telegram"></i> تيليجرام
                </label>
                <input type="url" name="executive_director_telegram" class="form-control" 
                       value="{{ old('executive_director_telegram', $settings['executive_director_telegram'] ?? '') }}" 
                       placeholder="https://t.me/username">
            </div>
        </div>

        <button type="submit" name="save_about" class="btn btn-primary" style="margin-top: 2rem;">
            <i class="fas fa-save"></i> حفظ المعلومات
        </button>
    </form>

    <!-- رابط الصفحة الأمامية -->
    <div class="content-card" style="background: rgba(95, 179, 142, 0.1); margin-top: 2rem; padding: 1.5rem; border-radius: 12px; border: 1px solid rgba(95, 179, 142, 0.3);">
        <h3 style="color: var(--primary-color); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.75rem;">
            <i class="fas fa-link"></i> رابط الصفحة الأمامية
        </h3>
        <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
            <input type="text" readonly value="{{ url('/executive-director') }}" 
                   style="flex: 1; min-width: 300px; padding: 0.75rem; background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 8px; color: var(--text-primary); font-family: monospace;">
            <a href="{{ url('/executive-director') }}" target="_blank" class="btn btn-primary">
                <i class="fas fa-external-link-alt"></i> فتح الصفحة
            </a>
        </div>
    </div>
</div>

