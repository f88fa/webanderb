# الملفات المعدلة بخصوص الألوان وإعدادات الموقع

## قائمة الملفات المعدلة:

### 1. `app/Http/Controllers/SettingsController.php`
### 2. `resources/views/dashboard/pages/settings.blade.php`
### 3. `resources/views/frontend/index.blade.php`
### 4. `public/assets/css/frontend.css`
### 5. `routes/web.php`

---

## 1. ملف: `app/Http/Controllers/SettingsController.php`

### التعديلات:
- إضافة validation rules للألوان الجديدة (السطور 69-75)
- إضافة دالة `resetColors()` لإعادة ضبط الألوان (السطور 675-707)

#### الكود المضاف:

```php
// في دالة validation (السطور 69-75)
'site_text_primary_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
'site_text_secondary_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
'site_icon_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
'site_card_bg_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
'site_card_border_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
'site_card_bg_opacity' => 'nullable|integer|min:0|max:100',
'site_card_title_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',

// دالة resetColors() (السطور 675-707)
public function resetColors(Request $request)
{
    // Reset site colors to default (green theme)
    SiteSetting::setValue('site_primary_color', '#5FB38E');
    SiteSetting::setValue('site_primary_dark', '#1F6B4F');
    SiteSetting::setValue('site_secondary_color', '#A8DCC3');
    SiteSetting::setValue('site_accent_color', '#5FB38E');
    
    // Reset text, icon, and card colors to default (green theme)
    SiteSetting::setValue('site_text_primary_color', '#5FB38E');
    SiteSetting::setValue('site_text_secondary_color', '#5FB38E');
    SiteSetting::setValue('site_icon_color', '#5FB38E');
    SiteSetting::setValue('site_card_bg_color', '#FFFFFF');
    SiteSetting::setValue('site_card_border_color', '#0F3D2E');
    SiteSetting::setValue('site_card_bg_opacity', '10');
    SiteSetting::setValue('site_card_title_color', '#5FB38E');
    
    // Reset dashboard colors to default
    SiteSetting::setValue('dashboard_primary_color', '#5FB38E');
    SiteSetting::setValue('dashboard_primary_dark', '#1F6B4F');
    SiteSetting::setValue('dashboard_secondary_color', '#A8DCC3');
    SiteSetting::setValue('dashboard_accent_color', '#5FB38E');
    SiteSetting::setValue('dashboard_sidebar_bg', 'rgba(15, 61, 46, 0.95)');
    SiteSetting::setValue('dashboard_content_bg', 'rgba(255, 255, 255, 0.05)');
    SiteSetting::setValue('dashboard_text_primary', '#FFFFFF');
    SiteSetting::setValue('dashboard_text_secondary', '#FFFFFF');
    SiteSetting::setValue('dashboard_border_color', 'rgba(255, 255, 255, 0.1)');
    SiteSetting::setValue('dashboard_bg_gradient', 'linear-gradient(180deg, #0F3D2E 0%, #1F6B4F 30%, #5FB38E 60%, #A8DCC3 85%, #FFFFFF 100%)');

    return back()->with('success_message', 'تم إعادة ضبط ألوان الموقع إلى الإعدادات الافتراضية بنجاح!');
}
```

---

## 2. ملف: `resources/views/dashboard/pages/settings.blade.php`

### التعديلات:
- إضافة شريط التنقل السريع (السطور 27-64)
- إضافة قسم ألوان النصوص والأيقونات والبطاقات (السطور 959-1117)
- إضافة زر إعادة ضبط الألوان (في قسم ألوان الموقع)

#### الكود المضاف:

```blade
<!-- شريط التنقل السريع (السطور 27-64) -->
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
        <a href="#register-btn" class="settings-nav-btn" onclick="scrollToSection('register-btn'); return false;">
            <i class="fas fa-user-plus"></i> زر التسجيل
        </a>
        <a href="#social-media" class="settings-nav-btn" onclick="scrollToSection('social-media'); return false;">
            <i class="fas fa-share-alt"></i> التواصل الاجتماعي
        </a>
        <a href="#site-colors" class="settings-nav-btn" onclick="scrollToSection('site-colors'); return false;">
            <i class="fas fa-palette"></i> ألوان الموقع
        </a>
        <a href="#text-colors" class="settings-nav-btn" onclick="scrollToSection('text-colors'); return false;">
            <i class="fas fa-font"></i> ألوان النصوص
        </a>
        <a href="#dashboard-colors" class="settings-nav-btn" onclick="scrollToSection('dashboard-colors'); return false;">
            <i class="fas fa-paint-brush"></i> ألوان لوحة التحكم
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
</style>

<script>
    function scrollToSection(sectionId) {
        const element = document.getElementById(sectionId);
        if (element) {
            const offset = 100;
            const elementPosition = element.getBoundingClientRect().top;
            const offsetPosition = elementPosition + window.pageYOffset - offset;
            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });
        }
    }
</script>

<!-- قسم ألوان النصوص والأيقونات والبطاقات (السطور 959-1117) -->
<div id="text-colors" style="scroll-margin-top: 120px; margin-top: 3rem; padding-top: 2rem; border-top: 2px solid rgba(255, 255, 255, 0.1);">
    <h3 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
        <i class="fas fa-font" style="color: var(--primary-color);"></i>
        ألوان النصوص والأيقونات والبطاقات
    </h3>
    
    <!-- لون النصوص الأساسي -->
    <div class="form-group">
        <label class="form-label">
            <i class="fas fa-text-height" style="color: {{ $settings['site_text_primary_color'] ?? '#5FB38E' }};"></i> لون النصوص الأساسي
        </label>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <input type="color" id="site_text_primary_color_picker" class="form-control" 
                   value="{{ old('site_text_primary_color', $settings['site_text_primary_color'] ?? '#5FB38E') }}" 
                   style="width: 80px; height: 50px; padding: 0; border: 2px solid rgba(255, 255, 255, 0.2); border-radius: 8px; cursor: pointer;"
                   onchange="document.getElementById('site_text_primary_color').value = this.value">
            <input type="text" name="site_text_primary_color" id="site_text_primary_color" class="form-control" 
                   value="{{ old('site_text_primary_color', $settings['site_text_primary_color'] ?? '#5FB38E') }}" 
                   placeholder="#5FB38E" 
                   pattern="^#[0-9A-Fa-f]{6}$"
                   oninput="if(/^#[0-9A-Fa-f]{6}$/i.test(this.value)) { document.getElementById('site_text_primary_color_picker').value = this.value; }">
        </div>
    </div>
    
    <!-- لون النصوص الثانوي -->
    <div class="form-group">
        <label class="form-label">
            <i class="fas fa-text-width" style="color: {{ $settings['site_text_secondary_color'] ?? '#5FB38E' }};"></i> لون النصوص الثانوي
        </label>
        <!-- نفس الكود السابق -->
    </div>
    
    <!-- لون الأيقونات -->
    <div class="form-group">
        <label class="form-label">
            <i class="fas fa-icons" style="color: {{ $settings['site_icon_color'] ?? '#5FB38E' }};"></i> لون الأيقونات
        </label>
        <!-- نفس الكود السابق -->
    </div>
    
    <!-- لون خلفية البطاقات -->
    <div class="form-group">
        <label class="form-label">
            <i class="fas fa-square" style="color: {{ $settings['site_card_bg_color'] ?? '#FFFFFF' }};"></i> لون خلفية البطاقات
        </label>
        <!-- نفس الكود السابق -->
    </div>
    
    <!-- لون حدود البطاقات -->
    <div class="form-group">
        <label class="form-label">
            <i class="fas fa-border-style" style="color: {{ $settings['site_card_border_color'] ?? '#0F3D2E' }};"></i> لون حدود البطاقات
        </label>
        <!-- نفس الكود السابق -->
    </div>
    
    <!-- شفافية البطاقات -->
    <div class="form-group">
        <label class="form-label">
            <i class="fas fa-adjust"></i> شفافية البطاقات (0 - 100)
        </label>
        <input type="range" name="site_card_bg_opacity" id="site_card_bg_opacity"
               min="0" max="100" 
               value="{{ old('site_card_bg_opacity', $settings['site_card_bg_opacity'] ?? '10') }}"
               oninput="document.getElementById('card_opacity_value').textContent = this.value + '%'">
        <span id="card_opacity_value" style="min-width: 50px; text-align: center; color: var(--primary-color); font-weight: 600;">
            {{ $settings['site_card_bg_opacity'] ?? '10' }}%
        </span>
    </div>
    
    <!-- لون عناوين البطاقات -->
    <div class="form-group">
        <label class="form-label">
            <i class="fas fa-heading" style="color: {{ $settings['site_card_title_color'] ?? '#5FB38E' }};"></i> لون عناوين البطاقات
        </label>
        <!-- نفس الكود السابق -->
    </div>
</div>

<!-- زر إعادة ضبط الألوان -->
<button type="button" onclick="resetColors()" class="btn btn-secondary" style="background: rgba(239, 68, 68, 0.2); border-color: rgba(239, 68, 68, 0.3); color: #ef4444; margin-top: 1rem;">
    <i class="fas fa-undo"></i> إعادة ضبط الألوان إلى الإعدادات الافتراضية
</button>

<script>
function resetColors() {
    if (confirm('هل أنت متأكد من إعادة ضبط جميع الألوان إلى الإعدادات الافتراضية؟')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("dashboard.settings.reset-colors") }}';
        form.innerHTML = '@csrf';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
```

---

## 3. ملف: `resources/views/frontend/index.blade.php`

### التعديلات:
- إضافة PHP variables للألوان الجديدة (السطور 15-21)
- إضافة CSS variables في `<style>` (السطور 46-54)

#### الكود المضاف:

```php
@php
    $primaryColor = $settings['site_primary_color'] ?? '#5FB38E';
    $primaryDark = $settings['site_primary_dark'] ?? '#1F6B4F';
    $secondaryColor = $settings['site_secondary_color'] ?? '#A8DCC3';
    $accentColor = $settings['site_accent_color'] ?? '#5FB38E';
    $textPrimaryColor = $settings['site_text_primary_color'] ?? '#5FB38E';
    $textSecondaryColor = $settings['site_text_secondary_color'] ?? '#5FB38E';
    $iconColor = $settings['site_icon_color'] ?? '#5FB38E';
    $cardBgColor = $settings['site_card_bg_color'] ?? '#FFFFFF';
    $cardBorderColor = $settings['site_card_border_color'] ?? '#0F3D2E';
    $cardBgOpacity = $settings['site_card_bg_opacity'] ?? '10';
    $cardTitleColor = $settings['site_card_title_color'] ?? '#5FB38E';
    
    // تحويل hex إلى rgb
    function hexToRgb($hex) {
        $hex = str_replace('#', '', $hex);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return "$r, $g, $b";
    }
    
    $primaryDarkRgb = hexToRgb($primaryDark);
    $primaryColorRgb = hexToRgb($primaryColor);
    $cardBorderColorRgb = hexToRgb($cardBorderColor);
    $cardBgColorRgb = hexToRgb($cardBgColor);
    
    // تحويل opacity من 0-100 إلى 0-1
    $cardBgOpacityDecimal = (float)$cardBgOpacity / 100;
@endphp
<style>
    :root {
        --primary-color: {{ $primaryColor }};
        --primary-dark: {{ $primaryDark }};
        --secondary-color: {{ $secondaryColor }};
        --accent-color: {{ $accentColor }};
        --text-primary: {{ $textPrimaryColor }};
        --text-secondary: {{ $textSecondaryColor }};
        --icon-color: {{ $iconColor }};
        --card-bg-color: {{ $cardBgColor }};
        --card-bg-color-rgb: {{ $cardBgColorRgb }};
        --card-bg-opacity: {{ $cardBgOpacityDecimal }};
        --card-border-color: {{ $cardBorderColor }};
        --card-border-color-rgb: {{ $cardBorderColorRgb }};
        --card-title-color: {{ $cardTitleColor }};
        --gradient-1: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $primaryDark }} 100%);
        --gradient-2: linear-gradient(135deg, {{ $secondaryColor }} 0%, {{ $primaryColor }} 100%);
        --gradient-3: linear-gradient(135deg, {{ $secondaryColor }} 0%, {{ $primaryColor }} 100%);
        --bg-dark: linear-gradient(180deg, {{ $primaryDark }} 0%, {{ $primaryColor }} 30%, {{ $secondaryColor }} 60%, #FFFFFF 85%, #FFFFFF 100%);
        --bg-darker: linear-gradient(180deg, {{ $primaryColor }} 0%, {{ $secondaryColor }} 40%, #FFFFFF 75%, #FFFFFF 100%);
        --primary-dark-rgb: {{ $primaryDarkRgb }};
        --primary-color-rgb: {{ $primaryColorRgb }};
        --shadow-glow: 0 0 30px rgba({{ $primaryColorRgb }}, 0.3);
    }
</style>
```

---

## 4. ملف: `public/assets/css/frontend.css`

### التعديلات:
- استبدال الألوان الثابتة بـ CSS variables في جميع أنحاء الملف

#### أمثلة على التعديلات:

```css
/* قبل التعديل */
.vision-title, .mission-title {
    color: white;
    background: var(--gradient-1);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* بعد التعديل */
.vision-title, .mission-title {
    color: var(--card-title-color, var(--text-primary, white));
    background: none;
    -webkit-background-clip: unset;
    -webkit-text-fill-color: var(--card-title-color, var(--text-primary, white));
}

/* قبل التعديل */
.service-title {
    color: white !important;
}

/* بعد التعديل */
.service-title {
    color: var(--card-title-color, white) !important;
}

/* قبل التعديل */
.vision-card, .mission-card {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(15, 61, 46, 0.1);
}

/* بعد التعديل */
.vision-card, .mission-card {
    background: rgba(var(--card-bg-color-rgb, 255, 255, 255), var(--card-bg-opacity, 0.08));
    border: 1px solid rgba(var(--card-border-color-rgb, 15, 61, 46), 0.1);
}

/* قبل التعديل */
.news-card-title {
    color: var(--text-primary);
}

/* بعد التعديل */
.news-card-title {
    color: var(--card-title-color, var(--text-primary));
}

/* قبل التعديل */
.project-name {
    color: var(--text-primary, white);
}

/* بعد التعديل */
.project-name {
    color: var(--card-title-color, var(--text-primary, white));
}

/* قبل التعديل */
.feature-title {
    color: rgba(var(--primary-dark-rgb, 15, 61, 46), 0.95);
}

/* بعد التعديل */
.feature-title {
    color: var(--card-title-color, rgba(var(--primary-dark-rgb, 15, 61, 46), 0.95));
}
```

---

## 5. ملف: `routes/web.php`

### التعديلات:
- إضافة route جديد لإعادة ضبط الألوان (السطر 146)

#### الكود المضاف:

```php
Route::post('/settings/reset-colors', [SettingsController::class, 'resetColors'])->name('.settings.reset-colors');
```

---

## ملخص التعديلات:

### الألوان الجديدة المضافة:
1. **ألوان النصوص:**
   - `site_text_primary_color` (افتراضي: `#5FB38E`)
   - `site_text_secondary_color` (افتراضي: `#5FB38E`)

2. **لون الأيقونات:**
   - `site_icon_color` (افتراضي: `#5FB38E`)

3. **ألوان البطاقات:**
   - `site_card_bg_color` (افتراضي: `#FFFFFF`)
   - `site_card_border_color` (افتراضي: `#0F3D2E`)
   - `site_card_bg_opacity` (افتراضي: `10`)
   - `site_card_title_color` (افتراضي: `#5FB38E`)

### الميزات المضافة:
- ✅ شريط تنقل علوي للوصول السريع للأقسام
- ✅ زر إعادة ضبط الألوان إلى القيم الافتراضية
- ✅ نظام CSS variables للألوان الديناميكية
- ✅ دعم RGB للألوان لاستخدام `rgba()`
- ✅ تحكم منفصل في ألوان النصوص والأيقونات والبطاقات

---

**تاريخ التعديل:** 2026-02-04
**المطور:** AI Assistant

