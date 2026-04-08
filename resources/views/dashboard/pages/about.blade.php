<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-info-circle"></i> من نحن
        </h1>
        <p class="page-subtitle">إدارة محتوى صفحة من نحن</p>
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
                    #about
                </code>
                <a href="{{ url('/#about') }}" target="_blank" class="btn btn-primary" style="text-decoration: none;">
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

    <form method="POST" action="{{ route('dashboard.about.store') }}" enctype="multipart/form-data" id="aboutForm">
        @csrf
        
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
                       value="{{ old('section_title', $about->section_title ?? 'من نحن') }}"
                       placeholder="من نحن">
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    سيظهر هذا العنوان في أعلى القسم في الموقع
                </small>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">
                <i class="fas fa-align-right"></i> المحتوى
            </label>
            <textarea name="content" class="form-control" rows="10"
                      placeholder="أدخل محتوى من نحن">{{ old('content', $about->content ?? '') }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">
                <i class="fas fa-link"></i> نص زر الدعوة للإجراء (اختياري)
            </label>
            <input type="text" name="cta_text" class="form-control" 
                   value="{{ old('cta_text', $about->cta_text ?? '') }}" 
                   placeholder="مثال: تواصل معنا">
        </div>

        <div class="form-group">
            <label class="form-label">
                <i class="fas fa-external-link-alt"></i> رابط زر الدعوة للإجراء (اختياري)
            </label>
            <input type="text" name="cta_link" class="form-control" 
                   value="{{ old('cta_link', $about->cta_link ?? '#contact') }}" 
                   placeholder="مثال: #contact أو https://example.com">
        </div>

        <div class="form-group">
            <label class="form-label">
                <i class="fas fa-image"></i> صورة من نحن
            </label>
            @if($about && !empty($about->image))
                <div style="margin-bottom: 1rem;">
                    <img src="{{ image_asset_url($about->image) }}" 
                         alt="صورة من نحن الحالية" 
                         style="max-width: 300px; max-height: 200px; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.1);">
                </div>
                <input type="hidden" name="image" value="{{ $about->image }}">
            @endif
            <input type="file" name="image_file" class="form-control" accept="image/*">
            <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                اختر صورة من جهازك (JPG, PNG, GIF, SVG) - حجم أقصى 10MB
            </small>
        </div>

        <!-- قسم الإحصائيات -->
        <div class="content-card" style="margin-top: 2rem;">
            <div class="page-header" style="margin-bottom: 1.5rem;">
                <h2 class="page-title" style="font-size: 1.5rem;">
                    <i class="fas fa-chart-bar"></i> الإحصائيات
                </h2>
                <p class="page-subtitle">إدارة بطاقات الإحصائيات (يمكنك إضافة/حذف/تعديل)</p>
            </div>

            <div id="stats-container">
                @if(isset($stats) && $stats->count() > 0)
                    @foreach($stats as $index => $stat)
                    <div class="stat-form-item" data-index="{{ $index }}">
                        <div class="form-row" style="display: grid; grid-template-columns: 1fr 2fr 3fr 1fr; gap: 1rem; align-items: end; margin-bottom: 1rem;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label" style="font-size: 0.9rem;">الأيقونة</label>
                                <input type="text" name="stats[{{ $index }}][icon]" class="form-control" 
                                       value="{{ $stat->icon ?? 'fas fa-star' }}" 
                                       placeholder="fas fa-star">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label" style="font-size: 0.9rem;">الرقم</label>
                                <input type="text" name="stats[{{ $index }}][number]" class="form-control" 
                                       value="{{ $stat->number }}" 
                                       placeholder="+15">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label" style="font-size: 0.9rem;">النص</label>
                                <input type="text" name="stats[{{ $index }}][label]" class="form-control" 
                                       value="{{ $stat->label }}" 
                                       placeholder="سنة من العطاء">
                            </div>
                            <button type="button" class="btn btn-danger remove-stat" style="padding: 0.75rem;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="stat-form-item" data-index="0">
                        <div class="form-row" style="display: grid; grid-template-columns: 1fr 2fr 3fr 1fr; gap: 1rem; align-items: end; margin-bottom: 1rem;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label" style="font-size: 0.9rem;">الأيقونة</label>
                                <input type="text" name="stats[0][icon]" class="form-control" 
                                       value="fas fa-calendar-alt" 
                                       placeholder="fas fa-star">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label" style="font-size: 0.9rem;">الرقم</label>
                                <input type="text" name="stats[0][number]" class="form-control" 
                                       value="+15" 
                                       placeholder="+15">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label" style="font-size: 0.9rem;">النص</label>
                                <input type="text" name="stats[0][label]" class="form-control" 
                                       value="سنة من العطاء" 
                                       placeholder="سنة من العطاء">
                            </div>
                            <button type="button" class="btn btn-danger remove-stat" style="padding: 0.75rem;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            <button type="button" id="add-stat-btn" class="btn" style="background: rgba(95, 179, 142, 0.2); border: 1px solid rgba(95, 179, 142, 0.3); color: var(--primary-color); margin-top: 1rem;">
                <i class="fas fa-plus"></i> إضافة إحصائية جديدة
            </button>
        </div>

        <!-- قسم أهدافنا -->
        <div class="content-card" style="margin-top: 2rem;">
            <div class="page-header" style="margin-bottom: 1.5rem;">
                <h2 class="page-title" style="font-size: 1.5rem;">
                    <i class="fas fa-bullseye"></i> أهدافنا
                </h2>
                <p class="page-subtitle">إدارة بطاقات الأهداف (يمكنك إضافة/حذف/تعديل)</p>
            </div>

            <div id="features-container">
                @if(isset($features) && $features->count() > 0)
                    @foreach($features as $index => $feature)
                    <div class="feature-form-item" data-index="{{ $index }}">
                        <div class="form-row" style="display: grid; grid-template-columns: 1fr 2fr 3fr 1fr; gap: 1rem; align-items: end; margin-bottom: 1rem;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label" style="font-size: 0.9rem;">الأيقونة</label>
                                <input type="text" name="features[{{ $index }}][icon]" class="form-control" 
                                       value="{{ $feature->icon ?? 'fas fa-check-circle' }}" 
                                       placeholder="fas fa-check-circle">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label" style="font-size: 0.9rem;">العنوان</label>
                                <input type="text" name="features[{{ $index }}][title]" class="form-control" 
                                       value="{{ $feature->title }}" 
                                       placeholder="خبرة طويلة">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label" style="font-size: 0.9rem;">النص</label>
                                <input type="text" name="features[{{ $index }}][text]" class="form-control" 
                                       value="{{ $feature->text }}" 
                                       placeholder="أكثر من 15 عاماً من الخبرة">
                            </div>
                            <button type="button" class="btn btn-danger remove-feature" style="padding: 0.75rem;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="feature-form-item" data-index="0">
                        <div class="form-row" style="display: grid; grid-template-columns: 1fr 2fr 3fr 1fr; gap: 1rem; align-items: end; margin-bottom: 1rem;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label" style="font-size: 0.9rem;">الأيقونة</label>
                                <input type="text" name="features[0][icon]" class="form-control" 
                                       value="fas fa-check-circle" 
                                       placeholder="fas fa-check-circle">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label" style="font-size: 0.9rem;">العنوان</label>
                                <input type="text" name="features[0][title]" class="form-control" 
                                       value="خبرة طويلة" 
                                       placeholder="خبرة طويلة">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label" style="font-size: 0.9rem;">النص</label>
                                <input type="text" name="features[0][text]" class="form-control" 
                                       value="أكثر من 15 عاماً من الخبرة في العمل الخيري" 
                                       placeholder="أكثر من 15 عاماً من الخبرة">
                            </div>
                            <button type="button" class="btn btn-danger remove-feature" style="padding: 0.75rem;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            <button type="button" id="add-feature-btn" class="btn" style="background: rgba(95, 179, 142, 0.2); border: 1px solid rgba(95, 179, 142, 0.3); color: var(--primary-color); margin-top: 1rem;">
                <i class="fas fa-plus"></i> إضافة مميزة جديدة
            </button>
        </div>

        <!-- قسم المدير التنفيذي -->
        <div class="content-card" style="margin-top: 2rem; background: linear-gradient(135deg, rgba(95, 179, 142, 0.1) 0%, rgba(31, 107, 79, 0.1) 100%); padding: 2rem; border-radius: 16px; border: 2px solid rgba(95, 179, 142, 0.2);">
            <div class="page-header" style="margin-bottom: 1.5rem;">
                <h2 class="page-title" style="font-size: 1.5rem;">
                    <i class="fas fa-user-tie"></i> المدير التنفيذي
                </h2>
                <p class="page-subtitle">سيظهر اسم المدير التنفيذي وصورته في نهاية نص قسم "من نحن"</p>
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-bottom: 1rem;">
                    <input type="checkbox" name="executive_director_visible" value="1" 
                           {{ ($settings['executive_director_visible'] ?? '0') == '1' ? 'checked' : '' }}
                           style="width: 24px; height: 24px; cursor: pointer;">
                    <span style="color: rgba(255, 255, 255, 0.9); font-weight: 600;">إظهار المدير التنفيذي في قسم "من نحن"</span>
                </label>
            </div>

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
                       placeholder="أدخل منصب المدير التنفيذي (مثال: المدير التنفيذي)">
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
                             style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(95, 179, 142, 0.3); box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);">
                    </div>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-bottom: 1rem;">
                        <input type="checkbox" name="executive_director_image_remove" value="1">
                        <span style="color: rgba(255, 255, 255, 0.7);">حذف الصورة الحالية</span>
                    </label>
                @endif
                <input type="file" name="executive_director_image" class="form-control" accept="image/*">
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    اختر صورة المدير التنفيذي (JPG, PNG) - حجم أقصى 5MB. سيتم عرضها بشكل دائري بجانب الاسم
                </small>
            </div>

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

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-file-alt"></i> السيرة الذاتية
                </label>
                <textarea name="executive_director_bio" class="form-control" rows="5" 
                          placeholder="أدخل السيرة الذاتية للمدير التنفيذي">{{ old('executive_director_bio', $settings['executive_director_bio'] ?? '') }}</textarea>
            </div>

            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid rgba(95, 179, 142, 0.2);">
                <h3 style="color: var(--text-primary); margin-bottom: 1.5rem; font-size: 1.2rem;">
                    <i class="fas fa-share-alt"></i> حسابات التواصل الاجتماعي
                </h3>

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
        </div>

        <button type="submit" name="save_about" class="btn btn-primary" style="margin-top: 2rem;">
            <i class="fas fa-save"></i> حفظ المعلومات
        </button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let statIndex = {{ isset($stats) && $stats->count() > 0 ? $stats->count() : 1 }};
            let featureIndex = {{ isset($features) && $features->count() > 0 ? $features->count() : 1 }};
            
            // إضافة إحصائية جديدة
            document.getElementById('add-stat-btn').addEventListener('click', function() {
                const container = document.getElementById('stats-container');
                const newStat = document.createElement('div');
                newStat.className = 'stat-form-item';
                newStat.setAttribute('data-index', statIndex);
                newStat.innerHTML = `
                    <div class="form-row" style="display: grid; grid-template-columns: 1fr 2fr 3fr 1fr; gap: 1rem; align-items: end; margin-bottom: 1rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="font-size: 0.9rem;">الأيقونة</label>
                            <input type="text" name="stats[${statIndex}][icon]" class="form-control" 
                                   value="fas fa-star" 
                                   placeholder="fas fa-star">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="font-size: 0.9rem;">الرقم</label>
                            <input type="text" name="stats[${statIndex}][number]" class="form-control" 
                                   value="" 
                                   placeholder="+15">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="font-size: 0.9rem;">النص</label>
                            <input type="text" name="stats[${statIndex}][label]" class="form-control" 
                                   value="" 
                                   placeholder="سنة من العطاء">
                        </div>
                        <button type="button" class="btn btn-danger remove-stat" style="padding: 0.75rem;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
                container.appendChild(newStat);
                statIndex++;
                
                // إضافة مستمع للحذف
                newStat.querySelector('.remove-stat').addEventListener('click', function() {
                    newStat.remove();
                });
            });
            
            // حذف إحصائية
            document.querySelectorAll('.remove-stat').forEach(btn => {
                btn.addEventListener('click', function() {
                    this.closest('.stat-form-item').remove();
                });
            });

            // إضافة مميزة جديدة
            document.getElementById('add-feature-btn').addEventListener('click', function() {
                const container = document.getElementById('features-container');
                const newFeature = document.createElement('div');
                newFeature.className = 'feature-form-item';
                newFeature.setAttribute('data-index', featureIndex);
                newFeature.innerHTML = `
                    <div class="form-row" style="display: grid; grid-template-columns: 1fr 2fr 3fr 1fr; gap: 1rem; align-items: end; margin-bottom: 1rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="font-size: 0.9rem;">الأيقونة</label>
                            <input type="text" name="features[${featureIndex}][icon]" class="form-control" 
                                   value="fas fa-check-circle" 
                                   placeholder="fas fa-check-circle">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="font-size: 0.9rem;">العنوان</label>
                            <input type="text" name="features[${featureIndex}][title]" class="form-control" 
                                   value="" 
                                   placeholder="خبرة طويلة">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="font-size: 0.9rem;">النص</label>
                            <input type="text" name="features[${featureIndex}][text]" class="form-control" 
                                   value="" 
                                   placeholder="أكثر من 15 عاماً من الخبرة">
                        </div>
                        <button type="button" class="btn btn-danger remove-feature" style="padding: 0.75rem;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
                container.appendChild(newFeature);
                featureIndex++;
                
                // إضافة مستمع للحذف
                newFeature.querySelector('.remove-feature').addEventListener('click', function() {
                    newFeature.remove();
                });
            });
            
            // حذف مميزة
            document.querySelectorAll('.remove-feature').forEach(btn => {
                btn.addEventListener('click', function() {
                    this.closest('.feature-form-item').remove();
                });
            });
        });
    </script>

    @if($about)
        <div class="content-card" style="margin-top: 2rem;">
            <h2 style="margin-bottom: 1rem; color: var(--text-primary);">معاينة المحتوى</h2>
            <div style="background: rgba(255, 255, 255, 0.05); padding: 1.5rem; border-radius: 12px;">
                @if($about->image)
                    <img src="{{ image_asset_url($about->image) }}" 
                         alt="صورة من نحن" 
                         style="max-width: 100%; border-radius: 12px; margin-bottom: 1rem;">
                @endif
                <p style="color: var(--text-secondary); line-height: 1.8; white-space: pre-wrap;">
                    {!! nl2br(e($about->content)) !!}
                </p>
                @if($about->cta_text)
                    <div style="margin-top: 1.5rem;">
                        <a href="{{ $about->cta_link ?? '#contact' }}" style="display: inline-block; padding: 0.75rem 1.5rem; background: var(--gradient-1); color: white; text-decoration: none; border-radius: 8px;">
                            {{ $about->cta_text }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

