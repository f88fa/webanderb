<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اللوائح والسياسات - {{ $settings['site_title'] ?? 'الموقع' }}</title>
    
    <!-- Favicon - استخدام نفس أيقونة الهيرو -->
    @if(!empty($settings['site_icon_file']))
        <link rel="icon" type="image/png" href="{{ image_asset_url($settings['site_icon_file']) }}">
        <link rel="shortcut icon" type="image/png" href="{{ image_asset_url($settings['site_icon_file']) }}">
        <link rel="apple-touch-icon" href="{{ image_asset_url($settings['site_icon_file']) }}">
    @endif
    
    <link rel="stylesheet" href="{{ asset('assets/css/frontend.css') }}?v={{ $settings['settings_updated_at'] ?? '1' }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet">
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
        $navbarBgColor = $settings['navbar_bg_color'] ?? '#FFFFFF';
        $navbarTextColor = $settings['navbar_text_color'] ?? '#0F3D2E';
        $navbarBorderColor = $settings['navbar_border_color'] ?? '#0F3D2E';
        $heroFooterBg = $settings['hero_footer_bg_color'] ?? null;
        $heroFooterText = $settings['hero_footer_text_color'] ?? null;
        $heroFooterTitle = $settings['hero_footer_title_color'] ?? null;
        $heroTitleColor = $heroFooterTitle ?? $settings['site_hero_title_color'] ?? '#5FB38E';
        $footerBgColor = $heroFooterBg ?? $settings['footer_bg_color'] ?? $primaryDark;
        $footerTextColor = $heroFooterText ?? $settings['footer_text_color'] ?? '#FFFFFF';
        $footerTextSecondaryColor = $heroFooterText ? 'rgba(' . hexToRgb($heroFooterText) . ', 0.85)' : ($settings['footer_text_secondary_color'] ?? 'rgba(255, 255, 255, 0.8)');
        $footerLinkColor = $heroFooterText ? 'rgba(' . hexToRgb($heroFooterText) . ', 0.85)' : ($settings['footer_link_color'] ?? 'rgba(255, 255, 255, 0.8)');
        $footerLinkHoverColor = $heroFooterText ?? $settings['footer_link_hover_color'] ?? '#FFFFFF';
        $footerBorderColor = $settings['footer_border_color'] ?? $primaryColor;
        $footerTitleColor = $heroFooterTitle ?? $settings['footer_title_color'] ?? '#FFFFFF';
        $footerIconColor = $settings['hero_social_icons_color'] ?? $settings['hero_circle_icon_color'] ?? $settings['footer_icon_color'] ?? $primaryColor;
        
        // Convert hex to rgb
        function hexToRgb($hex) {
            $hex = str_replace('#', '', $hex);
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
            return "$r, $g, $b";
        }
        
        $primaryDarkRgb = hexToRgb($primaryDark);
        $primaryColorRgb = hexToRgb($primaryColor);
        $secondaryColorRgb = hexToRgb($secondaryColor);
        $cardBorderColorRgb = hexToRgb($cardBorderColor);
        $cardBgColorRgb = hexToRgb($cardBgColor);
        $navbarBorderColorRgb = hexToRgb($navbarBorderColor);
        $navbarTextColorRgb = hexToRgb($navbarTextColor);
        // Footer RGB + hero overlay
        $footerBgColorRgb = hexToRgb($footerBgColor);
        $footerBorderColorRgb = hexToRgb($footerBorderColor);
        $footerIconColorRgb = hexToRgb($footerIconColor);
        $textPrimaryColorRgb = hexToRgb($textPrimaryColor);
        $textSecondaryColorRgb = hexToRgb($textSecondaryColor);
        $iconColorRgb = hexToRgb($iconColor);
        
        // Convert opacity from 0-100 to 0-1
        $cardBgOpacityDecimal = (float)$cardBgOpacity / 100;
        $pageContentBg = $settings['page_content_bg_color'] ?? '#FFFFFF';
        $pageContentText = $settings['page_content_text_color'] ?? '#0F3D2E';
        $pageContentTitle = $settings['page_content_title_color'] ?? '#5FB38E';
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
            --hero-title-color: {{ $heroTitleColor }};
            --hero-title-font-size: {{ ($settings['hero_title_font_size'] ?? '56') }}px;
            --hero-text-color: {{ $footerTextColor }};
            --hero-circle-bg: {{ $settings['hero_circle_bg_color'] ?? $primaryColor }};
            --hero-circle-icon-color: {{ $settings['hero_circle_icon_color'] ?? '#FFFFFF' }};
            --hero-social-icon-color: {{ $settings['hero_social_icons_color'] ?? '#FFFFFF' }};
            --navbar-bg-color: {{ $navbarBgColor }};
            --navbar-text-color: {{ $navbarTextColor }};
            --navbar-border-color: {{ $navbarBorderColor }};
            --navbar-text-color-rgb: {{ $navbarTextColorRgb }};
            --navbar-border-color-rgb: {{ $navbarBorderColorRgb }};
            --footer-bg-color: {{ $footerBgColor }};
            --footer-bg-color-rgb: {{ $footerBgColorRgb }};
            --footer-text-color: {{ $footerTextColor }};
            --footer-text-secondary-color: {{ $footerTextSecondaryColor }};
            --footer-link-color: {{ $footerLinkColor }};
            --footer-link-hover-color: {{ $footerLinkHoverColor }};
            --footer-border-color: {{ $footerBorderColor }};
            --footer-border-color-rgb: {{ $footerBorderColorRgb }};
            --footer-title-color: {{ $footerTitleColor }};
            --footer-icon-color: {{ $footerIconColor }};
            --footer-icon-color-rgb: {{ $footerIconColorRgb }};
            --gradient-1: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $primaryDark }} 100%);
            --gradient-2: linear-gradient(135deg, {{ $secondaryColor }} 0%, {{ $primaryColor }} 100%);
            --gradient-3: linear-gradient(135deg, {{ $secondaryColor }} 0%, {{ $primaryColor }} 100%);
            --shadow-glow: 0 0 30px rgba({{ $primaryColorRgb }}, 0.3);
            --primary-dark-rgb: {{ $primaryDarkRgb }};
            --primary-color-rgb: {{ $primaryColorRgb }};
            --secondary-color-rgb: {{ $secondaryColorRgb }};
            --text-primary-rgb: {{ $textPrimaryColorRgb }};
            --text-secondary-rgb: {{ $textSecondaryColorRgb }};
            --icon-color-rgb: {{ $iconColorRgb }};
            --page-content-bg: {{ $pageContentBg }};
            --page-content-text: {{ $pageContentText }};
            --page-content-title: {{ $pageContentTitle }};
        }
    </style>
</head>
<body>
    @include('frontend.partials.header')

    <!-- قسم اللوائح والسياسات -->
    <section class="policies-section" style="padding: 8rem 0; min-height: 80vh;">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">اللوائح والسياسات</span>
                <h2 class="section-title">اللوائح والسياسات</h2>
                <p class="section-description">تصفح اللوائح والسياسات المعتمدة</p>
            </div>
            
            @if($categories->count() > 0)
                <div style="margin-top: 4rem;">
                    @foreach($categories as $category)
                        <div class="policy-category" style="background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(10px); border-radius: 20px; padding: 2.5rem; margin-bottom: 2.5rem; border: 2px solid rgba(95, 179, 142, 0.2);">
                            <h3 style="color: var(--text-primary); font-size: 1.8rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;">
                                <i class="fas fa-folder" style="color: var(--primary-color);"></i>
                                {{ $category->name }}
                            </h3>
                            
                            @if($category->activePolicies->count() > 0)
                                <div class="policies-list" style="display: grid; gap: 1.5rem;">
                                    @foreach($category->activePolicies as $policy)
                                        <div class="policy-item" style="background: rgba(255, 255, 255, 0.05); padding: 1.5rem; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.1); transition: all 0.3s ease;">
                                            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                                                <div style="flex: 1; min-width: 200px;">
                                                    <h4 style="color: var(--text-primary); margin-bottom: 0.5rem; font-size: 1.2rem;">{{ $policy->title }}</h4>
                                                </div>
                                                @if($policy->file)
                                                    <a href="{{ image_asset_url($policy->file) }}" target="_blank" 
                                                       class="btn btn-primary" 
                                                       style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                                                        <i class="fas fa-file-pdf"></i>
                                                        <span>تحميل PDF</span>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p style="color: rgba(255, 255, 255, 0.5); text-align: center; padding: 2rem 0;">
                                    لا توجد لوائح/سياسات متاحة في هذا التصنيف
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 4rem 0; color: rgba(255, 255, 255, 0.5);">
                    <i class="fas fa-file-alt" style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                    <p>لا توجد لوائح/سياسات متاحة حالياً</p>
                </div>
            @endif
        </div>
    </section>

    @include('frontend.partials.footer')

    <script src="{{ asset('assets/js/frontend.js') }}?v={{ $settings['settings_updated_at'] ?? '1' }}"></script>
</body>
</html>

