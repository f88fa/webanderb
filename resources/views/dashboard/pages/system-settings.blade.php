@php
    $settings = $settings ?? [];
    $hexVal = function($key, $default = '#5FB38E') use ($settings) {
        $v = old($key, $settings[$key] ?? $default);
        $n = normalize_css_hex($v);
        return $n !== '' ? $n : $default;
    };
@endphp
<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-sliders-h"></i> إعدادات النظام
        </h1>
        <p class="page-subtitle">قسم تقنية المعلومات — هنا تجد: <strong>شعار الجهة</strong>، <strong>ألوان لوحة التحكم</strong>، <strong>شكل ورقة الخطابات</strong> (رأس / وسط / تذييل)</p>
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

    {{-- لا تستخدم form متداخلة؛ النموذج الرئيسي فقط. أزرار "الإعدادات الافتراضية" تُرسل عبر JS --}}
    <form method="POST" action="{{ route('wesal.system-settings.update') }}" enctype="multipart/form-data" id="system-settings-form">
        @csrf

        {{-- قسم تقنية المعلومات: شعار الجهة --}}
        <h2 style="color: var(--text-primary); margin: 0 0 1rem 0; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-laptop-code" style="color: var(--primary-color);"></i>
            قسم تقنية المعلومات
        </h2>

        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label class="form-label">
                <i class="fas fa-image"></i> شعار الجهة
            </label>
            @if(!empty($settings['organization_logo'] ?? ''))
                <div style="margin-bottom: 1rem;">
                    <img src="{{ image_asset_url($settings['organization_logo']) }}" alt="شعار الجهة"
                         style="max-width: 180px; max-height: 100px; object-fit: contain; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.2);">
                    <label style="display: inline-flex; align-items: center; gap: 0.5rem; margin-right: 1rem; margin-top: 0.5rem; cursor: pointer; color: rgba(255, 255, 255, 0.8); font-size: 0.9rem;">
                        <input type="checkbox" name="organization_logo_remove" value="1"> إزالة الشعار الحالي
                    </label>
                </div>
            @endif
            <input type="file" name="organization_logo" class="form-control" accept="image/*">
            <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
                شعار الجهة/المؤسسة (يُستخدم في التقارير والطباعة) — JPG, PNG, GIF — أقصى 10 ميجا
            </small>
        </div>

        <input type="hidden" name="organization_type" value="{{ $settings['organization_type'] ?? '' }}">

        {{-- قسم تقنية المعلومات: ألوان لوحة التحكم --}}
        <div style="margin-top: 2.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
            <h3 style="color: var(--primary-color); margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-paint-brush"></i>
                ألوان لوحة التحكم
            </h3>
            <p style="color: var(--text-secondary); margin-bottom: 1.5rem; font-size: 0.9rem;">
                تخصيص ألوان واجهة لوحة التحكم الداخلية فقط (لا تؤثر على الموقع الإلكتروني)
            </p>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-circle" style="color: {{ $settings['dashboard_primary_color'] ?? '#5FB38E' }};"></i> اللون الأساسي
                    </label>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <input type="color" id="dashboard_primary_color_picker" class="form-control"
                               value="{{ $hexVal('dashboard_primary_color', '#5FB38E') }}"
                               style="width: 80px; height: 50px; padding: 0; border: 2px solid rgba(255, 255, 255, 0.2); border-radius: 8px; cursor: pointer;"
                               onchange="var t=document.getElementById('dashboard_primary_color'); t.value=this.value; var icon=this.closest('.form-group').querySelector('label .fa-circle'); if(icon) icon.style.color=this.value;">
                        <input type="text" name="dashboard_primary_color" id="dashboard_primary_color" class="form-control"
                               value="{{ $hexVal('dashboard_primary_color', '#5FB38E') }}"
                               placeholder="#5FB38E" pattern="^#[0-9A-Fa-f]{6}$"
                               oninput="if(/^#[0-9A-Fa-f]{6}$/i.test(this.value)) { document.getElementById('dashboard_primary_color_picker').value=this.value; var icon=this.closest('.form-group').querySelector('label .fa-circle'); if(icon) icon.style.color=this.value; }">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-circle" style="color: {{ $settings['dashboard_primary_dark'] ?? '#1F6B4F' }};"></i> اللون الأساسي الداكن
                    </label>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <input type="color" id="dashboard_primary_dark_picker" class="form-control"
                               value="{{ $hexVal('dashboard_primary_dark', '#1F6B4F') }}"
                               style="width: 80px; height: 50px; padding: 0; border: 2px solid rgba(255, 255, 255, 0.2); border-radius: 8px; cursor: pointer;"
                               onchange="var t=document.getElementById('dashboard_primary_dark'); t.value=this.value; var icon=this.closest('.form-group').querySelector('label .fa-circle'); if(icon) icon.style.color=this.value;">
                        <input type="text" name="dashboard_primary_dark" id="dashboard_primary_dark" class="form-control"
                               value="{{ $hexVal('dashboard_primary_dark', '#1F6B4F') }}"
                               placeholder="#1F6B4F" pattern="^#[0-9A-Fa-f]{6}$"
                               oninput="if(/^#[0-9A-Fa-f]{6}$/i.test(this.value)) { document.getElementById('dashboard_primary_dark_picker').value=this.value; var icon=this.closest('.form-group').querySelector('label .fa-circle'); if(icon) icon.style.color=this.value; }">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-circle" style="color: {{ $settings['dashboard_secondary_color'] ?? '#A8DCC3' }};"></i> اللون الثانوي
                    </label>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <input type="color" id="dashboard_secondary_color_picker" class="form-control"
                               value="{{ $hexVal('dashboard_secondary_color', '#A8DCC3') }}"
                               style="width: 80px; height: 50px; padding: 0; border: 2px solid rgba(255, 255, 255, 0.2); border-radius: 8px; cursor: pointer;"
                               onchange="var t=document.getElementById('dashboard_secondary_color'); t.value=this.value; var icon=this.closest('.form-group').querySelector('label .fa-circle'); if(icon) icon.style.color=this.value;">
                        <input type="text" name="dashboard_secondary_color" id="dashboard_secondary_color" class="form-control"
                               value="{{ $hexVal('dashboard_secondary_color', '#A8DCC3') }}"
                               placeholder="#A8DCC3" pattern="^#[0-9A-Fa-f]{6}$"
                               oninput="if(/^#[0-9A-Fa-f]{6}$/i.test(this.value)) { document.getElementById('dashboard_secondary_color_picker').value=this.value; var icon=this.closest('.form-group').querySelector('label .fa-circle'); if(icon) icon.style.color=this.value; }">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-circle" style="color: {{ $settings['dashboard_accent_color'] ?? '#5FB38E' }};"></i> لون التمييز
                    </label>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <input type="color" id="dashboard_accent_color_picker" class="form-control"
                               value="{{ $hexVal('dashboard_accent_color', '#5FB38E') }}"
                               style="width: 80px; height: 50px; padding: 0; border: 2px solid rgba(255, 255, 255, 0.2); border-radius: 8px; cursor: pointer;"
                               onchange="var t=document.getElementById('dashboard_accent_color'); t.value=this.value; var icon=this.closest('.form-group').querySelector('label .fa-circle'); if(icon) icon.style.color=this.value;">
                        <input type="text" name="dashboard_accent_color" id="dashboard_accent_color" class="form-control"
                               value="{{ $hexVal('dashboard_accent_color', '#5FB38E') }}"
                               placeholder="#5FB38E" pattern="^#[0-9A-Fa-f]{6}$"
                               oninput="if(/^#[0-9A-Fa-f]{6}$/i.test(this.value)) { document.getElementById('dashboard_accent_color_picker').value=this.value; var icon=this.closest('.form-group').querySelector('label .fa-circle'); if(icon) icon.style.color=this.value; }">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-palette"></i> خلفية القائمة الجانبية</label>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <input type="color" id="dashboard_sidebar_bg_color" class="form-control" value="#0F3D2E"
                               style="width: 80px; height: 50px; padding: 0; border: 2px solid rgba(255, 255, 255, 0.2); border-radius: 8px; cursor: pointer;"
                               onchange="dashboardUpdateColorFromPicker('dashboard_sidebar_bg', this.value)">
                        <input type="text" name="dashboard_sidebar_bg" id="dashboard_sidebar_bg" class="form-control"
                               value="{{ old('dashboard_sidebar_bg', $settings['dashboard_sidebar_bg'] ?? 'rgba(15, 61, 46, 0.95)') }}"
                               placeholder="rgba(15, 61, 46, 0.95)"
                               oninput="dashboardUpdateColorPicker('dashboard_sidebar_bg_color', this.value)">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-square"></i> خلفية المحتوى (المنطقة الرئيسية)</label>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <input type="color" id="dashboard_content_bg_color" class="form-control" value="#FFFFFF"
                               style="width: 80px; height: 50px; padding: 0; border: 2px solid rgba(255, 255, 255, 0.2); border-radius: 8px; cursor: pointer;"
                               onchange="dashboardUpdateColorFromPicker('dashboard_content_bg', this.value)">
                        <input type="text" name="dashboard_content_bg" id="dashboard_content_bg" class="form-control"
                               value="{{ old('dashboard_content_bg', $settings['dashboard_content_bg'] ?? 'rgba(255, 255, 255, 0.05)') }}"
                               placeholder="rgba(255, 255, 255, 0.05)"
                               oninput="dashboardUpdateColorPicker('dashboard_content_bg_color', this.value)">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-th-large"></i> خلفية البطاقات (مستقل عن خلفية المحتوى)</label>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <input type="color" id="dashboard_card_bg_color" class="form-control" value="#FFFFFF"
                               style="width: 80px; height: 50px; padding: 0; border: 2px solid rgba(255, 255, 255, 0.2); border-radius: 8px; cursor: pointer;"
                               onchange="dashboardUpdateColorFromPicker('dashboard_card_bg', this.value)">
                        <input type="text" name="dashboard_card_bg" id="dashboard_card_bg" class="form-control"
                               value="{{ old('dashboard_card_bg', $settings['dashboard_card_bg'] ?? 'rgba(255, 255, 255, 0.08)') }}"
                               placeholder="rgba(255, 255, 255, 0.08)"
                               oninput="dashboardUpdateColorPicker('dashboard_card_bg_color', this.value)">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-list"></i> لون نصوص القائمة الجانبية</label>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <input type="color" id="dashboard_sidebar_text_picker" class="form-control"
                               value="{{ $hexVal('dashboard_sidebar_text', $settings['dashboard_text_primary'] ?? '#FFFFFF') }}"
                               style="width: 80px; height: 50px; padding: 0; border: 2px solid rgba(255, 255, 255, 0.2); border-radius: 8px; cursor: pointer;"
                               onchange="var t=document.getElementById('dashboard_sidebar_text'); t.value=this.value;">
                        <input type="text" name="dashboard_sidebar_text" id="dashboard_sidebar_text" class="form-control"
                               value="{{ $hexVal('dashboard_sidebar_text', $settings['dashboard_text_primary'] ?? '#FFFFFF') }}"
                               placeholder="#FFFFFF" pattern="^#[0-9A-Fa-f]{6}$"
                               oninput="if(/^#[0-9A-Fa-f]{6}$/i.test(this.value)) { document.getElementById('dashboard_sidebar_text_picker').value=this.value; }">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-font"></i> لون النص الأساسي</label>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <input type="color" id="dashboard_text_primary_picker" class="form-control"
                               value="{{ $hexVal('dashboard_text_primary', '#FFFFFF') }}"
                               style="width: 80px; height: 50px; padding: 0; border: 2px solid rgba(255, 255, 255, 0.2); border-radius: 8px; cursor: pointer;"
                               onchange="var t=document.getElementById('dashboard_text_primary'); t.value=this.value; var icon=this.closest('.form-group').querySelector('label .fa-circle'); if(icon) icon.style.color=this.value;">
                        <input type="text" name="dashboard_text_primary" id="dashboard_text_primary" class="form-control"
                               value="{{ $hexVal('dashboard_text_primary', '#FFFFFF') }}"
                               placeholder="#FFFFFF" pattern="^#[0-9A-Fa-f]{6}$"
                               oninput="if(/^#[0-9A-Fa-f]{6}$/i.test(this.value)) { document.getElementById('dashboard_text_primary_picker').value=this.value; var icon=this.closest('.form-group').querySelector('label .fa-circle'); if(icon) icon.style.color=this.value; }">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-font"></i> لون النص الثانوي</label>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <input type="color" id="dashboard_text_secondary_picker" class="form-control"
                               value="{{ $hexVal('dashboard_text_secondary', '#FFFFFF') }}"
                               style="width: 80px; height: 50px; padding: 0; border: 2px solid rgba(255, 255, 255, 0.2); border-radius: 8px; cursor: pointer;"
                               onchange="var t=document.getElementById('dashboard_text_secondary'); t.value=this.value; var icon=this.closest('.form-group').querySelector('label .fa-circle'); if(icon) icon.style.color=this.value;">
                        <input type="text" name="dashboard_text_secondary" id="dashboard_text_secondary" class="form-control"
                               value="{{ $hexVal('dashboard_text_secondary', '#FFFFFF') }}"
                               placeholder="#FFFFFF" pattern="^#[0-9A-Fa-f]{6}$"
                               oninput="if(/^#[0-9A-Fa-f]{6}$/i.test(this.value)) { document.getElementById('dashboard_text_secondary_picker').value=this.value; var icon=this.closest('.form-group').querySelector('label .fa-circle'); if(icon) icon.style.color=this.value; }">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-border-style"></i> لون الحدود</label>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <input type="color" id="dashboard_border_color_color" class="form-control" value="#FFFFFF"
                               style="width: 80px; height: 50px; padding: 0; border: 2px solid rgba(255, 255, 255, 0.2); border-radius: 8px; cursor: pointer;"
                               onchange="dashboardUpdateColorFromPicker('dashboard_border_color', this.value)">
                        <input type="text" name="dashboard_border_color" id="dashboard_border_color" class="form-control"
                               value="{{ old('dashboard_border_color', $settings['dashboard_border_color'] ?? 'rgba(255, 255, 255, 0.1)') }}"
                               placeholder="rgba(255, 255, 255, 0.1)"
                               oninput="dashboardUpdateColorPicker('dashboard_border_color_color', this.value)">
                    </div>
                </div>
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label class="form-label"><i class="fas fa-paint-roller"></i> التدرج اللوني للخلفية</label>
                    <textarea name="dashboard_bg_gradient" class="form-control" rows="3"
                              placeholder="linear-gradient(180deg, #0F3D2E 0%, #1F6B4F 30%, #5FB38E 60%, #A8DCC3 85%, #FFFFFF 100%)">{{ old('dashboard_bg_gradient', $settings['dashboard_bg_gradient'] ?? 'linear-gradient(180deg, #0F3D2E 0%, #1F6B4F 30%, #5FB38E 60%, #A8DCC3 85%, #FFFFFF 100%)') }}</textarea>
                </div>
                <div class="form-group" style="grid-column: 1 / -1;">
                    <button type="button" class="btn btn-secondary" data-post-url="{{ route('dashboard.settings.reset-dashboard-colors') }}" data-confirm="هل تريد استعادة الألوان الافتراضية للوحة التحكم؟">
                        <i class="fas fa-undo"></i> الإعدادات الافتراضية للألوان
                    </button>
                </div>
            </div>
        </div>

        {{-- شكل ورقة الخطابات — تحكم ديناميكي من إعدادات النظام --}}
        <div style="margin-top: 2.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
            <h3 style="color: var(--primary-color); margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-file-alt"></i>
                شكل ورقة الخطابات
            </h3>
            <p style="color: var(--text-secondary); margin-bottom: 1.5rem; font-size: 0.9rem;">
                ضبط التصميم الظاهر في أعلى الورقة (رأس)، أو في النص (وسط/ختم مائي)، أو في الأسفل (تذييل) عند طباعة الخطابات. تحكم مرن: لا شيء، أو نص/HTML، أو صورة.
            </p>

            @foreach(['header' => ['الجزء العلوي (رأس الصفحة)', 'fa-arrow-up'], 'middle' => ['الجزء الأوسط (خلفية/ختم مائي)', 'fa-align-center'], 'footer' => ['الجزء السفلي (تذييل)', 'fa-arrow-down']] as $zone => $label)
            @php
                $typeVal = old("letter_paper_{$zone}_type", $settings["letter_paper_{$zone}_type"] ?? 'none');
                $contentVal = old("letter_paper_{$zone}_content", $settings["letter_paper_{$zone}_content"] ?? '');
                $hasImage = ($typeVal === 'image' && !empty($contentVal));
            @endphp
            <div class="letter-paper-zone" style="background: rgba(255,255,255,0.04); border-radius: 12px; padding: 1.25rem; margin-bottom: 1.25rem; border: 1px solid var(--border-color);">
                <h4 style="color: var(--text-primary); margin: 0 0 1rem 0; font-size: 1rem;">
                    <i class="fas {{ $label[1] }}" style="color: var(--primary-color); margin-left: 0.35rem;"></i>
                    {{ $label[0] }}
                </h4>
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label">نوع المحتوى</label>
                    <select name="letter_paper_{{ $zone }}_type" id="letter_paper_{{ $zone }}_type" class="form-control letter-zone-type" data-zone="{{ $zone }}" style="max-width: 220px;">
                        <option value="none" {{ $typeVal === 'none' ? 'selected' : '' }}>لا شيء</option>
                        <option value="html" {{ $typeVal === 'html' ? 'selected' : '' }}>نص أو HTML</option>
                        <option value="image" {{ $typeVal === 'image' ? 'selected' : '' }}>صورة</option>
                    </select>
                </div>
                <div id="letter_paper_{{ $zone }}_html_wrap" class="letter-zone-html-wrap" style="display: {{ $typeVal === 'html' ? 'block' : 'none' }};">
                    <label class="form-label">محتوى HTML أو النص (يُعرض في {{ $label[0] }})</label>
                    <textarea name="letter_paper_{{ $zone }}_content" id="letter_paper_{{ $zone }}_content" class="form-control" rows="4" placeholder="مثال: <div style='text-align:center'><img src='...' /><p>اسم الجهة</p></div>">{{ $typeVal === 'html' ? $contentVal : '' }}</textarea>
                    <small style="color: rgba(255,255,255,0.6); margin-top: 0.35rem; display: block;">يمكنك استخدام HTML بسيط أو نص. للصور استخدم مسار من الرفع أو اختيار «صورة» أدناه.</small>
                </div>
                <div id="letter_paper_{{ $zone }}_image_wrap" class="letter-zone-image-wrap" style="display: {{ $typeVal === 'image' ? 'block' : 'none' }};">
                    @if($zone === 'header')
                    <p style="color: var(--primary-color); font-size: 0.85rem; margin-bottom: 0.75rem; padding: 0.5rem 0.75rem; background: rgba(95,179,142,0.15); border-radius: 8px; border: 1px solid rgba(95,179,142,0.3);">
                        <strong>مقاسات الرفع الموصى بها للرأس:</strong> عرض 100% (كامل ورقة A4). الارتفاع: 25–30 mm.<br>
                        <span style="opacity: 0.9;">بالبكسل (96 dpi): <strong>794 × 95 px</strong> أو <strong>794 × 113 px</strong>. أو بالمليمتر: <strong>210 × 25 mm</strong> إلى <strong>210 × 30 mm</strong>.</span>
                    </p>
                    @elseif($zone === 'footer')
                    <p style="color: var(--primary-color); font-size: 0.85rem; margin-bottom: 0.75rem; padding: 0.5rem 0.75rem; background: rgba(95,179,142,0.15); border-radius: 8px; border: 1px solid rgba(95,179,142,0.3);">
                        <strong>مقاسات الرفع الموصى بها للتذييل:</strong> عرض 100% (كامل ورقة A4). الارتفاع: 20–25 mm.<br>
                        <span style="opacity: 0.9;">بالبكسل (96 dpi): <strong>794 × 76 px</strong> أو <strong>794 × 95 px</strong>. أو بالمليمتر: <strong>210 × 20 mm</strong> إلى <strong>210 × 25 mm</strong>.</span>
                    </p>
                    @else
                    <p style="color: var(--primary-color); font-size: 0.85rem; margin-bottom: 0.75rem; padding: 0.5rem 0.75rem; background: rgba(95,179,142,0.15); border-radius: 8px; border: 1px solid rgba(95,179,142,0.3);">
                        <strong>مقاسات الرفع الموصى بها للختم المائي (الوسط):</strong> صورة مربعة أو دائرية، تظهر شفافة في منتصف الورقة.<br>
                        <span style="opacity: 0.9;">بالبكسل: <strong>300 × 300 px</strong> أو <strong>400 × 400 px</strong>. خلفية شفافة (PNG) مناسبة.</span>
                    </p>
                    @endif
                    @if($hasImage)
                    <div style="margin-bottom: 0.75rem;">
                        <img src="{{ image_asset_url($contentVal) }}" alt="صورة {{ $label[0] }}" style="max-height: 120px; max-width: 100%; object-fit: contain; border-radius: 8px; border: 1px solid var(--border-color);">
                        <label style="display: inline-flex; align-items: center; gap: 0.5rem; margin-right: 0.75rem; margin-top: 0.5rem; cursor: pointer; color: rgba(255,255,255,0.8); font-size: 0.85rem;">
                            <input type="checkbox" name="letter_paper_{{ $zone }}_image_remove" value="1"> إزالة الصورة الحالية
                        </label>
                    </div>
                    @endif
                    <input type="file" name="letter_paper_{{ $zone }}_image" class="form-control" accept="image/*">
                    <small style="color: rgba(255,255,255,0.6); margin-top: 0.35rem; display: block;">JPG, PNG, GIF, SVG — أقصى 5 ميجا</small>
                </div>
            </div>
            @endforeach
            <div style="margin-top: 1rem; display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center;">
                <a href="{{ route('wesal.letter-paper-preview') }}" target="_blank" rel="noopener" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                    <i class="fas fa-eye"></i> معاينة ورقة الخطاب
                </a>
                <button type="button" class="btn btn-secondary" data-post-url="{{ route('dashboard.settings.reset-letter-paper') }}" data-confirm="هل تريد استعادة شكل ورقة الخطاب إلى الافتراضي (إزالة الرأس والتذييل والختم المائي)؟">
                    <i class="fas fa-undo"></i> الإعدادات الافتراضية لشكل الورقة
                </button>
            </div>
        </div>

        {{-- إعدادات الحضور والانصراف --}}
        <h2 style="color: var(--text-primary); margin: 2.5rem 0 1rem 0; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-fingerprint" style="color: var(--primary-color);"></i>
            إعدادات الحضور والانصراف
        </h2>
        <p style="color: var(--text-secondary); margin-bottom: 1.5rem; font-size: 0.9rem;">
            تحكم في مكان وطريقة السماح بتسجيل الحضور والانصراف: من أي مكان، أو من عنوان IP محدد، أو من موقع جغرافي (دائرة حول نقطة معينة).
        </p>

        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label class="form-label">وضع التسجيل</label>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="radio" name="attendance_mode" value="anywhere" class="attendance-mode-radio"
                        {{ old('attendance_mode', $settings['attendance_mode'] ?? 'anywhere') === 'anywhere' ? 'checked' : '' }}>
                    <span><strong>عام</strong> — السماح بتسجيل الحضور والانصراف من أي مكان وأي اتصال</span>
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="radio" name="attendance_mode" value="ip_restricted" class="attendance-mode-radio"
                        {{ old('attendance_mode', $settings['attendance_mode'] ?? '') === 'ip_restricted' ? 'checked' : '' }}>
                    <span><strong>تقييد بعنوان IP</strong> — السماح فقط عند الاتصال من عناوين IP مسموحة (مثلاً شبكة المكتب)</span>
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="radio" name="attendance_mode" value="location_restricted" class="attendance-mode-radio"
                        {{ old('attendance_mode', $settings['attendance_mode'] ?? '') === 'location_restricted' ? 'checked' : '' }}>
                    <span><strong>تقييد بموقع جغرافي</strong> — السماح فقط عند التواجد داخل دائرة حول نقطة محددة (بالمتر)</span>
                </label>
            </div>
        </div>

        <div id="attendance-ip-wrap" class="form-group" style="margin-bottom: 1.5rem; display: none;">
            <p style="color: var(--text-primary); margin-bottom: 0.75rem; font-weight: 600;">
                <i class="fas fa-desktop"></i> عنوان IP الشبكة المتصل منها حالياً: <code style="background: rgba(0,0,0,0.2); padding: 0.2rem 0.5rem; border-radius: 4px;">{{ client_ip() }}</code>
            </p>
            <label class="form-label"><i class="fas fa-network-wired"></i> عناوين IP المسموحة (كل عنوان في حقل)</label>
            <div id="attendance-ip-list" style="display: flex; flex-direction: column; gap: 0.5rem;">
                @php
                    $allowedIps = old('attendance_allowed_ips');
                    if ($allowedIps === null) {
                        $raw = $settings['attendance_allowed_ips'] ?? '';
                        $allowedIps = $raw !== '' ? array_values(array_filter(preg_split('/[\s,،;\n]+/', $raw, -1, PREG_SPLIT_NO_EMPTY))) : [];
                    }
                    if (!is_array($allowedIps)) $allowedIps = [$allowedIps];
                    if (count($allowedIps) === 0) $allowedIps = [''];
                @endphp
                @foreach($allowedIps as $ip)
                <div class="attendance-ip-row" style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="text" name="attendance_allowed_ips[]" class="form-control" value="{{ is_string($ip) ? $ip : '' }}" placeholder="مثال: 192.168.1.1" style="max-width: 220px;">
                    <button type="button" class="btn btn-secondary attendance-ip-remove" style="padding: 0.35rem 0.6rem;" title="حذف"><i class="fas fa-times"></i></button>
                </div>
                @endforeach
            </div>
            <button type="button" id="attendance-ip-add" class="btn btn-secondary" style="margin-top: 0.75rem;">
                <i class="fas fa-plus"></i> إضافة عنوان IP
            </button>
            <small style="color: rgba(255,255,255,0.6); margin-top: 0.35rem; display: block;">أضف عناوين IP المسموح لها بتسجيل الحضور والانصراف. يمكنك نسخ «عنوان IP الشبكة المتصل منها حالياً» أعلاه وإضافته إن كان مسموحاً.</small>
        </div>

        <div id="attendance-location-wrap" style="display: none;">
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label"><i class="fas fa-map-marker-alt"></i> نقطة المركز (حدّدها على الخريطة أو ابحث أو استخدم موقعك)</label>
                <p style="color: var(--primary-color); font-size: 0.9rem; margin-bottom: 0.75rem;"><i class="fas fa-info-circle"></i> سيُطلب منك السماح بالموقع لتحديد موقعك تلقائياً. إن لم تُسمح، تظهر الخريطة على السعودية ويمكنك البحث عن عنوان أو النقر على الخريطة.</p>
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center; margin-bottom: 0.75rem;">
                    <button type="button" id="attendance-location-my-position" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                        <i class="fas fa-location-crosshairs"></i> موقعي الحالي
                    </button>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center; flex: 1; min-width: 200px;">
                        <input type="text" id="attendance-location-search" class="form-control" placeholder="ابحث عن مكان (مثال: الرياض، جدة، شارع الملك فهد)"
                            style="max-width: 320px; min-width: 200px;">
                        <button type="button" id="attendance-location-search-btn" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                            <i class="fas fa-search"></i> بحث
                        </button>
                    </div>
                </div>
                <div id="attendance-location-map" style="height: 360px; width: 100%; max-width: 700px; border-radius: 12px; border: 2px solid var(--border-color); background: #1a1a2e;"></div>
                <div style="display: flex; flex-wrap: wrap; gap: 1rem; margin-top: 0.75rem;">
                    <div>
                        <label class="form-label" style="margin-bottom: 0.25rem;">خط العرض</label>
                        <input type="text" name="attendance_location_lat" id="attendance_location_lat" class="form-control" placeholder="24.7136"
                            value="{{ old('attendance_location_lat', $settings['attendance_location_lat'] ?? '') }}" style="min-width: 140px;">
                    </div>
                    <div>
                        <label class="form-label" style="margin-bottom: 0.25rem;">خط الطول</label>
                        <input type="text" name="attendance_location_lng" id="attendance_location_lng" class="form-control" placeholder="46.6753"
                            value="{{ old('attendance_location_lng', $settings['attendance_location_lng'] ?? '') }}" style="min-width: 140px;">
                    </div>
                </div>
            </div>
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">نصف القطر المسموح (متر)</label>
                <input type="number" name="attendance_location_radius_meters" id="attendance_location_radius_meters" class="form-control" min="10" max="5000" step="10"
                    value="{{ old('attendance_location_radius_meters', $settings['attendance_location_radius_meters'] ?? '100') }}" style="max-width: 180px;">
                <small style="color: rgba(255,255,255,0.6); margin-top: 0.35rem; display: block;">إذا كان الموظف داخل هذه الدائرة (بالأمتار) من النقطة أعلاه يُسمح بتسجيل الحضور والانصراف. الدائرة تظهر على الخريطة.</small>
            </div>
        </div>

        <div class="form-group" style="margin-top: 2rem;">
            <button type="submit" name="save_settings" class="btn btn-primary">
                <i class="fas fa-save"></i> حفظ إعدادات النظام
            </button>
        </div>
    </form>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin></script>
<script>
(function() {
    document.querySelectorAll('.letter-zone-type').forEach(function(sel) {
        sel.addEventListener('change', function() {
            var zone = this.dataset.zone;
            var v = this.value;
            var htmlWrap = document.getElementById('letter_paper_' + zone + '_html_wrap');
            var imageWrap = document.getElementById('letter_paper_' + zone + '_image_wrap');
            if (htmlWrap) htmlWrap.style.display = v === 'html' ? 'block' : 'none';
            if (imageWrap) imageWrap.style.display = v === 'image' ? 'block' : 'none';
        });
    });
})();
(function() {
    function rgbaToHex(rgba) {
        if (!rgba || rgba.trim() === '') return null;
        if (rgba.startsWith('#')) return rgba;
        var match = rgba.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*([\d.]+))?\)/);
        if (match) {
            var r = parseInt(match[1], 10).toString(16).padStart(2, '0');
            var g = parseInt(match[2], 10).toString(16).padStart(2, '0');
            var b = parseInt(match[3], 10).toString(16).padStart(2, '0');
            return '#' + r + g + b;
        }
        return null;
    }
    function dashboardUpdateColorPicker(pickerId, textValue) {
        var picker = document.getElementById(pickerId);
        if (!picker) return;
        var hex = rgbaToHex(textValue);
        if (hex) picker.value = hex;
    }
    function dashboardUpdateColorFromPicker(textFieldId, hexValue) {
        var textField = document.getElementById(textFieldId);
        if (!textField) return;
        var currentValue = textField.value || '';
        if (currentValue.indexOf('rgba') !== -1) {
            var match = currentValue.match(/rgba?\([\d\s,]+,\s*([\d.]+)\)/);
            var alpha = match ? match[1] : '1';
            var r = parseInt(hexValue.slice(1, 3), 16);
            var g = parseInt(hexValue.slice(3, 5), 16);
            var b = parseInt(hexValue.slice(5, 7), 16);
            textField.value = 'rgba(' + r + ', ' + g + ', ' + b + ', ' + alpha + ')';
        } else {
            textField.value = hexValue;
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        ['dashboard_sidebar_bg', 'dashboard_content_bg', 'dashboard_card_bg', 'dashboard_border_color'].forEach(function(fieldId) {
            var textField = document.getElementById(fieldId);
            var picker = document.getElementById(fieldId + '_color');
            if (textField && picker) {
                var hex = rgbaToHex(textField.value);
                if (hex) picker.value = hex;
            }
        });
        var form = document.getElementById('system-settings-form');
        if (form) {
            form.addEventListener('submit', function() {
                ['dashboard_sidebar_bg', 'dashboard_content_bg', 'dashboard_card_bg', 'dashboard_border_color'].forEach(function(fieldId) {
                    var picker = document.getElementById(fieldId + '_color');
                    if (picker && typeof dashboardUpdateColorFromPicker === 'function') {
                        dashboardUpdateColorFromPicker(fieldId, picker.value);
                    }
                });
            });
        }
    });
})();
(function() {
    var attendanceMapInstance = null;
    var attendanceMarker = null;
    var attendanceCircle = null;
    var SAUDI_CENTER = [24.7136, 46.6753];

    function parseNum(val) {
        var n = parseFloat(val);
        return isNaN(n) ? null : n;
    }

    function setMapPosition(lat, lng, updateInputs) {
        var map = attendanceMapInstance;
        var latIn = document.getElementById('attendance_location_lat');
        var lngIn = document.getElementById('attendance_location_lng');
        if (!map || !attendanceMarker || !attendanceCircle) return;
        attendanceMarker.setLatLng([lat, lng]);
        attendanceCircle.setLatLng([lat, lng]);
        map.setView([lat, lng], map.getZoom());
        if (updateInputs !== false && latIn && lngIn) {
            latIn.value = lat.toFixed(6);
            lngIn.value = lng.toFixed(6);
        }
    }

    function initAttendanceMap() {
        var wrap = document.getElementById('attendance-location-wrap');
        var mapEl = document.getElementById('attendance-location-map');
        if (!wrap || !mapEl || wrap.style.display === 'none') return;
        if (attendanceMapInstance) return;

        var latIn = document.getElementById('attendance_location_lat');
        var lngIn = document.getElementById('attendance_location_lng');
        var radiusIn = document.getElementById('attendance_location_radius_meters');
        var hasSaved = parseNum(latIn && latIn.value) != null && parseNum(lngIn && lngIn.value) != null;
        var lat = parseNum(latIn && latIn.value) || SAUDI_CENTER[0];
        var lng = parseNum(lngIn && lngIn.value) || SAUDI_CENTER[1];
        var radius = Math.max(10, parseNum(radiusIn && radiusIn.value) || 100);

        var map = L.map('attendance-location-map').setView([lat, lng], hasSaved ? 15 : 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        var marker = L.marker([lat, lng]).addTo(map);
        var circle = L.circle([lat, lng], { radius: radius, color: '#5FB38E', fillColor: '#5FB38E', fillOpacity: 0.2, weight: 2 }).addTo(map);

        map.on('click', function(e) {
            var ll = e.latlng;
            marker.setLatLng(ll);
            circle.setLatLng(ll);
            if (latIn) latIn.value = ll.lat.toFixed(6);
            if (lngIn) lngIn.value = ll.lng.toFixed(6);
        });

        if (radiusIn) {
            radiusIn.addEventListener('input', function() {
                var r = parseNum(this.value);
                if (r != null && r > 0) circle.setRadius(r);
            });
        }
        if (latIn && lngIn) {
            function updateFromInputs() {
                var la = parseNum(latIn.value);
                var ln = parseNum(lngIn.value);
                if (la != null && ln != null) setMapPosition(la, ln, false);
            }
            latIn.addEventListener('change', updateFromInputs);
            lngIn.addEventListener('change', updateFromInputs);
        }

        attendanceMapInstance = map;
        attendanceMarker = marker;
        attendanceCircle = circle;

        // إذا لم يكن هناك موقع محفوظ، جرّب الموقع الحالي تلقائياً
        if (!hasSaved && navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    setMapPosition(pos.coords.latitude, pos.coords.longitude);
                    map.setZoom(15);
                },
                function() {
                    map.setView(SAUDI_CENTER, 6);
                },
                { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
            );
        }

        // زر "موقعي الحالي"
        var btnMyPos = document.getElementById('attendance-location-my-position');
        if (btnMyPos) {
            btnMyPos.addEventListener('click', function() {
                if (!navigator.geolocation) { alert('المتصفح لا يدعم تحديد الموقع.'); return; }
                btnMyPos.disabled = true;
                btnMyPos.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحديد...';
                navigator.geolocation.getCurrentPosition(
                    function(pos) {
                        setMapPosition(pos.coords.latitude, pos.coords.longitude);
                        map.setZoom(15);
                        btnMyPos.disabled = false;
                        btnMyPos.innerHTML = '<i class="fas fa-location-crosshairs"></i> موقعي الحالي';
                    },
                    function() {
                        alert('تعذّر الحصول على الموقع. تأكد من السماح للموقع أو استخدم البحث أو انقر على الخريطة.');
                        btnMyPos.disabled = false;
                        btnMyPos.innerHTML = '<i class="fas fa-location-crosshairs"></i> موقعي الحالي';
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            });
        }

        // بحث عن مكان (Nominatim)
        var searchInput = document.getElementById('attendance-location-search');
        var searchBtn = document.getElementById('attendance-location-search-btn');
        function doSearch() {
            var q = searchInput && searchInput.value.trim();
            if (!q) return;
            if (searchBtn) { searchBtn.disabled = true; searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> بحث...'; }
            var url = 'https://nominatim.openstreetmap.org/search?q=' + encodeURIComponent(q) + '&format=json&limit=1&countrycodes=sa';
            fetch(url, { headers: { 'Accept': 'application/json' } })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (searchBtn) { searchBtn.disabled = false; searchBtn.innerHTML = '<i class="fas fa-search"></i> بحث'; }
                    if (data && data[0]) {
                        var lat2 = parseFloat(data[0].lat);
                        var lng2 = parseFloat(data[0].lon);
                        setMapPosition(lat2, lng2);
                        map.setZoom(16);
                    } else {
                        alert('لم يتم العثور على نتائج. جرّب كلمات أخرى أو حدد النقطة على الخريطة.');
                    }
                })
                .catch(function() {
                    if (searchBtn) { searchBtn.disabled = false; searchBtn.innerHTML = '<i class="fas fa-search"></i> بحث'; }
                    alert('حدث خطأ أثناء البحث. جرّب لاحقاً.');
                });
        }
        if (searchBtn) searchBtn.addEventListener('click', doSearch);
        if (searchInput) searchInput.addEventListener('keypress', function(e) { if (e.key === 'Enter') { e.preventDefault(); doSearch(); } });
    }

    function toggleAttendanceExtra() {
        var mode = document.querySelector('input[name="attendance_mode"]:checked');
        var v = mode ? mode.value : 'anywhere';
        var ipWrap = document.getElementById('attendance-ip-wrap');
        var locWrap = document.getElementById('attendance-location-wrap');
        if (ipWrap) ipWrap.style.display = v === 'ip_restricted' ? 'block' : 'none';
        if (locWrap) locWrap.style.display = v === 'location_restricted' ? 'block' : 'none';
        if (v === 'location_restricted') {
            setTimeout(function() {
                if (attendanceMapInstance) attendanceMapInstance.invalidateSize();
                else initAttendanceMap();
            }, 100);
        }
    }
    document.querySelectorAll('.attendance-mode-radio').forEach(function(r) {
        r.addEventListener('change', toggleAttendanceExtra);
    });
    document.addEventListener('DOMContentLoaded', function() {
        toggleAttendanceExtra();
        if (document.getElementById('attendance-location-wrap').style.display !== 'none') setTimeout(initAttendanceMap, 300);
    });
})();
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        var list = document.getElementById('attendance-ip-list');
        var addBtn = document.getElementById('attendance-ip-add');
        if (!list || !addBtn) return;
        addBtn.addEventListener('click', function() {
            var row = document.createElement('div');
            row.className = 'attendance-ip-row';
            row.style.cssText = 'display: flex; align-items: center; gap: 0.5rem;';
            row.innerHTML = '<input type="text" name="attendance_allowed_ips[]" class="form-control" value="" placeholder="مثال: 192.168.1.1" style="max-width: 220px;">' +
                '<button type="button" class="btn btn-secondary attendance-ip-remove" style="padding: 0.35rem 0.6rem;" title="حذف"><i class="fas fa-times"></i></button>';
            list.appendChild(row);
            row.querySelector('.attendance-ip-remove').addEventListener('click', function() { row.remove(); });
        });
        list.addEventListener('click', function(e) {
            if (e.target.closest('.attendance-ip-remove')) e.target.closest('.attendance-ip-row').remove();
        });
    });
})();
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('button[data-post-url]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var url = this.getAttribute('data-post-url');
                var msg = this.getAttribute('data-confirm');
                if (msg && !confirm(msg)) return;
                var token = document.querySelector('#system-settings-form input[name="_token"]');
                if (!token) return;
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.style.display = 'none';
                var csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = token.value;
                form.appendChild(csrf);
                document.body.appendChild(form);
                form.submit();
            });
        });
    });
})();
</script>
