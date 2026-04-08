<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings['page_staff_title'] ?? 'فريق العمل' }} - {{ $settings['site_title'] ?? 'الموقع' }}</title>
    
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

    <!-- Staff Section -->
    <section class="staff-section" style="padding: 6rem 0; min-height: 80vh; position: relative;"
             @if(!empty($settings['section_staff_bg_image']) && strpos($settings['section_staff_bg_image'], 'storage/') !== false)
             style="background-image: url('{{ image_asset_url($settings['section_staff_bg_image']) }}'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative; padding: 6rem 0; min-height: 80vh;"
             @endif>
        @if(!empty($settings['section_staff_bg_image']) && strpos($settings['section_staff_bg_image'], 'storage/') !== false)
        <div class="section-background-overlay" style="opacity: {{ (100 - ($settings['section_staff_bg_opacity'] ?? 30)) / 100 }};"></div>
        @endif
        <div class="container">
            <div class="section-header" style="text-align: center; margin-bottom: 4rem;">
                <span class="section-badge" style="display: inline-block; padding: 0.5rem 1.5rem; background: var(--primary-color); color: white; border-radius: 25px; font-size: 0.9rem; font-weight: 600; margin-bottom: 1rem;">الموظفين</span>
                <h1 class="section-title" style="font-size: 2.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem;">
                    {{ $settings['page_staff_title'] ?? 'فريق العمل' }}
                </h1>
                @if(!empty($settings['section_staff_description']))
                <p class="section-description" style="font-size: 1.1rem; color: var(--text-secondary); max-width: 600px; margin: 0 auto;">{{ $settings['section_staff_description'] }}</p>
                @else
                <p class="section-description" style="font-size: 1.1rem; color: var(--text-secondary); max-width: 600px; margin: 0 auto;">تعرف على فريق العمل المتميز الذي يعمل بجد لخدمتكم</p>
                @endif
            </div>

            @if($staff->count() > 0)
                <div class="staff-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem;">
                    @foreach($staff as $index => $member)
                        <div class="staff-card {{ $index === 0 ? 'first-staff' : '' }}" style="background: rgba(255, 255, 255, 0.05); border-radius: 20px; padding: 2rem; text-align: center; transition: all 0.4s ease; border: 1px solid rgba(255, 255, 255, 0.1); position: relative; overflow: hidden; {{ $index === 0 ? 'grid-column: 1 / -1; max-width: 400px; margin: 0 auto;' : '' }}">
                            <div class="staff-card-bg" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, rgba(95, 179, 142, 0.1) 0%, rgba(31, 107, 79, 0.1) 100%); opacity: 0; transition: opacity 0.4s ease; pointer-events: none;"></div>
                            
                            <div class="staff-image-wrapper" style="position: relative; width: {{ $index === 0 ? '200px' : '180px' }}; height: {{ $index === 0 ? '200px' : '180px' }}; margin: 0 auto 1.5rem; border-radius: 50%; overflow: hidden; border: 4px solid var(--primary-color); box-shadow: 0 10px 30px rgba(95, 179, 142, 0.3); transition: all 0.4s ease;">
                                @if($member->image)
                                    <img src="{{ image_asset_url($member->image) }}" alt="{{ $member->name }}" 
                                         style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;">
                                @else
                                    <div style="width: 100%; height: 100%; background: var(--primary-color); display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-user" style="font-size: {{ $index === 0 ? '4.5rem' : '4rem' }}; color: white;"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <h3 class="staff-name" style="font-size: {{ $index === 0 ? '1.8rem' : '1.5rem' }}; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem; position: relative; z-index: 1;">{{ $member->name }}</h3>
                            <p class="staff-position" style="font-size: {{ $index === 0 ? '1.2rem' : '1.1rem' }}; color: var(--primary-color); font-weight: 600; margin-bottom: 1rem; position: relative; z-index: 1;">{{ $member->position }}</p>
                            
                            <div class="staff-decoration" style="position: absolute; bottom: 0; left: 0; right: 0; height: 4px; background: var(--primary-color); transform: scaleX(0); transition: transform 0.4s ease;"></div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state" style="text-align: center; padding: 4rem 2rem;">
                    <i class="fas fa-user-tie" style="font-size: 4rem; color: rgba(255, 255, 255, 0.3); margin-bottom: 1.5rem;"></i>
                    <p style="font-size: 1.2rem; color: var(--text-secondary);">لا يوجد موظفين متاحين حالياً</p>
                </div>
            @endif
        </div>
    </section>

    <style>
        .staff-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(95, 179, 142, 0.2);
            border-color: var(--primary-color);
        }
        
        .staff-card:hover .staff-card-bg {
            opacity: 1;
        }
        
        .staff-card:hover .staff-image-wrapper {
            transform: scale(1.05);
            box-shadow: 0 15px 40px rgba(95, 179, 142, 0.4);
        }
        
        .staff-card:hover .staff-image-wrapper img {
            transform: scale(1.1);
        }
        
        .staff-card:hover .staff-decoration {
            transform: scaleX(1);
        }

        .first-staff {
            grid-column: 1 / -1 !important;
        }

        @media (max-width: 768px) {
            .staff-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)) !important;
                gap: 1.5rem !important;
            }
            
            .staff-image-wrapper {
                width: 150px !important;
                height: 150px !important;
            }
            
            .first-staff .staff-image-wrapper {
                width: 180px !important;
                height: 180px !important;
            }
        }
    </style>
    
    @include('frontend.partials.footer')
    
    <script src="{{ asset('assets/js/frontend.js') }}?v={{ $settings['settings_updated_at'] ?? '1' }}"></script>
</body>
</html>

