<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-cog"></i> إعدادات الموقع
        </h1>
        <p class="page-subtitle">إدارة إعدادات الموقع العامة</p>
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

    @can('wesal.system-settings')
    <div style="padding: 1rem 1.25rem; background: rgba(95, 179, 142, 0.15); border: 1px solid var(--primary-color); border-radius: 12px; margin-bottom: 1.5rem;">
        <strong style="color: var(--primary-color);"><i class="fas fa-info-circle" style="margin-left: 0.5rem;"></i> إعدادات النظام (تقنية المعلومات)</strong>
        <p style="margin: 0.5rem 0 0 0; color: var(--text-secondary); font-size: 0.95rem;">
            ألوان لوحة التحكم وشكل ورقة الخطابات (رأس/وسط/تذييل) موجودان في صفحة <strong>إعدادات النظام</strong> وليس هنا.
        </p>
        <a href="{{ route('wesal.page', 'system-settings') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; margin-top: 0.75rem; padding: 0.5rem 1rem; background: var(--primary-color); color: white; border-radius: 8px; text-decoration: none; font-weight: 600;">
            <i class="fas fa-sliders-h"></i> فتح إعدادات النظام
        </a>
    </div>
    @endcan

    <!-- شريط التنقل السريع -->
    <div class="settings-nav-bar" style="position: sticky; top: 0; z-index: 100; background: rgba(15, 61, 46, 0.95); backdrop-filter: blur(20px); border-radius: 12px; padding: 1rem; margin-bottom: 2rem; border: 1px solid rgba(95, 179, 142, 0.2); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);">
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; justify-content: center;">
            <a href="#general-settings" class="settings-nav-btn" onclick="scrollToSection('general-settings'); return false;">
                <i class="fas fa-info-circle"></i> المعلومات العامة
            </a>
            <a href="#logo-settings" class="settings-nav-btn" onclick="scrollToSection('logo-settings'); return false;">
                <i class="fas fa-image"></i> الشعار
            </a>
            <a href="#hero-template" class="settings-nav-btn" onclick="scrollToSection('hero-template'); return false;">
                <i class="fas fa-layer-group"></i> قوالب الهيرو
            </a>
            <a href="#hero-background" class="settings-nav-btn" onclick="scrollToSection('hero-background'); return false;">
                <i class="fas fa-image"></i> خلفية الهيرو
            </a>
            <a href="#social-media" class="settings-nav-btn" onclick="scrollToSection('social-media'); return false;">
                <i class="fas fa-share-alt"></i> التواصل الاجتماعي
            </a>
            <a href="#section-colors" class="settings-nav-btn" onclick="scrollToSection('section-colors'); return false;">
                <i class="fas fa-palette"></i> ألوان الأقسام
            </a>
            <a href="#license" class="settings-nav-btn" onclick="scrollToSection('license'); return false;">
                <i class="fas fa-certificate"></i> الرخصة
            </a>
            <a href="#floating-buttons" class="settings-nav-btn" onclick="scrollToSection('floating-buttons'); return false;">
                <i class="fas fa-hand-pointer"></i> الأزرار المتحركة
            </a>
        </div>
    </div>

    <style>
        .settings-nav-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.2rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(95, 179, 142, 0.2);
            border-radius: 8px;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        .settings-nav-btn:hover {
            background: rgba(95, 179, 142, 0.2);
            border-color: var(--primary-color);
            color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(95, 179, 142, 0.3);
        }
        .settings-nav-btn i {
            font-size: 0.85rem;
        }
    </style>

    <script>
        function scrollToSection(sectionId) {
            const element = document.getElementById(sectionId);
            if (element) {
                const offset = 100; // مسافة من الأعلى
                const elementPosition = element.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - offset;
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        }
        function generateSectionColors(sectionKey) {
            var btn = document.getElementById('ai-btn-' + sectionKey);
            var status = document.getElementById('ai-status-' + sectionKey);
            if (btn) btn.disabled = true;
            if (status) status.textContent = 'جاري التوليد...';
            fetch('{{ settings_route("generate-section-colors", ["section" => "__SECTION__"]) }}'.replace('__SECTION__', sectionKey), {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]') ? document.querySelector('input[name="_token"]').value : '', 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({})
            }).then(function(r) { return r.json(); }).then(function(data) {
                if (data.error) { if (status) status.textContent = data.error; return; }
                ['bg_color', 'text_color', 'title_color', 'icon_color', 'card_bg_color', 'card_title_color', 'hover_text_color'].forEach(function(k) {
                    var name = 'section_' + sectionKey + '_' + k;
                    var el = document.getElementById(name);
                    var picker = document.getElementById(name + '_picker');
                    if (data[name]) { if (el) el.value = data[name]; if (picker) picker.value = data[name]; }
                });
                if (status) status.textContent = 'تم توليد الألوان. احفظ الإعدادات.';
            }).catch(function() { if (status) status.textContent = 'حدث خطأ.'; }).finally(function() { if (btn) btn.disabled = false; });
        }
        function ensureHash(v) {
            if (!v) return v;
            v = String(v).trim();
            if (v.length === 6 && /^[0-9A-Fa-f]+$/.test(v)) return '#' + v;
            if (v.charAt(0) === '#') return v;
            return v;
        }
        function generateSiteColors() {
            var btn = document.getElementById('btn-generate-site-colors');
            var status = document.getElementById('site-colors-ai-status');
            var c1 = ensureHash((document.getElementById('site_ai_color_1') || {}).value) || '#5FB38E';
            var c2 = ensureHash((document.getElementById('site_ai_color_2') || {}).value) || '#1F6B4F';
            var c3 = ensureHash((document.getElementById('site_ai_color_3') || {}).value) || '#0F3D2E';
            if (btn) btn.disabled = true;
            if (status) status.textContent = 'جاري توليد ألوان الموقع...';
            fetch('{{ settings_route("generate-site-colors") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]') ? document.querySelector('input[name="_token"]').value : '', 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ color1: c1, color2: c2, color3: c3 })
            }).then(function(r) { return r.json(); }).then(function(data) {
                if (data.error) { if (status) status.textContent = data.error; return; }
                Object.keys(data).forEach(function(key) {
                    var el = document.getElementById(key);
                    var picker = document.getElementById(key + '_picker');
                    var val = data[key];
                    if (val) {
                        if (el) { el.value = val; }
                        if (picker) { picker.value = val; }
                    }
                });
                if (status) status.textContent = 'تم توليد ألوان الموقع. احفظ الإعدادات لتطبيقها.';
            }).catch(function() { if (status) status.textContent = 'حدث خطأ في الاتصال.'; }).finally(function() { if (btn) btn.disabled = false; });
        }
    </script>

    <form id="settings-form" method="POST" action="{{ settings_update_url() }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label class="form-label">
                <i class="fas fa-heading"></i> عنوان الموقع
            </label>
            <input type="text" name="site_title" class="form-control" 
                   value="{{ old('site_title', $settings['site_title'] ?? '') }}" 
                   placeholder="أدخل عنوان الموقع">
        </div>

        <div class="form-group">
            <label class="form-label">
                <i class="fas fa-align-right"></i> وصف الموقع
            </label>
            <textarea name="site_description" class="form-control" 
                      placeholder="أدخل وصف الموقع">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">
                <i class="fas fa-image"></i> شعار الموقع
            </label>
            @if(!empty($settings['site_logo']))
                <div style="margin-bottom: 1rem;">
                    <img src="{{ image_asset_url($settings['site_logo']) }}" alt="الشعار الحالي" 
                         style="max-width: 200px; max-height: 100px; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.1);">
                </div>
            @endif
            <input type="file" name="site_logo" class="form-control" accept="image/*">
            <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                اختر صورة الشعار من جهازك (JPG, PNG, GIF) - حجم أقصى 10MB
            </small>
        </div>

        <!-- إعدادات خلفية اللوقو -->
        <div class="form-group" style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid rgba(255, 255, 255, 0.1);">
            <label class="form-label">
                <i class="fas fa-palette"></i> خلفية اللوقو
            </label>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem;">
                <!-- خلفية بيضاء -->
                <label style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.5rem; border-radius: 12px; background: rgba(255, 255, 255, 0.05); border: 2px solid {{ ($settings['logo_background_type'] ?? 'white') == 'white' ? 'var(--primary-color)' : 'transparent' }}; cursor: pointer; transition: all 0.3s ease;">
                    <input type="radio" name="logo_background_type" value="white" 
                           {{ ($settings['logo_background_type'] ?? 'white') == 'white' ? 'checked' : '' }}
                           style="width: 20px; height: 20px; cursor: pointer;">
                    <div style="width: 60px; height: 60px; background: #FFFFFF; border-radius: 12px; border: 2px solid rgba(15, 61, 46, 0.1);"></div>
                    <span style="color: rgba(255, 255, 255, 0.9); font-weight: 500; text-align: center;">خلفية بيضاء</span>
                </label>

                <!-- خلفية متدرجة -->
                <label style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.5rem; border-radius: 12px; background: rgba(255, 255, 255, 0.05); border: 2px solid {{ ($settings['logo_background_type'] ?? 'white') == 'gradient' ? 'var(--primary-color)' : 'transparent' }}; cursor: pointer; transition: all 0.3s ease;">
                    <input type="radio" name="logo_background_type" value="gradient" 
                           {{ ($settings['logo_background_type'] ?? 'white') == 'gradient' ? 'checked' : '' }}
                           style="width: 20px; height: 20px; cursor: pointer;">
                    <div style="width: 60px; height: 60px; background: var(--gradient-1); border-radius: 12px; border: 2px solid rgba(255, 255, 255, 0.2);"></div>
                    <span style="color: rgba(255, 255, 255, 0.9); font-weight: 500; text-align: center;">خلفية متدرجة</span>
                </label>

                <!-- شفاف -->
                <label style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.5rem; border-radius: 12px; background: rgba(255, 255, 255, 0.05); border: 2px solid {{ ($settings['logo_background_type'] ?? 'white') == 'transparent' ? 'var(--primary-color)' : 'transparent' }}; cursor: pointer; transition: all 0.3s ease;">
                    <input type="radio" name="logo_background_type" value="transparent" 
                           {{ ($settings['logo_background_type'] ?? 'white') == 'transparent' ? 'checked' : '' }}
                           style="width: 20px; height: 20px; cursor: pointer;">
                    <div style="width: 60px; height: 60px; background: transparent; border-radius: 12px; border: 2px dashed rgba(255, 255, 255, 0.3); position: relative;">
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 30px; height: 2px; background: rgba(255, 255, 255, 0.3);"></div>
                    </div>
                    <span style="color: rgba(255, 255, 255, 0.9); font-weight: 500; text-align: center;">شفاف</span>
                </label>

                <!-- لون مخصص -->
                <label style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.5rem; border-radius: 12px; background: rgba(255, 255, 255, 0.05); border: 2px solid {{ ($settings['logo_background_type'] ?? 'white') == 'custom' ? 'var(--primary-color)' : 'transparent' }}; cursor: pointer; transition: all 0.3s ease;">
                    <input type="radio" name="logo_background_type" value="custom" 
                           {{ ($settings['logo_background_type'] ?? 'white') == 'custom' ? 'checked' : '' }}
                           style="width: 20px; height: 20px; cursor: pointer;">
                    <div style="width: 60px; height: 60px; background: {{ $settings['logo_background_color'] ?? '#FFFFFF' }}; border-radius: 12px; border: 2px solid rgba(15, 61, 46, 0.1);"></div>
                    <span style="color: rgba(255, 255, 255, 0.9); font-weight: 500; text-align: center;">لون مخصص</span>
                </label>
            </div>

            <!-- اختيار اللون المخصص -->
            <div id="custom-color-picker" style="margin-top: 1.5rem; {{ ($settings['logo_background_type'] ?? 'white') == 'custom' ? '' : 'display: none;' }}">
                <label style="display: block; margin-bottom: 0.5rem; color: rgba(255, 255, 255, 0.9); font-weight: 500;">
                    <i class="fas fa-fill-drip" style="margin-left: 0.5rem;"></i>
                    اختر اللون المخصص
                </label>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <input type="color" name="logo_background_color" 
                           value="{{ old('logo_background_color', $settings['logo_background_color'] ?? '#FFFFFF') }}"
                           style="width: 80px; height: 50px; border-radius: 8px; border: 2px solid rgba(255, 255, 255, 0.2); cursor: pointer;">
                    <input type="text" name="logo_background_color_text" 
                           value="{{ old('logo_background_color', $settings['logo_background_color'] ?? '#FFFFFF') }}"
                           class="form-control" 
                           placeholder="#FFFFFF"
                           style="flex: 1;"
                           oninput="document.querySelector('input[name=logo_background_color]').value = this.value; document.querySelector('input[name=logo_background_type][value=custom]').parentElement.querySelector('div').style.background = this.value;">
                    <small style="color: rgba(255, 255, 255, 0.7);">أدخل كود اللون (مثل: #FFFFFF)</small>
                </div>
            </div>

            <small style="color: rgba(255, 255, 255, 0.7); margin-top: 1rem; display: block;">
                اختر نوع الخلفية لبطاقة اللوقو في الشريط العلوي
            </small>
        </div>

        <!-- إعدادات حجم اللوقو داخل البطاقة -->
        <div class="form-group" style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid rgba(255, 255, 255, 0.1);">
            <label class="form-label">
                <i class="fas fa-expand-arrows-alt"></i> حجم اللوقو داخل البطاقة (بالبكسل)
            </label>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <button type="button" class="btn btn-secondary" onclick="decreaseLogoIconSize()" style="padding: 0.5rem 1rem; min-width: 50px;">
                    <i class="fas fa-minus"></i>
                </button>
                <input type="number" 
                       name="site_logo_icon_size" 
                       id="site_logo_icon_size"
                       min="40" 
                       max="120" 
                       step="5"
                       value="{{ old('site_logo_icon_size', $settings['site_logo_icon_size'] ?? '70') }}"
                       class="form-control"
                       style="text-align: center; font-weight: 600; font-size: 1.1rem; max-width: 120px;"
                       onchange="validateLogoIconSize()">
                <button type="button" class="btn btn-secondary" onclick="increaseLogoIconSize()" style="padding: 0.5rem 1rem; min-width: 50px;">
                    <i class="fas fa-plus"></i>
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="resetLogoIconSize()" style="padding: 0.5rem 1rem; margin-right: auto;">
                    <i class="fas fa-undo"></i> إعادة تعيين
                </button>
            </div>
            <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                حجم اللوقو (الصورة أو الأيقونة) داخل بطاقة اللوقو (الحد الأدنى: 40px، الحد الأقصى: 120px)
            </small>
        </div>

        <script>
            function increaseLogoIconSize() {
                const input = document.getElementById('site_logo_icon_size');
                let value = parseInt(input.value) || 70;
                value = Math.min(120, value + 5);
                input.value = value;
            }

            function decreaseLogoIconSize() {
                const input = document.getElementById('site_logo_icon_size');
                let value = parseInt(input.value) || 70;
                value = Math.max(40, value - 5);
                input.value = value;
            }

            function resetLogoIconSize() {
                document.getElementById('site_logo_icon_size').value = 70;
            }

            function validateLogoIconSize() {
                const input = document.getElementById('site_logo_icon_size');
                let value = parseInt(input.value) || 70;
                if (value < 40) value = 40;
                if (value > 120) value = 120;
                input.value = value;
            }
        </script>

        <script>
            // إظهار/إخفاء منتقي الألوان عند اختيار لون مخصص
            document.addEventListener('DOMContentLoaded', function() {
                const radioButtons = document.querySelectorAll('input[name="logo_background_type"]');
                const colorPicker = document.getElementById('custom-color-picker');
                
                radioButtons.forEach(radio => {
                    radio.addEventListener('change', function() {
                        if (this.value === 'custom') {
                            colorPicker.style.display = 'block';
                        } else {
                            colorPicker.style.display = 'none';
                        }
                    });
                });

                // تحديث معاينة اللون عند تغيير منتقي الألوان
                const colorInput = document.querySelector('input[name="logo_background_color"]');
                const colorTextInput = document.querySelector('input[name="logo_background_color_text"]');
                
                if (colorInput && colorTextInput) {
                    colorInput.addEventListener('input', function() {
                        colorTextInput.value = this.value;
                        const previewDiv = document.querySelector('input[name="logo_background_type"][value="custom"]').parentElement.querySelector('div');
                        if (previewDiv) {
                            previewDiv.style.background = this.value;
                        }
                    });
                }
            });
        </script>

        <div class="form-group">
            <label class="form-label">
                <i class="fas fa-rocket"></i> أيقونة الموقع
            </label>
            <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 0.5rem; color: rgba(255, 255, 255, 0.8);">رفع أيقونة (صورة)</label>
                    <input type="file" name="site_icon_file" class="form-control" accept="image/*">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 0.5rem; color: rgba(255, 255, 255, 0.8);">أو استخدام Font Awesome</label>
                    <input type="text" name="site_icon" class="form-control" 
                           value="{{ old('site_icon', $settings['site_icon'] ?? 'fas fa-rocket') }}" 
                           placeholder="مثال: fas fa-rocket">
                </div>
            </div>
            @if(!empty($settings['site_icon_file']))
                <div style="margin-top: 1rem;">
                    <img src="{{ image_asset_url($settings['site_icon_file']) }}" alt="الأيقونة الحالية" 
                         style="max-width: 80px; max-height: 80px; border-radius: 50%; border: 2px solid rgba(95, 179, 142, 0.3);">
                </div>
            @endif
            <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                يمكنك رفع صورة أيقونة (حجم أقصى 10MB) أو استخدام أيقونة Font Awesome
            </small>
        </div>

        <div class="form-group">
            <label class="form-label">
                <i class="fas fa-envelope"></i> البريد الإلكتروني
            </label>
            <input type="email" name="contact_email" class="form-control" 
                   value="{{ old('contact_email', $settings['contact_email'] ?? '') }}" 
                   placeholder="example@domain.com">
        </div>

        <div class="form-group">
            <label class="form-label">
                <i class="fas fa-phone"></i> رقم الهاتف
            </label>
            <input type="text" name="contact_phone" class="form-control" 
                   value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}" 
                   placeholder="+966500000000">
        </div>

        <div class="form-group">
            <label class="form-label">
                <i class="fas fa-map-marker-alt"></i> العنوان
            </label>
            <input type="text" name="contact_address" class="form-control" 
                   value="{{ old('contact_address', $settings['contact_address'] ?? '') }}" 
                   placeholder="حائل، المملكة العربية السعودية">
        </div>

        <div class="form-group">
            <label class="form-label">
                <i class="fas fa-calendar-alt"></i> أيام العمل
            </label>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 0.75rem; margin-top: 0.75rem;">
                @php
                    $days = [
                        'sunday' => 'الأحد',
                        'monday' => 'الاثنين',
                        'tuesday' => 'الثلاثاء',
                        'wednesday' => 'الأربعاء',
                        'thursday' => 'الخميس',
                        'friday' => 'الجمعة',
                        'saturday' => 'السبت'
                    ];
                    $selectedDays = json_decode($settings['working_days'] ?? '[]', true) ?: [];
                @endphp
                @foreach($days as $key => $day)
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 0.5rem; border-radius: 8px; background: rgba(255, 255, 255, 0.05); transition: all 0.3s ease;">
                    <input type="checkbox" name="working_days[]" value="{{ $key }}" 
                           {{ in_array($key, $selectedDays) ? 'checked' : '' }}
                           style="width: 18px; height: 18px; cursor: pointer;">
                    <span style="color: rgba(255, 255, 255, 0.9); font-size: 0.95rem;">{{ $day }}</span>
                </label>
                @endforeach
            </div>
            <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                اختر أيام العمل من الأسبوع
            </small>
        </div>

        <div class="form-group">
            <label class="form-label">
                <i class="fas fa-clock"></i> ساعات العمل
            </label>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 0.75rem;">
                <div>
                    <label style="display: block; color: rgba(255, 255, 255, 0.8); margin-bottom: 0.5rem; font-size: 0.9rem;">من</label>
                    <input type="time" name="working_hours_from" class="form-control" 
                           value="{{ old('working_hours_from', $settings['working_hours_from'] ?? '08:00') }}">
                </div>
                <div>
                    <label style="display: block; color: rgba(255, 255, 255, 0.8); margin-bottom: 0.5rem; font-size: 0.9rem;">إلى</label>
                    <input type="time" name="working_hours_to" class="form-control" 
                           value="{{ old('working_hours_to', $settings['working_hours_to'] ?? '16:00') }}">
                </div>
            </div>
            <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                حدد ساعات العمل (من - إلى) - سيتم عرضها بنظام 12 ساعة (ص/م)
            </small>
            @php
                $previewDays = json_decode($settings['working_days'] ?? '[]', true) ?: [];
                $previewFrom = $settings['working_hours_from'] ?? '';
                $previewTo = $settings['working_hours_to'] ?? '';
            @endphp
            @if(!empty($previewDays) && !empty($previewFrom) && !empty($previewTo))
            <div style="margin-top: 1rem; padding: 1rem; background: rgba(95, 179, 142, 0.1); border-radius: 8px; border: 1px solid rgba(95, 179, 142, 0.3);">
                <strong style="color: rgba(255, 255, 255, 0.9); display: block; margin-bottom: 0.5rem;">معاينة:</strong>
                @php
                    $daysMap = [
                        'sunday' => 'الأحد',
                        'monday' => 'الاثنين',
                        'tuesday' => 'الثلاثاء',
                        'wednesday' => 'الأربعاء',
                        'thursday' => 'الخميس',
                        'friday' => 'الجمعة',
                        'saturday' => 'السبت'
                    ];
                    $dayOrder = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                    $sortedDays = array_values(array_intersect($dayOrder, $previewDays));
                    
                    $dayRanges = [];
                    if (count($sortedDays) > 0) {
                        $start = $sortedDays[0];
                        $end = $sortedDays[0];
                        
                        for ($i = 1; $i < count($sortedDays); $i++) {
                            $currentIndex = array_search($sortedDays[$i], $dayOrder);
                            $prevIndex = array_search($sortedDays[$i-1], $dayOrder);
                            
                            if ($currentIndex == $prevIndex + 1) {
                                $end = $sortedDays[$i];
                            } else {
                                if ($start == $end) {
                                    $dayRanges[] = $daysMap[$start];
                                } else {
                                    $dayRanges[] = $daysMap[$start] . ' - ' . $daysMap[$end];
                                }
                                $start = $sortedDays[$i];
                                $end = $sortedDays[$i];
                            }
                        }
                        
                        if ($start == $end) {
                            $dayRanges[] = $daysMap[$start];
                        } else {
                            $dayRanges[] = $daysMap[$start] . ' - ' . $daysMap[$end];
                        }
                    }
                    
                    $daysDisplay = implode('، ', $dayRanges);
                    
                    // Convert to 12-hour format
                    $fromTime = date('g:i', strtotime($previewFrom));
                    $fromPeriod = date('A', strtotime($previewFrom)) == 'AM' ? 'ص' : 'م';
                    $toTime = date('g:i', strtotime($previewTo));
                    $toPeriod = date('A', strtotime($previewTo)) == 'AM' ? 'ص' : 'م';
                    
                    $timeDisplay = $fromTime . ' ' . $fromPeriod . ' - ' . $toTime . ' ' . $toPeriod;
                @endphp
                <div style="color: rgba(255, 255, 255, 0.9);">
                    <span style="font-weight: 600;">{{ $daysDisplay }}</span>
                    <span style="color: var(--primary-color); margin-right: 0.5rem; margin-left: 0.5rem;">|</span>
                    <span style="color: var(--primary-color); font-weight: 600;">{{ $timeDisplay }}</span>
                </div>
            </div>
            @endif
        </div>

        <div class="form-group">
            <label class="form-label">
                <i class="fas fa-map-marker-alt"></i> رابط موقع Google Maps
            </label>
            <input type="url" name="google_maps_link" class="form-control" 
                   value="{{ old('google_maps_link', $settings['google_maps_link'] ?? '') }}" 
                   placeholder="https://maps.google.com/...">
            <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                رابط موقع الجمعية على Google Maps (سيظهر زر "موقع الجمعية" في الفوتر تحت ساعات العمل)
            </small>
        </div>

        <div class="form-group">
            <label class="form-label">
                <i class="fas fa-file-alt"></i> وصف الموقع (للفوتر)
            </label>
            <textarea name="site_description_footer" class="form-control" rows="3"
                      placeholder="وصف مختصر للموقع يظهر في الفوتر">{{ old('site_description_footer', $settings['site_description_footer'] ?? '') }}</textarea>
        </div>

        <!-- قسم قوالب الهيرو -->
        <div id="hero-template" style="scroll-margin-top: 120px;">
        <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-top: 2rem; padding: 2rem; border-radius: 16px; border: 2px solid rgba(95, 179, 142, 0.2);">
            <h2 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-palette" style="color: var(--primary-color);"></i>
                قوالب قسم الهيرو
            </h2>
            <p style="color: var(--text-secondary); margin-bottom: 2rem; font-size: 0.95rem;">
                اختر نوع القالب الذي تريد استخدامه في قسم الهيرو
            </p>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-layer-group"></i> نوع القالب
                </label>
                <select name="hero_template_type" class="form-control" id="hero_template_type" onchange="toggleHeroTemplateSections()">
                    <option value="default" {{ old('hero_template_type', $settings['hero_template_type'] ?? 'default') === 'default' ? 'selected' : '' }}>القالب الأساسي (صورة/فيديو مع محتوى)</option>
                    <option value="video" {{ old('hero_template_type', $settings['hero_template_type'] ?? 'default') === 'video' ? 'selected' : '' }}>قالب الفيديو (فيديو فقط)</option>
                    <option value="slider" {{ old('hero_template_type', $settings['hero_template_type'] ?? 'default') === 'slider' ? 'selected' : '' }}>قالب الصور المتحركة (سلايدر)</option>
                </select>
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    اختر نوع القالب الذي تريد عرضه في قسم الهيرو
                </small>
            </div>

            <!-- إعدادات حجم أيقونة الهيرو -->
            <div class="form-group" style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid rgba(255, 255, 255, 0.1);">
                <label class="form-label">
                    <i class="fas fa-expand-arrows-alt"></i> حجم أيقونة الهيرو (بالبكسل)
                </label>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <button type="button" class="btn btn-secondary" onclick="decreaseHeroIconSize()" style="padding: 0.5rem 1rem; min-width: 50px;">
                        <i class="fas fa-minus"></i>
                    </button>
                    <input type="number" 
                           name="site_hero_icon_size" 
                           id="site_hero_icon_size"
                           min="120" 
                           max="350" 
                           step="10"
                           value="{{ old('site_hero_icon_size', $settings['site_hero_icon_size'] ?? '200') }}"
                           class="form-control"
                           style="text-align: center; font-weight: 600; font-size: 1.1rem; max-width: 120px;"
                           onchange="validateHeroIconSize()">
                    <button type="button" class="btn btn-secondary" onclick="increaseHeroIconSize()" style="padding: 0.5rem 1rem; min-width: 50px;">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="resetHeroIconSize()" style="padding: 0.5rem 1rem; margin-right: auto;">
                        <i class="fas fa-undo"></i> إعادة تعيين
                    </button>
                </div>
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    حجم أيقونة الكوكب في قسم الهيرو (الحد الأدنى: 120px، الحد الأقصى: 350px)
                </small>
            </div>

            <!-- حجم خط عنوان الموقع في الهيرو -->
            <div class="form-group" style="margin-top: 1.5rem;">
                <label class="form-label">
                    <i class="fas fa-text-height"></i> حجم خط عنوان الموقع في الهيرو (بالبكسل)
                </label>
                <select name="hero_title_font_size" id="hero_title_font_size" class="form-control" style="max-width: 220px;">
                    @php $currentSize = old('hero_title_font_size', $settings['hero_title_font_size'] ?? '56'); @endphp
                    <option value="24" {{ $currentSize == '24' ? 'selected' : '' }}>24px - صغير جداً</option>
                    <option value="28" {{ $currentSize == '28' ? 'selected' : '' }}>28px - صغير</option>
                    <option value="32" {{ $currentSize == '32' ? 'selected' : '' }}>32px</option>
                    <option value="40" {{ $currentSize == '40' ? 'selected' : '' }}>40px</option>
                    <option value="48" {{ $currentSize == '48' ? 'selected' : '' }}>48px</option>
                    <option value="56" {{ $currentSize == '56' ? 'selected' : '' }}>56px - افتراضي</option>
                    <option value="64" {{ $currentSize == '64' ? 'selected' : '' }}>64px</option>
                    <option value="72" {{ $currentSize == '72' ? 'selected' : '' }}>72px - كبير</option>
                    <option value="84" {{ $currentSize == '84' ? 'selected' : '' }}>84px - كبير جداً</option>
                </select>
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    يتحكم في حجم نص عنوان الموقع الظاهر في قسم الهيرو (القالب الأساسي وقالب الفيديو)
                </small>
            </div>
        </div>

        <script>
            function increaseHeroIconSize() {
                const input = document.getElementById('site_hero_icon_size');
                let value = parseInt(input.value) || 200;
                value = Math.min(350, value + 10);
                input.value = value;
            }

            function decreaseHeroIconSize() {
                const input = document.getElementById('site_hero_icon_size');
                let value = parseInt(input.value) || 200;
                value = Math.max(120, value - 10);
                input.value = value;
            }

            function resetHeroIconSize() {
                document.getElementById('site_hero_icon_size').value = 200;
            }

            function validateHeroIconSize() {
                const input = document.getElementById('site_hero_icon_size');
                let value = parseInt(input.value) || 200;
                if (value < 120) value = 120;
                if (value > 350) value = 350;
                input.value = value;
            }
        </script>

        <!-- فيديو منبثق عند الدخول للموقع -->
        <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-top: 2rem; padding: 2rem; border-radius: 16px; border: 2px solid rgba(95, 179, 142, 0.2);">
            <h2 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-video" style="color: var(--primary-color);"></i>
                فيديو منبثق عند الدخول للموقع
            </h2>
            <p style="color: var(--text-secondary); margin-bottom: 1.5rem; font-size: 0.95rem;">
                يظهر نافذة صغيرة بفيديو (يوتيوب أو فيديو مرفوع) على يمين أو يسار الصفحة عند دخول الزائر، مع زر إغلاق (×) لإبعادها.
            </p>
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                    <input type="checkbox" name="popup_video_enabled" value="1" {{ ($settings['popup_video_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                    <span style="color: var(--text-primary); font-weight: 600;">تفعيل الفيديو المنبثق</span>
                </label>
            </div>
            <div class="form-group">
                <label class="form-label">رابط فيديو يوتيوب (اختياري)</label>
                <input type="text" name="popup_video_url" class="form-control" 
                       value="{{ old('popup_video_url', $settings['popup_video_url'] ?? '') }}" 
                       placeholder="https://www.youtube.com/watch?v=... أو https://youtu.be/...">
            </div>
            <div class="form-group">
                <label class="form-label">أو رفع فيديو (mp4, webm, ogg — حد أقصى 20 ميجا)</label>
                @if(!empty($settings['popup_video_file']))
                    <div style="margin-bottom: 0.75rem;">
                        <span style="color: var(--text-secondary);">فيديو حالياً مرفوع.</span>
                        <label style="display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-right: 1rem;">
                            <input type="checkbox" name="popup_video_file_remove" value="1">
                            <span style="color: rgba(255, 255, 255, 0.7);">حذف الفيديو الحالي</span>
                        </label>
                    </div>
                @endif
                <input type="file" name="popup_video_file" class="form-control" accept="video/mp4,video/webm,video/ogg">
            </div>
            <div class="form-group">
                <label class="form-label">موضع النافذة</label>
                <select name="popup_video_position" class="form-control">
                    <option value="right" {{ old('popup_video_position', $settings['popup_video_position'] ?? 'right') === 'right' ? 'selected' : '' }}>يمين الموقع</option>
                    <option value="left" {{ old('popup_video_position', $settings['popup_video_position'] ?? 'right') === 'left' ? 'selected' : '' }}>يسار الموقع</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">مقاس الفيديو</label>
                <select name="popup_video_size" class="form-control">
                    <option value="small" {{ old('popup_video_size', $settings['popup_video_size'] ?? 'medium') === 'small' ? 'selected' : '' }}>صغير (260px)</option>
                    <option value="medium" {{ old('popup_video_size', $settings['popup_video_size'] ?? 'medium') === 'medium' ? 'selected' : '' }}>متوسط (320px)</option>
                    <option value="large" {{ old('popup_video_size', $settings['popup_video_size'] ?? 'medium') === 'large' ? 'selected' : '' }}>كبير (420px)</option>
                </select>
            </div>
        </div>

        <!-- قسم خلفية الهيرو -->
        <div id="hero-background" style="scroll-margin-top: 120px;">
        <div class="content-card hero-template-section hero-template-default-section" style="background: rgba(255, 255, 255, 0.08); margin-top: 2rem; padding: 2rem; border-radius: 16px; border: 2px solid rgba(95, 179, 142, 0.2);">
            <h2 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-image" style="color: var(--primary-color);"></i>
                خلفية قسم الهيرو (القالب الأساسي)
            </h2>
            <p style="color: var(--text-secondary); margin-bottom: 2rem; font-size: 0.95rem;">
                قم برفع صورة خلفية لقسم الهيرو وضبط شفافيتها
            </p>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-image"></i> صورة الخلفية
                </label>
                @if(!empty($settings['hero_background_image']))
                    <div style="margin-bottom: 1rem;">
                        <img src="{{ image_asset_url($settings['hero_background_image']) }}" alt="صورة الخلفية الحالية" 
                             style="max-width: 100%; max-height: 300px; border-radius: 12px; border: 2px solid rgba(255, 255, 255, 0.1); object-fit: cover; background: rgba(255, 255, 255, 0.05); padding: 1rem;">
                    </div>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-bottom: 1rem;">
                        <input type="checkbox" name="hero_background_image_remove" value="1">
                        <span style="color: rgba(255, 255, 255, 0.7);">حذف الصورة الحالية</span>
                    </label>
                @endif
                <input type="file" name="hero_background_image" class="form-control" accept="image/*">
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    اختر صورة خلفية من جهازك (JPG, PNG, GIF, SVG) - حجم أقصى 10MB
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-adjust"></i> شفافية الصورة (0 - 100)
                </label>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <input type="range" 
                           name="hero_background_opacity" 
                           id="hero_background_opacity"
                           min="0" 
                           max="100" 
                           value="{{ old('hero_background_opacity', $settings['hero_background_opacity'] ?? '30') }}"
                           class="form-control"
                           style="flex: 1;"
                           oninput="document.getElementById('hero_opacity_value').textContent = this.value + '%'">
                    <span id="hero_opacity_value" style="min-width: 50px; text-align: center; color: var(--primary-color); font-weight: 600;">
                        {{ old('hero_background_opacity', $settings['hero_background_opacity'] ?? '30') }}%
                    </span>
                </div>
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    قم بضبط شفافية الصورة (0 = شفافة تماماً، 100 = غير شفافة)
                </small>
            </div>

            <div class="form-group" style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid rgba(255, 255, 255, 0.1);">
                <label class="form-label">
                    <i class="fas fa-video"></i> فيديو الخلفية (تشغيل تلقائي)
                </label>
                @if(!empty($settings['hero_background_video']))
                    <div style="margin-bottom: 1rem;">
                        <video controls style="max-width: 100%; max-height: 300px; border-radius: 12px; border: 2px solid rgba(255, 255, 255, 0.1); background: rgba(255, 255, 255, 0.05); padding: 1rem;">
                            <source src="{{ image_asset_url($settings['hero_background_video']) }}" type="video/mp4">
                        </video>
                    </div>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-bottom: 1rem;">
                        <input type="checkbox" name="hero_background_video_remove" value="1">
                        <span style="color: rgba(255, 255, 255, 0.7);">حذف الفيديو الحالي</span>
                    </label>
                @endif
                <input type="file" name="hero_background_video" class="form-control" accept="video/*">
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    اختر فيديو خلفية من جهازك (MP4, WebM, OGG) - حجم أقصى 50MB. سيتم تشغيله تلقائياً بشكل متكرر
                </small>
            </div>

            @if(!empty($settings['hero_background_video']))
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-adjust"></i> شفافية الفيديو (0 - 100)
                </label>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <input type="range" 
                           name="hero_background_video_opacity" 
                           id="hero_background_video_opacity"
                           min="0" 
                           max="100" 
                           value="{{ old('hero_background_video_opacity', $settings['hero_background_video_opacity'] ?? '50') }}"
                           class="form-control"
                           style="flex: 1;"
                           oninput="document.getElementById('hero_video_opacity_value').textContent = this.value + '%'">
                    <span id="hero_video_opacity_value" style="min-width: 50px; text-align: center; color: var(--primary-color); font-weight: 600;">
                        {{ old('hero_background_video_opacity', $settings['hero_background_video_opacity'] ?? '50') }}%
                    </span>
                </div>
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    قم بضبط شفافية الفيديو (0 = شفاف تماماً، 100 = غير شفاف)
                </small>
            </div>
            @endif
        </div>

        <!-- قسم قالب الفيديو -->
        <div class="content-card hero-template-section hero-template-video-section" style="background: rgba(255, 255, 255, 0.08); margin-top: 2rem; padding: 2rem; border-radius: 16px; border: 2px solid rgba(95, 179, 142, 0.2); display: none;">
            <h2 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-video" style="color: var(--primary-color);"></i>
                إعدادات قالب الفيديو
            </h2>
            <p style="color: var(--text-secondary); margin-bottom: 2rem; font-size: 0.95rem;">
                قم برفع فيديو للهيرو (سيتم تشغيله تلقائياً فقط)
            </p>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-video"></i> فيديو الهيرو
                </label>
                @if(!empty($settings['hero_background_video']))
                    <div style="margin-bottom: 1rem;">
                        <video controls style="max-width: 100%; max-height: 300px; border-radius: 12px; border: 2px solid rgba(255, 255, 255, 0.1); background: rgba(255, 255, 255, 0.05); padding: 1rem;">
                            <source src="{{ image_asset_url($settings['hero_background_video']) }}" type="video/mp4">
                        </video>
                    </div>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-bottom: 1rem;">
                        <input type="checkbox" name="hero_background_video_remove" value="1">
                        <span style="color: rgba(255, 255, 255, 0.7);">حذف الفيديو الحالي</span>
                    </label>
                @endif
                <input type="file" name="hero_background_video" class="form-control" accept="video/*">
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    اختر فيديو للهيرو (MP4, WebM, OGG) - حجم أقصى 50MB. سيتم تشغيله تلقائياً بشكل متكرر
                </small>
            </div>

            @if(!empty($settings['hero_background_video']))
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-adjust"></i> شفافية الفيديو (0 - 100)
                </label>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <input type="range" 
                           name="hero_background_video_opacity" 
                           id="hero_background_video_opacity_template"
                           min="0" 
                           max="100" 
                           value="{{ old('hero_background_video_opacity', $settings['hero_background_video_opacity'] ?? '100') }}"
                           class="form-control"
                           style="flex: 1;"
                           oninput="document.getElementById('hero_video_opacity_value_template').textContent = this.value + '%'">
                    <span id="hero_video_opacity_value_template" style="min-width: 50px; text-align: center; color: var(--primary-color); font-weight: 600;">
                        {{ old('hero_background_video_opacity', $settings['hero_background_video_opacity'] ?? '100') }}%
                    </span>
                </div>
            </div>
            @endif

            <!-- إعدادات قالب الفيديو -->
            <div class="form-group" style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid rgba(255, 255, 255, 0.1);">
                <h3 style="color: var(--text-primary); margin-bottom: 1.5rem; font-size: 1.1rem;">
                    <i class="fas fa-cog" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
                    إعدادات قالب الفيديو
                </h3>

                <!-- خيار خلفية النص -->
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; margin-bottom: 0.5rem;">
                        <input type="checkbox" 
                               name="hero_video_title_background" 
                               value="1"
                               {{ old('hero_video_title_background', $settings['hero_video_title_background'] ?? '1') == '1' ? 'checked' : '' }}
                               style="width: 20px; height: 20px; cursor: pointer;">
                        <span style="color: var(--text-primary); font-weight: 500;">
                            <i class="fas fa-paint-brush" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
                            إظهار خلفية نص اسم الموقع
                        </span>
                    </label>
                    <small style="color: rgba(255, 255, 255, 0.7); margin-right: 2rem; display: block;">
                        عند تفعيل هذا الخيار، سيظهر اسم الموقع بخلفية شفافة. عند إلغاء التفعيل، سيظهر النص بدون خلفية.
                    </small>
                </div>

                <!-- خيار زر التواصل -->
                <div class="form-group" style="margin-top: 1.5rem;">
                    <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; margin-bottom: 0.5rem;">
                        <input type="checkbox" 
                               name="hero_video_show_contact_button" 
                               value="1"
                               {{ old('hero_video_show_contact_button', $settings['hero_video_show_contact_button'] ?? '0') == '1' ? 'checked' : '' }}
                               style="width: 20px; height: 20px; cursor: pointer;">
                        <span style="color: var(--text-primary); font-weight: 500;">
                            <i class="fas fa-phone-alt" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
                            إظهار زر التواصل في الهيرو
                        </span>
                    </label>
                    <small style="color: rgba(255, 255, 255, 0.7); margin-right: 2rem; display: block;">
                        عند تفعيل هذا الخيار، سيظهر زر "تواصل معنا" في قالب الفيديو.
                    </small>
                </div>
            </div>
        </div>

        <!-- قسم قالب السلايدر -->
        <div class="content-card hero-template-section hero-template-slider-section" style="background: rgba(255, 255, 255, 0.08); margin-top: 2rem; padding: 2rem; border-radius: 16px; border: 2px solid rgba(95, 179, 142, 0.2); display: none;">
            <h2 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-images" style="color: var(--primary-color);"></i>
                إدارة صور السلايدر
            </h2>
            <p style="color: var(--text-secondary); margin-bottom: 2rem; font-size: 0.95rem;">
                قم بإضافة صور للسلايدر المتحرك في الهيرو
            </p>

            <!-- الصور الحالية -->
            @if(isset($heroSliderImages) && $heroSliderImages->count() > 0)
            <div style="margin-bottom: 2rem;">
                <h3 style="color: var(--text-primary); margin-bottom: 1rem; font-size: 1.1rem;">الصور الحالية</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem;">
                    @foreach($heroSliderImages as $sliderImage)
                    <div style="position: relative; border: 2px solid rgba(255, 255, 255, 0.1); border-radius: 12px; overflow: hidden; background: rgba(255, 255, 255, 0.05);">
                        <img src="{{ image_asset_url($sliderImage->image) }}" alt="{{ $sliderImage->title ?? 'صورة السلايدر' }}" 
                             style="width: 100%; height: 150px; object-fit: cover;">
                        <div style="padding: 0.75rem;">
                            @if($sliderImage->title)
                            <p style="color: var(--text-primary); font-weight: 600; margin-bottom: 0.25rem; font-size: 0.9rem;">{{ $sliderImage->title }}</p>
                            @endif
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-top: 0.5rem;">
                                <input type="checkbox" name="hero_slider_delete[]" value="{{ $sliderImage->id }}">
                                <span style="color: rgba(255, 255, 255, 0.7); font-size: 0.85rem;">حذف</span>
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- إضافة صور جديدة -->
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-plus-circle"></i> إضافة صور جديدة
                </label>
                <input type="file" name="hero_slider_images[]" class="form-control" accept="image/*" multiple id="hero_slider_images_input">
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    يمكنك اختيار عدة صور في المرة الواحدة (JPG, PNG, GIF, SVG) - حجم أقصى 10MB لكل صورة
                </small>
            </div>

            <!-- معاينة الصور المختارة -->
            <div id="hero_slider_preview" style="display: none; margin-top: 1.5rem;">
                <h3 style="color: var(--text-primary); margin-bottom: 1rem; font-size: 1.1rem;">معاينة الصور المختارة</h3>
                <div id="hero_slider_preview_container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem;"></div>
            </div>
        </div>

            <div class="form-group" style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px solid rgba(255, 255, 255, 0.1);">
                <button type="button" 
                        onclick="resetHeroBackground()" 
                        class="btn btn-secondary"
                        style="background: rgba(239, 68, 68, 0.2); border-color: rgba(239, 68, 68, 0.3); color: #ef4444;">
                    <i class="fas fa-undo"></i>
                    إعادة الإعدادات الافتراضية
                </button>
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    سيتم حذف الصورة الخلفية وإرجاع الخلفية الافتراضية (النجوم والتدرج)
                </small>
            </div>
        </div>

        </div>

        <!-- قسم التواصل الاجتماعي -->
        <div id="social-media" style="scroll-margin-top: 120px;">
        <div class="form-section" style="margin-top: 3rem; padding-top: 2rem; border-top: 2px solid rgba(255, 255, 255, 0.1);">
            <h2 style="margin-bottom: 1.5rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-share-alt"></i>
                <span>حسابات التواصل الاجتماعي</span>
            </h2>
            <p style="color: rgba(255, 255, 255, 0.7); margin-bottom: 2rem; font-size: 0.95rem;">
                أضف روابط حساباتك على منصات التواصل الاجتماعي. ستظهر الأيقونات تلقائياً في قسم الهيرو عند إضافة الروابط.
            </p>

            <div class="form-group">
                <label class="form-label">
                    <i class="fab fa-facebook-f"></i> Facebook
                </label>
                <input type="url" name="social_facebook" class="form-control" 
                       value="{{ old('social_facebook', $settings['social_facebook'] ?? '') }}" 
                       placeholder="https://facebook.com/yourpage">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="width: 1em; height: 1em; vertical-align: -0.125em; display: inline-block; margin-left: 0.5rem;">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg> X (Twitter)
                </label>
                <input type="url" name="social_twitter" class="form-control" 
                       value="{{ old('social_twitter', $settings['social_twitter'] ?? '') }}" 
                       placeholder="https://twitter.com/yourhandle">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fab fa-instagram"></i> Instagram
                </label>
                <input type="url" name="social_instagram" class="form-control" 
                       value="{{ old('social_instagram', $settings['social_instagram'] ?? '') }}" 
                       placeholder="https://instagram.com/yourhandle">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fab fa-linkedin-in"></i> LinkedIn
                </label>
                <input type="url" name="social_linkedin" class="form-control" 
                       value="{{ old('social_linkedin', $settings['social_linkedin'] ?? '') }}" 
                       placeholder="https://linkedin.com/company/yourcompany">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fab fa-youtube"></i> YouTube
                </label>
                <input type="url" name="social_youtube" class="form-control" 
                       value="{{ old('social_youtube', $settings['social_youtube'] ?? '') }}" 
                       placeholder="https://youtube.com/channel/yourchannel">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </label>
                <input type="text" name="social_whatsapp" class="form-control" 
                       value="{{ old('social_whatsapp', $settings['social_whatsapp'] ?? '') }}" 
                       placeholder="966500000000 (بدون + أو مسافات)">
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    أدخل رقم الهاتف فقط (مثال: 966500000000)
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fab fa-telegram-plane"></i> Telegram
                </label>
                <input type="url" name="social_telegram" class="form-control" 
                       value="{{ old('social_telegram', $settings['social_telegram'] ?? '') }}" 
                       placeholder="https://t.me/yourchannel">
            </div>
        </div>

        </div>

        @include('dashboard.partials.section-colors-form')

        <!-- قسم صورة الرخصة -->
        <div id="license" style="scroll-margin-top: 120px;">
        <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-top: 2rem; padding: 2rem;">
            <h2 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-certificate" style="color: var(--primary-color);"></i>
                رخصة الجمعية
            </h2>
            <p style="color: var(--text-secondary); margin-bottom: 1.5rem; font-size: 0.95rem;">
                رفع صورة رخصة الجمعية (ستظهر في الفوتر)
            </p>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-image"></i> صورة الرخصة
                </label>
                @if(!empty($settings['license_image']))
                    <div style="margin-bottom: 1rem;">
                        <img src="{{ image_asset_url($settings['license_image']) }}" alt="صورة الرخصة الحالية" 
                             style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 2px solid rgba(255, 255, 255, 0.1); object-fit: contain; background: rgba(255, 255, 255, 0.05); padding: 1rem;">
                    </div>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-bottom: 1rem;">
                        <input type="checkbox" name="license_image_remove" value="1">
                        <span style="color: rgba(255, 255, 255, 0.7);">حذف الصورة الحالية</span>
                    </label>
                @endif
                <input type="file" name="license_image" class="form-control" accept="image/*">
                <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                    اختر صورة الرخصة من جهازك (JPG, PNG, GIF, SVG) - حجم أقصى 10MB
                </small>
            </div>
            </div>
        </div>

        <!-- قسم الأزرار المتحركة -->
        <div id="floating-buttons" style="scroll-margin-top: 120px;">
        <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-top: 2rem; padding: 2rem;">
            <h2 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-mobile-alt" style="color: var(--primary-color);"></i>
                الأزرار المتحركة
            </h2>
            <p style="color: var(--text-secondary); margin-bottom: 2rem; font-size: 0.95rem;">
                إدارة الأزرار المتحركة التي تظهر مع التمرير في الصفحة
            </p>

            <div style="display: grid; gap: 2rem;">
                <!-- زر الواتساب -->
                <div style="background: rgba(255, 255, 255, 0.05); padding: 1.5rem; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.1);">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                        <i class="fab fa-whatsapp" style="font-size: 2rem; color: #25D366;"></i>
                        <h3 style="color: var(--text-primary); margin: 0;">زر الواتساب</h3>
                    </div>
                    
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-bottom: 1rem;">
                            <input type="checkbox" name="floating_whatsapp_enabled" value="1" 
                                   {{ ($settings['floating_whatsapp_enabled'] ?? '1') == '1' ? 'checked' : '' }}>
                            <span style="color: var(--text-primary); font-weight: 600;">تفعيل زر الواتساب</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-phone"></i> رقم الواتساب
                        </label>
                        <input type="text" name="floating_whatsapp_number" class="form-control" 
                               value="{{ old('floating_whatsapp_number', $settings['floating_whatsapp_number'] ?? '') }}" 
                               placeholder="966500000000 (بدون + أو مسافات)">
                        <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                            أدخل رقم الهاتف فقط (مثال: 966500000000)
                        </small>
                    </div>
                </div>

                <!-- زر التبرع -->
                <div style="background: rgba(255, 255, 255, 0.05); padding: 1.5rem; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.1);">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                        <i class="fas fa-heart" style="font-size: 2rem; color: #ef4444;"></i>
                        <h3 style="color: var(--text-primary); margin: 0;">زر التبرع</h3>
                    </div>
                    
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-bottom: 1rem;">
                            <input type="checkbox" name="floating_donate_enabled" value="1" 
                                   {{ ($settings['floating_donate_enabled'] ?? '1') == '1' ? 'checked' : '' }}>
                            <span style="color: var(--text-primary); font-weight: 600;">تفعيل زر التبرع</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-link"></i> رابط التبرع
                        </label>
                        <input type="url" name="floating_donate_link" class="form-control" 
                               value="{{ old('floating_donate_link', $settings['floating_donate_link'] ?? '') }}" 
                               placeholder="https://example.com/donate">
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-font"></i> نص الزر
                        </label>
                        <input type="text" name="floating_donate_text" class="form-control" 
                               value="{{ old('floating_donate_text', $settings['floating_donate_text'] ?? 'تبرع الآن') }}" 
                               placeholder="تبرع الآن">
                    </div>
                </div>
            </div>
        </div>

        <!-- قسم إظهار/إخفاء الأقسام -->
        <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-top: 2rem; padding: 2rem;">
            <h2 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-eye" style="color: var(--primary-color);"></i>
                إظهار/إخفاء الأقسام
            </h2>
            <p style="color: var(--text-secondary); margin-bottom: 2rem; font-size: 0.95rem;">
                تحكم في إظهار أو إخفاء أقسام الموقع الرئيسية
            </p>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                <!-- قسم من نحن -->
                <div style="background: rgba(255, 255, 255, 0.05); padding: 1.5rem; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.1);">
                    <label style="display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <i class="fas fa-info-circle" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                            <div>
                                <h4 style="color: var(--text-primary); margin: 0; font-size: 1.1rem; font-weight: 600;">قسم من نحن</h4>
                                <p style="color: var(--text-secondary); margin: 0.25rem 0 0; font-size: 0.9rem;">إظهار/إخفاء قسم من نحن</p>
                            </div>
                        </div>
                        <input type="checkbox" name="section_about_visible" value="1" 
                               {{ ($settings['section_about_visible'] ?? '1') == '1' ? 'checked' : '' }}
                               style="width: 24px; height: 24px; cursor: pointer;">
                    </label>
                </div>

                <!-- قسم الرؤية والرسالة -->
                <div style="background: rgba(255, 255, 255, 0.05); padding: 1.5rem; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.1);">
                    <label style="display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <i class="fas fa-eye" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                            <div>
                                <h4 style="color: var(--text-primary); margin: 0; font-size: 1.1rem; font-weight: 600;">الرؤية والرسالة</h4>
                                <p style="color: var(--text-secondary); margin: 0.25rem 0 0; font-size: 0.9rem;">إظهار/إخفاء قسم الرؤية والرسالة</p>
                            </div>
                        </div>
                        <input type="checkbox" name="section_vision_mission_visible" value="1" 
                               {{ ($settings['section_vision_mission_visible'] ?? '1') == '1' ? 'checked' : '' }}
                               style="width: 24px; height: 24px; cursor: pointer;">
                    </label>
                </div>

                <!-- قسم الخدمات -->
                <div style="background: rgba(255, 255, 255, 0.05); padding: 1.5rem; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.1);">
                    <label style="display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <i class="fas fa-concierge-bell" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                            <div>
                                <h4 style="color: var(--text-primary); margin: 0; font-size: 1.1rem; font-weight: 600;">قسم الخدمات</h4>
                                <p style="color: var(--text-secondary); margin: 0.25rem 0 0; font-size: 0.9rem;">إظهار/إخفاء قسم الخدمات</p>
                            </div>
                        </div>
                        <input type="checkbox" name="section_services_visible" value="1" 
                               {{ ($settings['section_services_visible'] ?? '1') == '1' ? 'checked' : '' }}
                               style="width: 24px; height: 24px; cursor: pointer;">
                    </label>
                </div>

                <!-- قسم المركز الإعلامي -->
                <div style="background: rgba(255, 255, 255, 0.05); padding: 1.5rem; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.1);">
                    <label style="display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <i class="fas fa-photo-video" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                            <div>
                                <h4 style="color: var(--text-primary); margin: 0; font-size: 1.1rem; font-weight: 600;">المركز الإعلامي</h4>
                                <p style="color: var(--text-secondary); margin: 0.25rem 0 0; font-size: 0.9rem;">إظهار/إخفاء قسم المركز الإعلامي</p>
                            </div>
                        </div>
                        <input type="checkbox" name="section_media_visible" value="1" 
                               {{ ($settings['section_media_visible'] ?? '1') == '1' ? 'checked' : '' }}
                               style="width: 24px; height: 24px; cursor: pointer;">
                    </label>
                </div>

                <!-- قسم المشاريع -->
                <div style="background: rgba(255, 255, 255, 0.05); padding: 1.5rem; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.1);">
                    <label style="display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <i class="fas fa-project-diagram" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                            <div>
                                <h4 style="color: var(--text-primary); margin: 0; font-size: 1.1rem; font-weight: 600;">قسم المشاريع</h4>
                                <p style="color: var(--text-secondary); margin: 0.25rem 0 0; font-size: 0.9rem;">إظهار/إخفاء قسم المشاريع</p>
                            </div>
                        </div>
                        <input type="checkbox" name="section_projects_visible" value="1" 
                               {{ ($settings['section_projects_visible'] ?? '1') == '1' ? 'checked' : '' }}
                               style="width: 24px; height: 24px; cursor: pointer;">
                    </label>
                </div>

                <!-- قسم ماذا قالوا عنا -->
                <div style="background: rgba(255, 255, 255, 0.05); padding: 1.5rem; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.1);">
                    <label style="display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <i class="fas fa-quote-left" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                            <div>
                                <h4 style="color: var(--text-primary); margin: 0; font-size: 1.1rem; font-weight: 600;">ماذا قالوا عنا</h4>
                                <p style="color: var(--text-secondary); margin: 0.25rem 0 0; font-size: 0.9rem;">إظهار/إخفاء قسم الشهادات</p>
                            </div>
                        </div>
                        <input type="checkbox" name="section_testimonials_visible" value="1" 
                               {{ ($settings['section_testimonials_visible'] ?? '1') == '1' ? 'checked' : '' }}
                               style="width: 24px; height: 24px; cursor: pointer;">
                    </label>
                </div>

                <!-- قسم الشركاء -->
                <div style="background: rgba(255, 255, 255, 0.05); padding: 1.5rem; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.1);">
                    <label style="display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <i class="fas fa-handshake" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                            <div>
                                <h4 style="color: var(--text-primary); margin: 0; font-size: 1.1rem; font-weight: 600;">قسم الشركاء</h4>
                                <p style="color: var(--text-secondary); margin: 0.25rem 0 0; font-size: 0.9rem;">إظهار/إخفاء قسم الشركاء</p>
                            </div>
                        </div>
                        <input type="checkbox" name="section_partners_visible" value="1" 
                               {{ ($settings['section_partners_visible'] ?? '1') == '1' ? 'checked' : '' }}
                               style="width: 24px; height: 24px; cursor: pointer;">
                    </label>
                </div>

                <!-- قسم الأخبار -->
                <div style="background: rgba(255, 255, 255, 0.05); padding: 1.5rem; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.1);">
                    <label style="display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <i class="fas fa-newspaper" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                            <div>
                                <h4 style="color: var(--text-primary); margin: 0; font-size: 1.1rem; font-weight: 600;">قسم الأخبار</h4>
                                <p style="color: var(--text-secondary); margin: 0.25rem 0 0; font-size: 0.9rem;">إظهار/إخفاء قسم الأخبار</p>
                            </div>
                        </div>
                        <input type="checkbox" name="section_news_visible" value="1" 
                               {{ ($settings['section_news_visible'] ?? '1') == '1' ? 'checked' : '' }}
                               style="width: 24px; height: 24px; cursor: pointer;">
                    </label>
                </div>

                <!-- أقسام البانر -->
                <div style="background: rgba(255, 255, 255, 0.05); padding: 1.5rem; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.1);">
                    <label style="display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <i class="fas fa-image" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                            <div>
                                <h4 style="color: var(--text-primary); margin: 0; font-size: 1.1rem; font-weight: 600;">أقسام البانر</h4>
                                <p style="color: var(--text-secondary); margin: 0.25rem 0 0; font-size: 0.9rem;">إظهار/إخفاء أقسام البانر</p>
                            </div>
                        </div>
                        <input type="checkbox" name="section_banner_sections_visible" value="1" 
                               {{ ($settings['section_banner_sections_visible'] ?? '1') == '1' ? 'checked' : '' }}
                               style="width: 24px; height: 24px; cursor: pointer;">
                    </label>
                </div>

                <!-- الموظفين -->
                <div style="background: rgba(255, 255, 255, 0.05); padding: 1.5rem; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.1);">
                    <label style="display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <i class="fas fa-user-tie" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                            <div>
                                <h4 style="color: var(--text-primary); margin: 0; font-size: 1.1rem; font-weight: 600;">الموظفين</h4>
                                <p style="color: var(--text-secondary); margin: 0.25rem 0 0; font-size: 0.9rem;">إظهار/إخفاء قسم الموظفين</p>
                            </div>
                        </div>
                        <input type="checkbox" name="section_staff_visible" value="1" 
                               {{ ($settings['section_staff_visible'] ?? '0') == '1' ? 'checked' : '' }}
                               style="width: 24px; height: 24px; cursor: pointer;">
                    </label>
                </div>
                
                <!-- قسم التقارير -->
                <div style="background: rgba(255, 255, 255, 0.05); padding: 1.5rem; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.1);">
                    <label style="display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <i class="fas fa-file-pdf" style="color: var(--primary-color); font-size: 1.5rem;"></i>
                            <div>
                                <div style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem;">التقارير</div>
                                <div style="font-size: 0.85rem; color: var(--text-secondary);">إظهار/إخفاء قسم التقارير</div>
                            </div>
                        </div>
                        <input type="checkbox" name="section_reports_visible" value="1" 
                               {{ ($settings['section_reports_visible'] ?? '0') == '1' ? 'checked' : '' }}
                               style="width: 24px; height: 24px; cursor: pointer;">
                    </label>
                </div>
            </div>
        </div>

        <!-- قسم عناوين الأقسام -->
        <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-top: 2rem; padding: 2rem;">
            <h2 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-heading" style="color: var(--primary-color);"></i>
                عناوين الأقسام
            </h2>
            <p style="color: var(--text-secondary); margin-bottom: 2rem; font-size: 0.95rem;">
                قم بتعديل عناوين الأقسام التي تظهر في الموقع
            </p>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-info-circle"></i> عنوان قسم "من نحن"
                    </label>
                    <input type="text" name="section_about_title" class="form-control" 
                           value="{{ old('section_about_title', $settings['section_about_title'] ?? 'من نحن') }}" 
                           placeholder="من نحن">
                    <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                        يمكنك تعديله أيضاً من صفحة "من نحن"
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-eye"></i> عنوان قسم "رؤيتنا ورسالتنا"
                    </label>
                    <input type="text" name="section_vision_mission_title" class="form-control" 
                           value="{{ old('section_vision_mission_title', $settings['section_vision_mission_title'] ?? 'رؤيتنا ورسالتنا') }}" 
                           placeholder="رؤيتنا ورسالتنا">
                    <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                        يمكنك تعديله أيضاً من صفحة "الرؤية والرسالة"
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-concierge-bell"></i> عنوان قسم "خدماتنا"
                    </label>
                    <input type="text" name="section_services_title" class="form-control" 
                           value="{{ old('section_services_title', $settings['section_services_title'] ?? 'خدماتنا المميزة') }}" 
                           placeholder="خدماتنا المميزة">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-project-diagram"></i> عنوان قسم "مشاريعنا"
                    </label>
                    <input type="text" name="section_projects_title" class="form-control" 
                           value="{{ old('section_projects_title', $settings['section_projects_title'] ?? 'مشاريعنا المميزة') }}" 
                           placeholder="مشاريعنا المميزة">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-photo-video"></i> عنوان قسم "المركز الإعلامي"
                    </label>
                    <input type="text" name="section_media_title" class="form-control" 
                           value="{{ old('section_media_title', $settings['section_media_title'] ?? 'محتوى إعلامي مميز') }}" 
                           placeholder="محتوى إعلامي مميز">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-quote-left"></i> عنوان قسم "ماذا قالوا عنا"
                    </label>
                    <input type="text" name="section_testimonials_title" class="form-control" 
                           value="{{ old('section_testimonials_title', $settings['section_testimonials_title'] ?? 'آراء عملائنا') }}" 
                           placeholder="آراء عملائنا">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-handshake"></i> عنوان قسم "شركاؤنا"
                    </label>
                    <input type="text" name="section_partners_title" class="form-control" 
                           value="{{ old('section_partners_title', $settings['section_partners_title'] ?? 'شركاؤنا الاستراتيجيون') }}" 
                           placeholder="شركاؤنا الاستراتيجيون">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-newspaper"></i> عنوان قسم "الأخبار"
                    </label>
                    <input type="text" name="section_news_title" class="form-control" 
                           value="{{ old('section_news_title', $settings['section_news_title'] ?? 'آخر الأخبار') }}" 
                           placeholder="آخر الأخبار">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-users"></i> عنوان صفحة "مجلس الإدارة"
                    </label>
                    <input type="text" name="page_board_members_title" class="form-control" 
                           value="{{ old('page_board_members_title', $settings['page_board_members_title'] ?? 'أعضاء مجلس الإدارة') }}" 
                           placeholder="أعضاء مجلس الإدارة">
                    <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                        العنوان الرئيسي الذي يظهر في صفحة مجلس الإدارة
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user-tie"></i> عنوان صفحة "الموظفين"
                    </label>
                    <input type="text" name="page_staff_title" class="form-control" 
                           value="{{ old('page_staff_title', $settings['page_staff_title'] ?? 'فريق العمل') }}" 
                           placeholder="فريق العمل">
                    <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                        العنوان الرئيسي الذي يظهر في صفحة الموظفين
                    </small>
                </div>
            </div>
        </div>

        <!-- قسم أيقونات الأقسام -->
        <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-top: 2rem; padding: 2rem;">
            <h2 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-icons" style="color: var(--primary-color);"></i>
                أيقونات الأقسام
            </h2>
            <p style="color: var(--text-secondary); margin-bottom: 2rem; font-size: 0.95rem;">
                قم بتعديل أيقونات الأقسام التي تظهر بجانب العناوين في الموقع (استخدم Font Awesome icons مثل: fas fa-users)
            </p>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-users"></i> أيقونة قسم "من نحن"
                    </label>
                    <input type="text" name="section_about_icon" class="form-control" 
                           value="{{ old('section_about_icon', $settings['section_about_icon'] ?? 'fas fa-users') }}" 
                           placeholder="fas fa-users">
                    <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                        مثال: fas fa-users, fas fa-info-circle
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-bullseye"></i> عنوان قسم "أهدافنا"
                    </label>
                    <input type="text" name="section_about_features_title" class="form-control" 
                           value="{{ old('section_about_features_title', $settings['section_about_features_title'] ?? 'أهدافنا') }}" 
                           placeholder="أهدافنا">
                    <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                        العنوان الذي سيظهر أعلى قسم الأهداف في صفحة "من نحن"
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-icons"></i> أيقونة قسم "أهدافنا"
                    </label>
                    <input type="text" name="section_about_features_icon" class="form-control" 
                           value="{{ old('section_about_features_icon', $settings['section_about_features_icon'] ?? 'fas fa-bullseye') }}" 
                           placeholder="fas fa-bullseye">
                    <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                        مثال: fas fa-bullseye, fas fa-target
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-align-right"></i> وصف قسم "أهدافنا"
                    </label>
                    <textarea name="section_about_features_description" class="form-control" rows="2"
                              placeholder="وصف قصير لقسم الأهداف">{{ old('section_about_features_description', $settings['section_about_features_description'] ?? '') }}</textarea>
                    <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                        وصف اختياري يظهر أسفل عنوان القسم
                    </small>
                </div>

                <!-- المدير التنفيذي -->
                <div class="form-group" style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid rgba(255, 255, 255, 0.1);">
                    <h3 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                        <i class="fas fa-user-tie" style="color: var(--primary-color);"></i>
                        المدير التنفيذي
                    </h3>
                    <p style="color: var(--text-secondary); margin-bottom: 1.5rem; font-size: 0.95rem;">
                        سيظهر اسم المدير التنفيذي وصورته في نهاية نص قسم "من نحن"
                    </p>

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
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-eye"></i> أيقونة قسم "رؤيتنا ورسالتنا"
                    </label>
                    <input type="text" name="section_vision_mission_icon" class="form-control" 
                           value="{{ old('section_vision_mission_icon', $settings['section_vision_mission_icon'] ?? 'fas fa-eye') }}" 
                           placeholder="fas fa-eye">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-concierge-bell"></i> أيقونة قسم "خدماتنا"
                    </label>
                    <input type="text" name="section_services_icon" class="form-control" 
                           value="{{ old('section_services_icon', $settings['section_services_icon'] ?? 'fas fa-concierge-bell') }}" 
                           placeholder="fas fa-concierge-bell">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-project-diagram"></i> أيقونة قسم "مشاريعنا"
                    </label>
                    <input type="text" name="section_projects_icon" class="form-control" 
                           value="{{ old('section_projects_icon', $settings['section_projects_icon'] ?? 'fas fa-project-diagram') }}" 
                           placeholder="fas fa-project-diagram">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-video"></i> أيقونة قسم "المركز الإعلامي"
                    </label>
                    <input type="text" name="section_media_icon" class="form-control" 
                           value="{{ old('section_media_icon', $settings['section_media_icon'] ?? 'fas fa-video') }}" 
                           placeholder="fas fa-video">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-quote-right"></i> أيقونة قسم "ماذا قالوا عنا"
                    </label>
                    <input type="text" name="section_testimonials_icon" class="form-control" 
                           value="{{ old('section_testimonials_icon', $settings['section_testimonials_icon'] ?? 'fas fa-quote-right') }}" 
                           placeholder="fas fa-quote-right">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-handshake"></i> أيقونة قسم "شركاؤنا"
                    </label>
                    <input type="text" name="section_partners_icon" class="form-control" 
                           value="{{ old('section_partners_icon', $settings['section_partners_icon'] ?? 'fas fa-handshake') }}" 
                           placeholder="fas fa-handshake">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-newspaper"></i> أيقونة قسم "الأخبار"
                    </label>
                    <input type="text" name="section_news_icon" class="form-control" 
                           value="{{ old('section_news_icon', $settings['section_news_icon'] ?? 'fas fa-newspaper') }}" 
                           placeholder="fas fa-newspaper">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-images"></i> أيقونة قسم "أقسام البانر"
                    </label>
                    <input type="text" name="section_banner_sections_icon" class="form-control" 
                           value="{{ old('section_banner_sections_icon', $settings['section_banner_sections_icon'] ?? 'fas fa-images') }}" 
                           placeholder="fas fa-images">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user-tie"></i> أيقونة قسم "الموظفين"
                    </label>
                    <input type="text" name="section_staff_icon" class="form-control" 
                           value="{{ old('section_staff_icon', $settings['section_staff_icon'] ?? 'fas fa-user-tie') }}" 
                           placeholder="fas fa-user-tie">
                </div>
            </div>
        </div>

        <!-- قسم خلفيات الأقسام -->
        <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-top: 2rem; padding: 2rem;">
            <h2 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-images" style="color: var(--primary-color);"></i>
                خلفيات الأقسام
            </h2>
            <p style="color: var(--text-secondary); margin-bottom: 2rem; font-size: 0.95rem;">
                قم برفع صورة خلفية لكل قسم وضبط شفافيتها. يمكنك إعادة الإعدادات الافتراضية في أي وقت.
            </p>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 2rem;">
                @php
                    $sections = [
                        'about' => ['name' => 'من نحن', 'icon' => 'fas fa-info-circle'],
                        'vision_mission' => ['name' => 'رؤيتنا ورسالتنا', 'icon' => 'fas fa-eye'],
                        'services' => ['name' => 'خدماتنا', 'icon' => 'fas fa-concierge-bell'],
                        'projects' => ['name' => 'مشاريعنا', 'icon' => 'fas fa-project-diagram'],
                        'media' => ['name' => 'المركز الإعلامي', 'icon' => 'fas fa-video'],
                        'testimonials' => ['name' => 'ماذا قالوا عنا', 'icon' => 'fas fa-quote-right'],
                        'partners' => ['name' => 'شركاؤنا', 'icon' => 'fas fa-handshake'],
                        'news' => ['name' => 'الأخبار', 'icon' => 'fas fa-newspaper'],
                        'banner_sections' => ['name' => 'أقسام البانر', 'icon' => 'fas fa-images'],
                        'staff' => ['name' => 'الموظفين', 'icon' => 'fas fa-user-tie'],
                    ];
                @endphp

                @foreach($sections as $key => $section)
                <div class="content-card" style="background: rgba(255, 255, 255, 0.05); padding: 1.5rem; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.1);">
                    <h3 style="color: var(--text-primary); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.75rem;">
                        <i class="{{ $section['icon'] }}" style="color: var(--primary-color);"></i>
                        {{ $section['name'] }}
                    </h3>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-image"></i> صورة الخلفية
                        </label>
                        @if(!empty($settings['section_' . $key . '_bg_image']))
                            <div style="margin-bottom: 1rem;">
                                <img src="{{ image_asset_url($settings['section_' . $key . '_bg_image']) }}" alt="صورة الخلفية" 
                                     style="max-width: 100%; max-height: 200px; border-radius: 12px; border: 2px solid rgba(255, 255, 255, 0.1); object-fit: cover; background: rgba(255, 255, 255, 0.05); padding: 0.5rem;">
                            </div>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-bottom: 1rem;">
                                <input type="checkbox" name="section_{{ $key }}_bg_image_remove" value="1">
                                <span style="color: rgba(255, 255, 255, 0.7);">حذف الصورة الحالية</span>
                            </label>
                        @endif
                        <input type="file" name="section_{{ $key }}_bg_image" class="form-control" accept="image/*">
                        <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                            حجم أقصى 10MB
                        </small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-adjust"></i> شفافية الصورة (0 - 100)
                        </label>
                        <div style="display: flex; gap: 1rem; align-items: center;">
                            <input type="range" 
                                   name="section_{{ $key }}_bg_opacity" 
                                   id="section_{{ $key }}_bg_opacity"
                                   min="0" 
                                   max="100" 
                                   value="{{ old('section_' . $key . '_bg_opacity', $settings['section_' . $key . '_bg_opacity'] ?? '30') }}"
                                   class="form-control"
                                   style="flex: 1;"
                                   oninput="document.getElementById('section_{{ $key }}_opacity_value').textContent = this.value + '%'">
                            <span id="section_{{ $key }}_opacity_value" style="min-width: 50px; text-align: center; color: var(--primary-color); font-weight: 600;">
                                {{ old('section_' . $key . '_bg_opacity', $settings['section_' . $key . '_bg_opacity'] ?? '30') }}%
                            </span>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgba(255, 255, 255, 0.1);">
                        <button type="button" 
                                onclick="resetSectionBackground('{{ $key }}')" 
                                class="btn btn-secondary"
                                style="background: rgba(239, 68, 68, 0.2); border-color: rgba(239, 68, 68, 0.3); color: #ef4444; width: 100%;">
                            <i class="fas fa-undo"></i>
                            إعادة الإعدادات الافتراضية
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            </div>
        </div>

        <button type="submit" name="save_settings" class="btn btn-primary" style="margin-top: 2rem;">
            <i class="fas fa-save"></i> حفظ الإعدادات
        </button>
        
        <!-- زر مخفي لإعادة الإعدادات (يتم استخدامه برمجياً) -->
        <button type="submit" name="save_settings" id="hidden-submit-btn" style="display: none;"></button>
    </form>

    <script>
    // معاينة ساعات العمل مباشرة
    document.addEventListener('DOMContentLoaded', function() {
        const dayCheckboxes = document.querySelectorAll('input[name="working_days[]"]');
        const hoursFromInput = document.querySelector('input[name="working_hours_from"]');
        const hoursToInput = document.querySelector('input[name="working_hours_to"]');
        
        function updatePreview() {
            const selectedDays = Array.from(dayCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);
            
            const from = hoursFromInput.value;
            const to = hoursToInput.value;
            
            if (selectedDays.length > 0 && from && to) {
                const daysMap = {
                    'sunday': 'الأحد',
                    'monday': 'الاثنين',
                    'tuesday': 'الثلاثاء',
                    'wednesday': 'الأربعاء',
                    'thursday': 'الخميس',
                    'friday': 'الجمعة',
                    'saturday': 'السبت'
                };
                
                const dayOrder = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                const sortedDays = selectedDays.filter(d => dayOrder.includes(d)).sort((a, b) => dayOrder.indexOf(a) - dayOrder.indexOf(b));
                
                let dayRanges = [];
                if (sortedDays.length > 0) {
                    let start = sortedDays[0];
                    let end = sortedDays[0];
                    
                    for (let i = 1; i < sortedDays.length; i++) {
                        const currentIndex = dayOrder.indexOf(sortedDays[i]);
                        const prevIndex = dayOrder.indexOf(sortedDays[i-1]);
                        
                        if (currentIndex == prevIndex + 1) {
                            end = sortedDays[i];
                        } else {
                            if (start == end) {
                                dayRanges.push(daysMap[start]);
                            } else {
                                dayRanges.push(daysMap[start] + ' - ' + daysMap[end]);
                            }
                            start = sortedDays[i];
                            end = sortedDays[i];
                        }
                    }
                    
                    if (start == end) {
                        dayRanges.push(daysMap[start]);
                    } else {
                        dayRanges.push(daysMap[start] + ' - ' + daysMap[end]);
                    }
                }
                
                const daysDisplay = dayRanges.join('، ');
                
                // Convert to 12-hour format
                const fromDate = new Date('2000-01-01T' + from);
                const toDate = new Date('2000-01-01T' + to);
                
                const fromHours = fromDate.getHours();
                const fromMinutes = fromDate.getMinutes();
                const fromPeriod = fromHours >= 12 ? 'م' : 'ص';
                const fromHour12 = fromHours % 12 || 12;
                const fromTime = fromHour12 + ':' + (fromMinutes < 10 ? '0' : '') + fromMinutes;
                
                const toHours = toDate.getHours();
                const toMinutes = toDate.getMinutes();
                const toPeriod = toHours >= 12 ? 'م' : 'ص';
                const toHour12 = toHours % 12 || 12;
                const toTime = toHour12 + ':' + (toMinutes < 10 ? '0' : '') + toMinutes;
                
                const timeDisplay = fromTime + ' ' + fromPeriod + ' - ' + toTime + ' ' + toPeriod;
                
                // Update or create preview
                let previewDiv = document.getElementById('working-hours-preview');
                if (!previewDiv) {
                    previewDiv = document.createElement('div');
                    previewDiv.id = 'working-hours-preview';
                    previewDiv.style.cssText = 'margin-top: 1rem; padding: 1rem; background: rgba(95, 179, 142, 0.1); border-radius: 8px; border: 1px solid rgba(95, 179, 142, 0.3);';
                    hoursToInput.closest('.form-group').appendChild(previewDiv);
                }
                
                previewDiv.innerHTML = '<strong style="color: rgba(255, 255, 255, 0.9); display: block; margin-bottom: 0.5rem;">معاينة:</strong>' +
                    '<div style="color: rgba(255, 255, 255, 0.9);">' +
                    '<span style="font-weight: 600;">' + daysDisplay + '</span>' +
                    '<span style="color: var(--primary-color); margin-right: 0.5rem; margin-left: 0.5rem;">|</span>' +
                    '<span style="color: var(--primary-color); font-weight: 600;">' + timeDisplay + '</span>' +
                    '</div>';
            } else {
                const previewDiv = document.getElementById('working-hours-preview');
                if (previewDiv) {
                    previewDiv.remove();
                }
            }
        }
        
        dayCheckboxes.forEach(cb => cb.addEventListener('change', updatePreview));
        if (hoursFromInput) hoursFromInput.addEventListener('change', updatePreview);
        if (hoursToInput) hoursToInput.addEventListener('change', updatePreview);
        
        // Initial preview
        updatePreview();
    });
    </script>
</div>

<script>
    // دالة لتحويل rgba إلى hex
    function rgbaToHex(rgba) {
        if (!rgba || rgba.trim() === '') return null;
        
        // إذا كان hex بالفعل
        if (rgba.startsWith('#')) {
            return rgba;
        }
        
        // تحويل rgba إلى hex
        const match = rgba.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*([\d.]+))?\)/);
        if (match) {
            const r = parseInt(match[1]).toString(16).padStart(2, '0');
            const g = parseInt(match[2]).toString(16).padStart(2, '0');
            const b = parseInt(match[3]).toString(16).padStart(2, '0');
            return '#' + r + g + b;
        }
        
        return null;
    }
    
    // تحديث color picker عند تغيير النص
    function updateColorPicker(pickerId, textValue) {
        const picker = document.getElementById(pickerId);
        if (!picker) return;
        
        const hex = rgbaToHex(textValue);
        if (hex) {
            picker.value = hex;
        }
    }
    
    // تحديث حقل النص عند تغيير color picker
    function updateColorFromPicker(textFieldId, hexValue) {
        const textField = document.getElementById(textFieldId);
        if (!textField) return;
        
        // إذا كان الحقل يحتوي على rgba، نحتفظ بالشفافية
        const currentValue = textField.value;
        if (currentValue && currentValue.includes('rgba')) {
            const match = currentValue.match(/rgba?\([\d\s,]+,\s*([\d.]+)\)/);
            const alpha = match ? match[1] : '1';
            
            // تحويل hex إلى rgb
            const r = parseInt(hexValue.slice(1, 3), 16);
            const g = parseInt(hexValue.slice(3, 5), 16);
            const b = parseInt(hexValue.slice(5, 7), 16);
            
            textField.value = `rgba(${r}, ${g}, ${b}, ${alpha})`;
        } else {
            // إذا كان hex أو نص عادي، نستبدله بـ hex
            textField.value = hexValue;
        }
    }

// مزامنة حقول الألوان
document.addEventListener('DOMContentLoaded', function() {
    // تهيئة color pickers للحقول التي تدعم rgba
    const rgbaFields = ['dashboard_sidebar_bg', 'dashboard_content_bg', 'dashboard_border_color'];
    rgbaFields.forEach(fieldId => {
        const textField = document.getElementById(fieldId);
        const pickerId = fieldId + '_color';
        const picker = document.getElementById(pickerId);
        
        if (textField && picker) {
            const hex = rgbaToHex(textField.value);
            if (hex) {
                picker.value = hex;
            }
        }
    });
    
    const colorInputs = document.querySelectorAll('input[type="color"]');
    colorInputs.forEach(colorInput => {
        const textInput = colorInput.nextElementSibling;
        if (textInput && textInput.type === 'text') {
            // تحديث النص عند تغيير اللون
            colorInput.addEventListener('input', function() {
                textInput.value = this.value;
            });
            // تحديث اللون عند تغيير النص
            textInput.addEventListener('input', function() {
                if (/^#[0-9A-Fa-f]{6}$/i.test(this.value)) {
                    colorInput.value = this.value;
                }
            });
        }
    });
});

// دالة معالجة رفع ملف الهوية
function handleBrandFileUpload(input) {
    const file = input.files[0];
    if (!file) return;
    
    // التحقق من نوع الملف
    const isImage = file.type.match('image.*');
    const isPDF = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
    
    if (!isImage && !isPDF) {
        alert('يرجى رفع ملف صورة (JPEG, PNG, GIF) أو ملف PDF فقط');
        input.value = '';
        return;
    }
    
    const preview = document.getElementById('brand_file_preview');
    const previewImg = document.getElementById('brand_file_preview_img');
    const extractBtn = document.getElementById('extract-colors-btn');
    
    if (isImage) {
        // عرض معاينة الصورة
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
            extractBtn.style.display = 'inline-block';
        };
        reader.readAsDataURL(file);
    } else if (isPDF) {
        // عرض أيقونة PDF
        previewImg.src = 'data:image/svg+xml;base64,' + btoa('<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>');
        preview.style.display = 'block';
        extractBtn.style.display = 'inline-block';
        previewImg.style.width = '100px';
        previewImg.style.height = '100px';
        previewImg.style.objectFit = 'contain';
    }
}

// دالة استخراج الألوان من الصورة
function extractColorsFromImage() {
    const fileInput = document.getElementById('brand_file_upload');
    const file = fileInput.files[0];
    
    if (!file) {
        alert('يرجى رفع ملف الهوية أولاً');
        return;
    }
    
    const formData = new FormData();
    formData.append('brand_file', file);
    formData.append('_token', document.querySelector('input[name="_token"]').value);
    
    // إظهار حالة التحميل
    const extractBtn = document.getElementById('extract-colors-btn');
    const originalText = extractBtn.innerHTML;
    extractBtn.disabled = true;
    extractBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري استخراج الألوان...';
    
    // إرسال طلب AJAX
    fetch('{{ settings_route("extract-colors") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.colors) {
            // تحديث حقول الألوان بالألوان المستخرجة
            if (data.colors.primary) {
                document.getElementById('brand_primary_color').value = data.colors.primary;
                document.getElementById('brand_primary_color_picker').value = data.colors.primary;
            }
            if (data.colors.secondary) {
                document.getElementById('brand_secondary_color').value = data.colors.secondary;
                document.getElementById('brand_secondary_color_picker').value = data.colors.secondary;
            }
            if (data.colors.tertiary) {
                document.getElementById('brand_tertiary_color').value = data.colors.tertiary;
                document.getElementById('brand_tertiary_color_picker').value = data.colors.tertiary;
            }
            if (data.colors.accent) {
                document.getElementById('brand_accent_color').value = data.colors.accent;
                document.getElementById('brand_accent_color_picker').value = data.colors.accent;
            }
            
            alert('تم استخراج الألوان بنجاح! يمكنك الآن استخدام زر "تنسيق تلقائي" لتوليد الخيارات.');
        } else {
            alert('حدث خطأ: ' + (data.message || 'فشل استخراج الألوان'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء استخراج الألوان');
    })
    .finally(() => {
        extractBtn.disabled = false;
        extractBtn.innerHTML = originalText;
    });
}

// دالة التنسيق التلقائي للألوان بالذكاء الصناعي
function applyAutoColorScheme() {
    const primaryColor = document.getElementById('brand_primary_color').value || '#5FB38E';
    const secondaryColor = document.getElementById('brand_secondary_color').value || '';
    const tertiaryColor = document.getElementById('brand_tertiary_color').value || '';
    const accentColor = document.getElementById('brand_accent_color').value || '';
    
    if (!primaryColor || !/^#[0-9A-Fa-f]{6}$/i.test(primaryColor)) {
        alert('يرجى إدخال لون أساسي صحيح للهوية');
        return;
    }
    
    // إظهار حالة التحميل
    const btn = document.getElementById('auto-color-btn');
    const btnText = document.getElementById('auto-color-btn-text');
    const btnLoading = document.getElementById('auto-color-btn-loading');
    
    btn.disabled = true;
    btnText.style.display = 'none';
    btnLoading.style.display = 'inline';
    
    // إرسال طلب AJAX
    fetch('{{ settings_route("auto-color-scheme") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: JSON.stringify({
            brand_primary_color: primaryColor,
            brand_secondary_color: secondaryColor,
            brand_tertiary_color: tertiaryColor,
            brand_accent_color: accentColor
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.colorOptions && data.colorOptions.length > 0) {
            // عرض خيارات الألوان
            displayColorOptions(data.colorOptions);
        } else if (data.success && data.colors) {
            // دعم الإصدار القديم (خيار واحد)
            applySingleColorScheme(data.colors);
        } else {
            alert('حدث خطأ: ' + (data.message || 'فشل توليد لوحة الألوان'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء الاتصال بالخادم');
    })
    .finally(() => {
        // إخفاء حالة التحميل
        btn.disabled = false;
        btnText.style.display = 'inline';
        btnLoading.style.display = 'none';
    });
}

// دالة عرض خيارات الألوان
function displayColorOptions(colorOptions) {
    const container = document.getElementById('color-options-container');
    const list = document.getElementById('color-options-list');
    
    if (!container || !list) return;
    
    // إظهار الحاوية
    container.style.display = 'block';
    
    // مسح المحتوى السابق
    list.innerHTML = '';
    
    // حفظ الخيارات مؤقتاً في window للوصول إليها لاحقاً
    window.currentColorOptions = colorOptions;
    
    // إنشاء عنصر لكل خيار بشكل بسيط
    colorOptions.forEach((colors, index) => {
        const optionItem = document.createElement('div');
        optionItem.className = 'color-option-item';
        optionItem.style.cssText = `
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            transition: all 0.3s ease;
        `;
        
        optionItem.onmouseenter = function() {
            this.style.borderColor = colors.site_primary_color;
            this.style.background = `rgba(${hexToRgb(colors.site_primary_color)}, 0.1)`;
        };
        
        optionItem.onmouseleave = function() {
            this.style.borderColor = 'rgba(255, 255, 255, 0.1)';
            this.style.background = 'rgba(255, 255, 255, 0.05)';
        };
        
        // معلومات الخيار
        const optionInfo = document.createElement('div');
        optionInfo.style.cssText = `
            display: flex;
            align-items: center;
            gap: 1rem;
            flex: 1;
        `;
        
        // معاينة الألوان (مربعات صغيرة)
        const colorPreview = document.createElement('div');
        colorPreview.style.cssText = `
            display: flex;
            gap: 0.5rem;
        `;
        
        const colorBox1 = document.createElement('div');
        colorBox1.style.cssText = `
            width: 30px;
            height: 30px;
            background: ${colors.site_primary_color};
            border-radius: 6px;
            border: 2px solid rgba(255, 255, 255, 0.2);
        `;
        
        const colorBox2 = document.createElement('div');
        colorBox2.style.cssText = `
            width: 30px;
            height: 30px;
            background: ${colors.site_primary_dark};
            border-radius: 6px;
            border: 2px solid rgba(255, 255, 255, 0.2);
        `;
        
        const colorBox3 = document.createElement('div');
        colorBox3.style.cssText = `
            width: 30px;
            height: 30px;
            background: ${colors.site_accent_color};
            border-radius: 6px;
            border: 2px solid rgba(255, 255, 255, 0.2);
        `;
        
        colorPreview.appendChild(colorBox1);
        colorPreview.appendChild(colorBox2);
        colorPreview.appendChild(colorBox3);
        
        // نص الخيار
        const optionText = document.createElement('span');
        optionText.textContent = `الخيار ${index + 1}`;
        optionText.style.cssText = `
            color: var(--text-primary);
            font-size: 1rem;
            font-weight: 500;
        `;
        
        optionInfo.appendChild(colorPreview);
        optionInfo.appendChild(optionText);
        
        // زر الاستخدام
        const useButton = document.createElement('button');
        useButton.type = 'button';
        useButton.className = 'btn btn-primary';
        useButton.textContent = 'استخدام';
        useButton.style.cssText = `
            padding: 0.5rem 1.5rem;
            background: linear-gradient(135deg, ${colors.site_primary_color} 0%, ${colors.site_primary_dark} 100%);
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
        `;
        
        useButton.onclick = function() {
            applyColorScheme(colors);
        };
        
        useButton.onmouseenter = function() {
            this.style.transform = 'scale(1.05)';
            this.style.boxShadow = `0 5px 15px rgba(${hexToRgb(colors.site_primary_color)}, 0.4)`;
        };
        
        useButton.onmouseleave = function() {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = 'none';
        };
        
        // تجميع العناصر
        optionItem.appendChild(optionInfo);
        optionItem.appendChild(useButton);
        
        list.appendChild(optionItem);
    });
    
    // التمرير إلى الخيارات
    container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// دالة إنشاء صندوق لون
function createColorBox(color, label) {
    const box = document.createElement('div');
    box.style.cssText = `
        flex: 1;
        min-width: 60px;
        height: 60px;
        background: ${color};
        border-radius: 10px;
        border: 2px solid rgba(255, 255, 255, 0.2);
        position: relative;
        cursor: pointer;
        transition: transform 0.3s ease;
    `;
    
    box.title = `${label}: ${color}`;
    
    box.onclick = function() {
        navigator.clipboard.writeText(color).then(() => {
            alert(`تم نسخ اللون: ${color}`);
        });
    };
    
    box.onmouseenter = function() {
        this.style.transform = 'scale(1.1)';
    };
    
    box.onmouseleave = function() {
        this.style.transform = 'scale(1)';
    };
    
    return box;
}

// دالة تطبيق خيار الألوان
function applyColorScheme(colors, autoSave = false) {
    if (!autoSave && !confirm('هل تريد تطبيق هذا الخيار من الألوان؟ سيتم تحديث جميع حقول الألوان.')) {
        return;
    }
    
    // تحديث جميع حقول الألوان
    if (colors.site_primary_color) {
        document.getElementById('site_primary_color').value = colors.site_primary_color;
        document.getElementById('site_primary_color_picker').value = colors.site_primary_color;
    }
    if (colors.site_primary_dark) {
        document.getElementById('site_primary_dark').value = colors.site_primary_dark;
        document.getElementById('site_primary_dark_picker').value = colors.site_primary_dark;
    }
    if (colors.site_secondary_color) {
        document.getElementById('site_secondary_color').value = colors.site_secondary_color;
        document.getElementById('site_secondary_color_picker').value = colors.site_secondary_color;
    }
    if (colors.site_accent_color) {
        document.getElementById('site_accent_color').value = colors.site_accent_color;
        document.getElementById('site_accent_color_picker').value = colors.site_accent_color;
    }
    if (colors.site_text_primary_color) {
        document.getElementById('site_text_primary_color').value = colors.site_text_primary_color;
        document.getElementById('site_text_primary_color_picker').value = colors.site_text_primary_color;
    }
    if (colors.site_text_secondary_color) {
        document.getElementById('site_text_secondary_color').value = colors.site_text_secondary_color;
        document.getElementById('site_text_secondary_color_picker').value = colors.site_text_secondary_color;
    }
    if (colors.site_icon_color) {
        document.getElementById('site_icon_color').value = colors.site_icon_color;
        document.getElementById('site_icon_color_picker').value = colors.site_icon_color;
    }
    if (colors.site_card_bg_color) {
        document.getElementById('site_card_bg_color').value = colors.site_card_bg_color;
        document.getElementById('site_card_bg_color_picker').value = colors.site_card_bg_color;
    }
    if (colors.site_card_border_color) {
        document.getElementById('site_card_border_color').value = colors.site_card_border_color;
        document.getElementById('site_card_border_color_picker').value = colors.site_card_border_color;
    }
    if (colors.site_card_title_color) {
        document.getElementById('site_card_title_color').value = colors.site_card_title_color;
        document.getElementById('site_card_title_color_picker').value = colors.site_card_title_color;
    }
    if (colors.site_hero_title_color) {
        document.getElementById('site_hero_title_color').value = colors.site_hero_title_color;
        document.getElementById('site_hero_title_color_picker').value = colors.site_hero_title_color;
    }
    if (colors.navbar_bg_color) {
        document.getElementById('navbar_bg_color').value = colors.navbar_bg_color;
        document.getElementById('navbar_bg_color_picker').value = colors.navbar_bg_color;
    }
    if (colors.navbar_text_color) {
        document.getElementById('navbar_text_color').value = colors.navbar_text_color;
        document.getElementById('navbar_text_color_picker').value = colors.navbar_text_color;
    }
    if (colors.navbar_border_color) {
        document.getElementById('navbar_border_color').value = colors.navbar_border_color;
        document.getElementById('navbar_border_color_picker').value = colors.navbar_border_color;
    }
    // Footer colors (if fields exist)
    if (colors.footer_bg_color && document.getElementById('footer_bg_color')) {
        document.getElementById('footer_bg_color').value = colors.footer_bg_color;
        if (document.getElementById('footer_bg_color_picker')) {
            document.getElementById('footer_bg_color_picker').value = colors.footer_bg_color;
        }
    }
    if (colors.footer_text_color && document.getElementById('footer_text_color')) {
        document.getElementById('footer_text_color').value = colors.footer_text_color;
        if (document.getElementById('footer_text_color_picker')) {
            document.getElementById('footer_text_color_picker').value = colors.footer_text_color;
        }
    }
    if (colors.footer_link_color && document.getElementById('footer_link_color')) {
        document.getElementById('footer_link_color').value = colors.footer_link_color;
        if (document.getElementById('footer_link_color_picker')) {
            document.getElementById('footer_link_color_picker').value = colors.footer_link_color;
        }
    }
    // Article colors (if fields exist)
    if (colors.article_title_color && document.getElementById('article_title_color')) {
        document.getElementById('article_title_color').value = colors.article_title_color;
        if (document.getElementById('article_title_color_picker')) {
            document.getElementById('article_title_color_picker').value = colors.article_title_color;
        }
    }
    if (colors.article_text_color && document.getElementById('article_text_color')) {
        document.getElementById('article_text_color').value = colors.article_text_color;
        if (document.getElementById('article_text_color_picker')) {
            document.getElementById('article_text_color_picker').value = colors.article_text_color;
        }
    }
    
    // إذا كان autoSave = true، احفظ تلقائياً
    if (autoSave) {
        // حفظ الألوان مباشرة
        saveColorSettings();
    } else {
        // عرض رسالة للمستخدم
        if (confirm('تم تطبيق خيار الألوان بنجاح! هل تريد حفظ الألوان الآن؟')) {
            saveColorSettings();
        } else {
            alert('تم تطبيق الألوان على الحقول. لا تنسَ حفظ الإعدادات قبل مغادرة الصفحة.');
        }
    }
    
    // لا نخفي الخيارات - نبقيهم مرئيين للاستخدام مرة أخرى
    // يمكن للمستخدم استخدام خيارات أخرى أو حفظها
}

// دالة حفظ إعدادات الألوان
function saveColorSettings() {
    const form = document.getElementById('settings-form');
    if (!form) {
        alert('حدث خطأ: لم يتم العثور على نموذج الإعدادات.');
        return;
    }
    
    // إنشاء FormData من النموذج
    const formData = new FormData(form);
    formData.append('save_settings', '1');
    
    // إظهار مؤشر التحميل
    const submitBtn = document.getElementById('hidden-submit-btn');
    if (submitBtn) {
        submitBtn.disabled = true;
    }
    
    // إرسال البيانات
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        // التحقق من نوع الاستجابة
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            // إذا كانت الاستجابة HTML (إعادة توجيه)، نعيد تحميل الصفحة
            return { success: true, redirect: true };
        }
    })
    .then(data => {
        if (data && (data.success || data.redirect)) {
            alert('تم حفظ الألوان بنجاح!');
            // إعادة تحميل الصفحة لتحديث القيم
            setTimeout(() => {
                window.location.reload();
            }, 500);
        } else if (data && data.message) {
            alert('حدث خطأ أثناء حفظ الألوان: ' + data.message);
        } else {
            // إذا لم تكن هناك استجابة JSON، تم إعادة التوجيه بالفعل
            alert('تم حفظ الألوان بنجاح!');
            setTimeout(() => {
                window.location.reload();
            }, 500);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // محاولة إرسال النموذج بالطريقة التقليدية إذا فشل fetch
        alert('جارٍ حفظ الإعدادات...');
        form.submit();
    })
    .finally(() => {
        if (submitBtn) {
            submitBtn.disabled = false;
        }
    });
}

// دالة حفظ الخيارات المولدة
function saveColorOptions() {
    if (!window.currentColorOptions || window.currentColorOptions.length === 0) {
        alert('لا توجد خيارات مولدة للحفظ');
        return;
    }
    
    // حفظ الخيارات في localStorage
    const savedOptions = JSON.parse(localStorage.getItem('savedColorOptions') || '[]');
    const timestamp = new Date().toLocaleString('ar-SA');
    
    savedOptions.push({
        id: Date.now(),
        timestamp: timestamp,
        options: window.currentColorOptions
    });
    
    localStorage.setItem('savedColorOptions', JSON.stringify(savedOptions));
    
    alert('تم حفظ الخيارات بنجاح! يمكنك الوصول إليها من قسم "الخيارات المحفوظة"');
    
    // تحديث عرض الخيارات المحفوظة
    displaySavedOptions();
    
    // إخفاء الخيارات المولدة بعد الحفظ لتجنب التكرار
    const container = document.getElementById('color-options-container');
    if (container) {
        container.style.display = 'none';
    }
}

// دالة عرض الخيارات المحفوظة
function displaySavedOptions() {
    const container = document.getElementById('saved-options-container');
    const list = document.getElementById('saved-options-list');
    
    if (!container || !list) return;
    
    const savedOptions = JSON.parse(localStorage.getItem('savedColorOptions') || '[]');
    
    if (savedOptions.length === 0) {
        container.style.display = 'none';
        return;
    }
    
    container.style.display = 'block';
    list.innerHTML = '';
    
    savedOptions.forEach((saved, savedIndex) => {
        const savedGroup = document.createElement('div');
        savedGroup.style.cssText = `
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.03);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
        `;
        
        const groupHeader = document.createElement('div');
        groupHeader.style.cssText = `
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        `;
        
        const groupTitle = document.createElement('h4');
        groupTitle.textContent = `مجموعة محفوظة ${savedIndex + 1} - ${saved.timestamp}`;
        groupTitle.style.cssText = `
            color: var(--text-primary);
            margin: 0;
            font-size: 1rem;
        `;
        
        const deleteButton = document.createElement('button');
        deleteButton.type = 'button';
        deleteButton.className = 'btn btn-danger';
        deleteButton.innerHTML = '<i class="fas fa-trash"></i>';
        deleteButton.style.cssText = `
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        `;
        
        deleteButton.onclick = function() {
            if (confirm('هل تريد حذف هذه المجموعة المحفوظة؟')) {
                const updated = savedOptions.filter((_, idx) => idx !== savedIndex);
                localStorage.setItem('savedColorOptions', JSON.stringify(updated));
                displaySavedOptions();
            }
        };
        
        groupHeader.appendChild(groupTitle);
        groupHeader.appendChild(deleteButton);
        
        const optionsList = document.createElement('div');
        optionsList.style.cssText = `
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        `;
        
        saved.options.forEach((colors, index) => {
            const optionItem = document.createElement('div');
            optionItem.style.cssText = `
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0.75rem 1rem;
                background: rgba(255, 255, 255, 0.05);
                border: 2px solid rgba(255, 255, 255, 0.1);
                border-radius: 8px;
                transition: all 0.3s ease;
            `;
            
            optionItem.onmouseenter = function() {
                this.style.borderColor = colors.site_primary_color;
                this.style.background = `rgba(${hexToRgb(colors.site_primary_color)}, 0.1)`;
            };
            
            optionItem.onmouseleave = function() {
                this.style.borderColor = 'rgba(255, 255, 255, 0.1)';
                this.style.background = 'rgba(255, 255, 255, 0.05)';
            };
            
            const optionInfo = document.createElement('div');
            optionInfo.style.cssText = `
                display: flex;
                align-items: center;
                gap: 1rem;
                flex: 1;
            `;
            
            const colorPreview = document.createElement('div');
            colorPreview.style.cssText = `
                display: flex;
                gap: 0.4rem;
            `;
            
            [colors.site_primary_color, colors.site_primary_dark, colors.site_accent_color].forEach(color => {
                const box = document.createElement('div');
                box.style.cssText = `
                    width: 25px;
                    height: 25px;
                    background: ${color};
                    border-radius: 5px;
                    border: 2px solid rgba(255, 255, 255, 0.2);
                `;
                colorPreview.appendChild(box);
            });
            
            const optionText = document.createElement('span');
            optionText.textContent = `الخيار ${index + 1}`;
            optionText.style.cssText = `
                color: var(--text-primary);
                font-size: 0.95rem;
            `;
            
            optionInfo.appendChild(colorPreview);
            optionInfo.appendChild(optionText);
            
            const useButton = document.createElement('button');
            useButton.type = 'button';
            useButton.className = 'btn btn-primary';
            useButton.textContent = 'استخدام';
            useButton.style.cssText = `
                padding: 0.5rem 1.5rem;
                background: linear-gradient(135deg, ${colors.site_primary_color} 0%, ${colors.site_primary_dark} 100%);
                border: none;
                border-radius: 8px;
                color: white;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                white-space: nowrap;
            `;
            
            useButton.onclick = function() {
                applyColorScheme(colors);
            };
            
            optionItem.appendChild(optionInfo);
            optionItem.appendChild(useButton);
            optionsList.appendChild(optionItem);
        });
        
        savedGroup.appendChild(groupHeader);
        savedGroup.appendChild(optionsList);
        list.appendChild(savedGroup);
    });
}

// دالة تحويل hex إلى rgb
function hexToRgb(hex) {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? 
        `${parseInt(result[1], 16)}, ${parseInt(result[2], 16)}, ${parseInt(result[3], 16)}` : 
        '95, 179, 142';
}

// عرض الخيارات المحفوظة عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    displaySavedOptions();
});

function resetColors() {
    if (confirm('هل أنت متأكد من إعادة ضبط الألوان إلى الإعدادات الافتراضية؟ سيتم استعادة الألوان الخضراء الافتراضية.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ settings_route("reset-colors") }}';
        
        // إضافة CSRF token
        const csrfToken = document.querySelector('input[name="_token"]');
        if (csrfToken) {
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = csrfToken.value;
            form.appendChild(tokenInput);
        }
        
        // إضافة method spoofing
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'POST';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function resetHeroBackground() {
    if (confirm('هل أنت متأكد من إعادة الإعدادات الافتراضية؟ سيتم حذف الصورة الخلفية وإرجاع الخلفية الافتراضية (النجوم والتدرج).')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ settings_route("reset-hero-background") }}';
        
        // إضافة CSRF token
        const csrfToken = document.querySelector('input[name="_token"]');
        if (csrfToken) {
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = csrfToken.value;
            form.appendChild(tokenInput);
        }
        
        document.body.appendChild(form);
        form.submit();
    }
}

// دالة إعادة الإعدادات الافتراضية لخلفية الأقسام
function resetSectionBackground(sectionKey) {
    if (confirm('هل أنت متأكد من إعادة الإعدادات الافتراضية لهذا القسم؟ سيتم حذف الصورة الخلفية وإرجاع الشفافية إلى القيمة الافتراضية.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ settings_route("reset-section-background", ["section" => "__SECTION__"]) }}'.replace('__SECTION__', sectionKey);
        
        // إضافة CSRF token
        const csrfToken = document.querySelector('input[name="_token"]');
        if (csrfToken) {
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = csrfToken.value;
            form.appendChild(tokenInput);
        }
        
        document.body.appendChild(form);
        form.submit();
    }
}

// إدارة قوالب الهيرو
function toggleHeroTemplateSections() {
    const templateType = document.getElementById('hero_template_type').value;
    const defaultSection = document.querySelector('.hero-template-default-section');
    const videoSection = document.querySelector('.hero-template-video-section');
    const sliderSection = document.querySelector('.hero-template-slider-section');
    
    // إخفاء جميع الأقسام
    if (defaultSection) defaultSection.style.display = 'none';
    if (videoSection) videoSection.style.display = 'none';
    if (sliderSection) sliderSection.style.display = 'none';
    
    // إظهار القسم المطلوب
    if (templateType === 'default' && defaultSection) {
        defaultSection.style.display = 'block';
    } else if (templateType === 'video' && videoSection) {
        videoSection.style.display = 'block';
    } else if (templateType === 'slider' && sliderSection) {
        sliderSection.style.display = 'block';
    }
}

// تشغيل الدالة عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    toggleHeroTemplateSections();
    
    // معاينة صور السلايدر
    const sliderInput = document.getElementById('hero_slider_images_input');
    const previewContainer = document.getElementById('hero_slider_preview_container');
    const previewDiv = document.getElementById('hero_slider_preview');
    
    if (sliderInput && previewContainer && previewDiv) {
        sliderInput.addEventListener('change', function(e) {
            const files = e.target.files;
            if (files.length > 0) {
                previewContainer.innerHTML = '';
                previewDiv.style.display = 'block';
                
                Array.from(files).forEach((file, index) => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const div = document.createElement('div');
                            div.style.cssText = 'position: relative; border: 2px solid rgba(255, 255, 255, 0.1); border-radius: 12px; overflow: hidden; background: rgba(255, 255, 255, 0.05);';
                            
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.style.cssText = 'width: 100%; height: 150px; object-fit: cover;';
                            
                            const inputDiv = document.createElement('div');
                            inputDiv.style.cssText = 'padding: 0.75rem;';
                            
                            const titleInput = document.createElement('input');
                            titleInput.type = 'text';
                            titleInput.name = 'hero_slider_titles[]';
                            titleInput.placeholder = 'عنوان الصورة (اختياري)';
                            titleInput.className = 'form-control';
                            titleInput.style.cssText = 'margin-bottom: 0.5rem;';
                            
                            const descInput = document.createElement('textarea');
                            descInput.name = 'hero_slider_descriptions[]';
                            descInput.placeholder = 'وصف الصورة (اختياري)';
                            descInput.className = 'form-control';
                            descInput.rows = 2;
                            descInput.style.cssText = 'margin-bottom: 0.5rem;';
                            
                            const orderInput = document.createElement('input');
                            orderInput.type = 'number';
                            orderInput.name = 'hero_slider_orders[]';
                            orderInput.value = index;
                            orderInput.placeholder = 'الترتيب';
                            orderInput.className = 'form-control';
                            orderInput.style.cssText = 'margin-bottom: 0.5rem;';
                            
                            inputDiv.appendChild(titleInput);
                            inputDiv.appendChild(descInput);
                            inputDiv.appendChild(orderInput);
                            
                            div.appendChild(img);
                            div.appendChild(inputDiv);
                            previewContainer.appendChild(div);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            } else {
                previewDiv.style.display = 'none';
            }
        });
    }
});
</script>

