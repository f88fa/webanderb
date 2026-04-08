<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-eye"></i>
        الرؤية والرسالة
    </h1>
    <p class="page-description">إدارة محتوى الرؤية والرسالة</p>
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
                #vision-mission
            </code>
            <a href="{{ url('/#vision-mission') }}" target="_blank" class="btn btn-primary" style="text-decoration: none;">
                <i class="fas fa-external-link-alt"></i> عرض القسم
            </a>
        </div>
    </div>
</div>

<div class="content-card">
    <form action="{{ route('dashboard.vision-mission.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        @if(session('success_message'))
            <div class="alert alert-success" style="margin-bottom: 2rem;">
                <i class="fas fa-check-circle"></i>
                {{ session('success_message') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error" style="margin-bottom: 2rem;">
                <i class="fas fa-exclamation-circle"></i>
                <ul style="margin: 0.5rem 0 0 1.5rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- عنوان القسم -->
        <div class="content-card" style="background: linear-gradient(135deg, rgba(95, 179, 142, 0.1) 0%, rgba(31, 107, 79, 0.1) 100%); padding: 2rem; border-radius: 16px; margin-bottom: 2rem; border: 2px solid rgba(95, 179, 142, 0.2);">
            <h2 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-heading" style="color: var(--primary-color);"></i>
                عنوان القسم
            </h2>
            
            <div class="form-group">
                <label for="section_title" class="form-label">
                    <i class="fas fa-text-width"></i>
                    عنوان القسم في الموقع
                </label>
                <input type="text" 
                       id="section_title" 
                       name="section_title" 
                       class="form-control" 
                       value="{{ old('section_title', $visionMission->section_title ?? 'رؤيتنا ورسالتنا') }}"
                       placeholder="رؤيتنا ورسالتنا">
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    سيظهر هذا العنوان في أعلى القسم في الموقع
                </small>
            </div>
        </div>

        <!-- قسم الرؤية -->
        <div class="content-card" style="background: rgba(255, 255, 255, 0.05); padding: 2rem; border-radius: 16px; margin-bottom: 2rem; border: 2px solid rgba(95, 179, 142, 0.15);">
            <h2 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; padding-bottom: 1rem; border-bottom: 2px solid rgba(95, 179, 142, 0.2);">
                <i class="fas fa-eye" style="color: var(--primary-color); font-size: 1.5rem;"></i>
                الرؤية
            </h2>
            
            <div class="form-group">
                <label for="vision_icon" class="form-label">
                    <i class="fas fa-icons"></i>
                    أيقونة الرؤية
                </label>
                <input type="text" 
                       id="vision_icon" 
                       name="vision_icon" 
                       class="form-control" 
                       value="{{ old('vision_icon', $visionMission->vision_icon ?? 'fas fa-eye') }}"
                       placeholder="مثال: fas fa-eye">
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    استخدم أيقونات Font Awesome (مثال: fas fa-eye)
                </small>
            </div>

            <div class="form-group">
                <label for="vision" class="form-label">
                    <i class="fas fa-align-right"></i>
                    نص الرؤية
                </label>
                <textarea id="vision" 
                          name="vision" 
                          class="form-control" 
                          rows="8"
                          style="min-height: 150px; resize: vertical;"
                          placeholder="اكتب الرؤية هنا...">{{ old('vision', $visionMission->vision ?? '') }}</textarea>
            </div>
        </div>

        <!-- قسم الرسالة -->
        <div class="content-card" style="background: rgba(255, 255, 255, 0.05); padding: 2rem; border-radius: 16px; margin-bottom: 2rem; border: 2px solid rgba(95, 179, 142, 0.15);">
            <h2 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; padding-bottom: 1rem; border-bottom: 2px solid rgba(95, 179, 142, 0.2);">
                <i class="fas fa-bullseye" style="color: var(--primary-color); font-size: 1.5rem;"></i>
                الرسالة
            </h2>
            
            <div class="form-group">
                <label for="mission_icon" class="form-label">
                    <i class="fas fa-icons"></i>
                    أيقونة الرسالة
                </label>
                <input type="text" 
                       id="mission_icon" 
                       name="mission_icon" 
                       class="form-control" 
                       value="{{ old('mission_icon', $visionMission->mission_icon ?? 'fas fa-bullseye') }}"
                       placeholder="مثال: fas fa-bullseye">
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    استخدم أيقونات Font Awesome (مثال: fas fa-bullseye)
                </small>
            </div>

            <div class="form-group">
                <label for="mission" class="form-label">
                    <i class="fas fa-align-right"></i>
                    نص الرسالة
                </label>
                <textarea id="mission" 
                          name="mission" 
                          class="form-control" 
                          rows="8"
                          style="min-height: 150px; resize: vertical;"
                          placeholder="اكتب الرسالة هنا...">{{ old('mission', $visionMission->mission ?? '') }}</textarea>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem; padding-top: 2rem; border-top: 2px solid rgba(255, 255, 255, 0.1);">
            <button type="submit" class="btn btn-primary" style="padding: 0.875rem 2rem; font-size: 1.1rem; font-weight: 600;">
                <i class="fas fa-save"></i>
                حفظ التغييرات
            </button>
        </div>
    </form>
</div>

