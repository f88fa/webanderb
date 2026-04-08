# جميع الملفات المعدلة - تفاصيل كاملة

## 📋 قائمة الملفات المعدلة (5 ملفات):

1. ✅ `app/Http/Controllers/SettingsController.php`
2. ✅ `resources/views/dashboard/pages/settings.blade.php`
3. ✅ `resources/views/frontend/index.blade.php`
4. ✅ `public/assets/css/frontend.css`
5. ✅ `routes/web.php`

---

## 1️⃣ ملف: `app/Http/Controllers/SettingsController.php`

### 📍 الموقع: `app/Http/Controllers/SettingsController.php`

### ✏️ التعديلات:

#### أ) إضافة Validation Rules (السطور 69-76):
```php
'site_text_primary_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
'site_text_secondary_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
'site_icon_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
'site_card_bg_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
'site_card_border_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
'site_card_bg_opacity' => 'nullable|integer|min:0|max:100',
'site_card_title_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
'site_hero_title_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
```

#### ب) دالة resetColors() (السطور 675-707):
```php
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
    SiteSetting::setValue('site_hero_title_color', '#5FB38E');
    
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

## 2️⃣ ملف: `resources/views/dashboard/pages/settings.blade.php`

### 📍 الموقع: `resources/views/dashboard/pages/settings.blade.php`

### ✏️ التعديلات:

#### أ) شريط التنقل السريع (السطور 27-64):
```blade
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
```

#### ب) قسم ألوان النصوص والأيقونات والبطاقات (السطور 959-1117):
```blade
<!-- قسم ألوان النصوص والأيقونات والبطاقات -->
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
        <small style="color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; display: block;">
            اللون المستخدم في النصوص الرئيسية والعناوين
        </small>
    </div>
    
    <!-- نفس النمط لبقية الألوان: site_text_secondary_color, site_icon_color, site_card_bg_color, site_card_border_color, site_card_bg_opacity, site_card_title_color, site_hero_title_color -->
</div>
```

#### ج) إضافة IDs للأقسام:
- `id="general-settings"` - قسم المعلومات العامة
- `id="logo-settings"` - قسم إعدادات الشعار
- `id="hero-template"` - قسم قوالب الهيرو
- `id="hero-background"` - قسم خلفية الهيرو
- `id="register-btn"` - قسم زر التسجيل
- `id="social-media"` - قسم التواصل الاجتماعي
- `id="site-colors"` - قسم ألوان الموقع
- `id="text-colors"` - قسم ألوان النصوص
- `id="dashboard-colors"` - قسم ألوان لوحة التحكم
- `id="license"` - قسم الرخصة
- `id="floating-buttons"` - قسم الأزرار المتحركة

---

## 3️⃣ ملف: `resources/views/frontend/index.blade.php`

### 📍 الموقع: `resources/views/frontend/index.blade.php`

### ✏️ التعديلات:

#### أ) إضافة PHP Variables (السطور 15-22):
```php
$textPrimaryColor = $settings['site_text_primary_color'] ?? '#5FB38E';
$textSecondaryColor = $settings['site_text_secondary_color'] ?? '#5FB38E';
$iconColor = $settings['site_icon_color'] ?? '#5FB38E';
$cardBgColor = $settings['site_card_bg_color'] ?? '#FFFFFF';
$cardBorderColor = $settings['site_card_border_color'] ?? '#0F3D2E';
$cardBgOpacity = $settings['site_card_bg_opacity'] ?? '10';
$cardTitleColor = $settings['site_card_title_color'] ?? '#5FB38E';
$heroTitleColor = $settings['site_hero_title_color'] ?? '#5FB38E';
```

#### ب) إضافة CSS Variables (السطور 46-56):
```php
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
    --hero-title-color: {{ $heroTitleColor }};
    --gradient-1: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $primaryDark }} 100%);
    --gradient-2: linear-gradient(135deg, {{ $secondaryColor }} 0%, {{ $primaryColor }} 100%);
    --gradient-3: linear-gradient(135deg, {{ $secondaryColor }} 0%, {{ $primaryColor }} 100%);
    --bg-dark: linear-gradient(180deg, {{ $primaryDark }} 0%, {{ $primaryColor }} 30%, {{ $secondaryColor }} 60%, #FFFFFF 85%, #FFFFFF 100%);
    --bg-darker: linear-gradient(180deg, {{ $primaryColor }} 0%, {{ $secondaryColor }} 40%, #FFFFFF 75%, #FFFFFF 100%);
    --primary-dark-rgb: {{ $primaryDarkRgb }};
    --primary-color-rgb: {{ $primaryColorRgb }};
    --shadow-glow: 0 0 30px rgba({{ $primaryColorRgb }}, 0.3);
}
```

---

## 4️⃣ ملف: `public/assets/css/frontend.css`

### 📍 الموقع: `public/assets/css/frontend.css`

### ✏️ التعديلات:

#### أ) بطاقة اللوقو - محاذاة مستقرة (السطور 63-88):
```css
.logo-card-wrapper {
    position: fixed;
    top: 1.5rem;
    left: 50%;
    transform: translateX(calc(-50% - 600px));
    z-index: 1001;
    pointer-events: none;
}

@media (min-width: 1400px) {
    .logo-card-wrapper {
        transform: translateX(calc(-50% - 700px));
    }
}

@media (min-width: 1600px) {
    .logo-card-wrapper {
        transform: translateX(calc(-50% - 750px));
    }
}

@media (min-width: 1920px) {
    .logo-card-wrapper {
        transform: translateX(calc(-50% - 800px));
    }
}
```

#### ب) الشريط العلوي - توسيط ديناميكي (السطور 175-204):
```css
.navbar {
    position: fixed;
    top: 1.5rem;
    left: 50%;
    transform: translateX(-50%) translateY(20px);
    width: auto;
    max-width: calc(100% - 3rem - 160px);
    min-width: fit-content;
    background: #FFFFFF;
    backdrop-filter: blur(30px);
    border: 1px solid rgba(var(--card-border-color-rgb, 15, 61, 46), 0.1);
    border-radius: 25px;
    z-index: 1000;
    padding: 1rem 1.5rem;
    transition: var(--transition);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(var(--card-border-color-rgb, 15, 61, 46), 0.05);
    /* توسيط ديناميكي */
    display: flex;
    justify-content: center;
    align-items: center;
}

@media (max-width: 1399px) {
    .navbar {
        max-width: calc(100% - 2rem - 160px);
        padding: 1rem 1rem;
    }
}
```

#### ج) Container والقائمة - توسيط ديناميكي (السطور 206-236):
```css
.navbar .container {
    display: flex;
    justify-content: center;
    align-items: center;
    max-width: 100%;
    position: relative;
    gap: 0.4rem;
    flex-wrap: nowrap;
    overflow: visible;
    padding: 0;
    margin: 0;
    width: 100%;
    /* توسيط ديناميكي للمحتوى */
    flex: 1;
}

.nav-menu {
    display: flex;
    list-style: none;
    gap: 0.3rem;
    align-items: center;
    margin: 0;
    padding: 0;
    flex-wrap: nowrap;
    justify-content: center;
    width: 100%;
    overflow: visible;
    position: relative;
    flex: 1;
}

.nav-menu-wrapper:not(.mobile-menu-wrapper) {
    flex: 1;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    min-width: 0;
    gap: 0;
    overflow: visible;
    position: relative;
    z-index: 100;
    margin: 0;
    padding: 0;
    width: 100%;
}
```

#### د) عنوان الهيرو - لون خاص (السطور 862-871):
```css
.hero-title {
    font-size: 3.5rem;
    font-weight: 900;
    margin-bottom: 1.5rem;
    color: var(--hero-title-color, var(--primary-color));
    background: none;
    -webkit-background-clip: unset;
    -webkit-text-fill-color: var(--hero-title-color, var(--primary-color));
    background-clip: unset;
    line-height: 1.2;
}
```

#### هـ) عنوان قالب الفيديو (السطر 7268):
```css
.hero-video-title {
    font-family: 'Cairo', sans-serif;
    font-size: 2.5rem;
    font-weight: 600;
    color: var(--hero-title-color, #ffffff);
    /* ... */
}
```

#### و) عنوان قالب السلايدر (السطر 7407):
```css
.hero-slide-title {
    font-size: 2.5rem;
    font-weight: 900;
    margin-bottom: 1rem;
    color: var(--hero-title-color, #ffffff);
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}
```

#### ز) تحديثات أخرى في CSS:
- جميع عناوين البطاقات تستخدم `var(--card-title-color)`
- جميع النصوص تستخدم `var(--text-primary)` و `var(--text-secondary)`
- جميع الأيقونات تستخدم `var(--icon-color)`
- جميع خلفيات البطاقات تستخدم `rgba(var(--card-bg-color-rgb), var(--card-bg-opacity))`

---

## 5️⃣ ملف: `routes/web.php`

### 📍 الموقع: `routes/web.php`

### ✏️ التعديلات:

#### إضافة Route جديد (السطر 146):
```php
Route::post('/settings/reset-colors', [SettingsController::class, 'resetColors'])->name('.settings.reset-colors');
```

---

## 📊 ملخص التعديلات:

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

4. **لون عنوان الهيرو:**
   - `site_hero_title_color` (افتراضي: `#5FB38E`)

### الميزات المضافة:
- ✅ شريط تنقل علوي للوصول السريع للأقسام
- ✅ زر إعادة ضبط الألوان إلى القيم الافتراضية
- ✅ نظام CSS variables للألوان الديناميكية
- ✅ دعم RGB للألوان لاستخدام `rgba()`
- ✅ تحكم منفصل في ألوان النصوص والأيقونات والبطاقات وعنوان الهيرو
- ✅ توسيط ديناميكي للشريط العلوي
- ✅ محاذاة مستقرة للشريط العلوي وبطاقة اللوقو

---

**تاريخ التعديل:** 2026-02-05  
**المطور:** AI Assistant

