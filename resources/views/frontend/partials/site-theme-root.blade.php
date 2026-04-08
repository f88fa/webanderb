@php
    if (!function_exists('hexToRgb')) {
        function hexToRgb($hex) {
            $hex = str_replace('#', '', $hex);
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
            return "$r, $g, $b";
        }
    }

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
    $articleBgColor = $settings['article_bg_color'] ?? '#FFFFFF';
    $articleTextColor = $settings['article_text_color'] ?? '#0F3D2E';
    $articleTitleColor = $settings['article_title_color'] ?? $primaryColor;
    $articleMetaColor = $settings['article_meta_color'] ?? '#1F6B4F';
    $articleBorderColor = $settings['article_border_color'] ?? '#5FB38E';
    $articleCardBgColor = $settings['article_card_bg_color'] ?? '#FFFFFF';
    $articleCardBorderColor = $settings['article_card_border_color'] ?? '#1F6B4F';
    $articleButtonColor = $settings['article_button_color'] ?? $primaryColor;
    $articleButtonHoverColor = $settings['article_button_hover_color'] ?? $primaryDark;
    $logoIconSize = isset($settings['site_logo_icon_size']) && $settings['site_logo_icon_size'] !== '' ? (int)$settings['site_logo_icon_size'] : 70;
    $heroIconSize = isset($settings['site_hero_icon_size']) && $settings['site_hero_icon_size'] !== '' ? (int)$settings['site_hero_icon_size'] : 200;

    $primaryDarkRgb = hexToRgb($primaryDark);
    $primaryColorRgb = hexToRgb($primaryColor);
    $secondaryColorRgb = hexToRgb($secondaryColor);
    $cardBorderColorRgb = hexToRgb($cardBorderColor);
    $cardBgColorRgb = hexToRgb($cardBgColor);
    $navbarBorderColorRgb = hexToRgb($navbarBorderColor);
    $navbarTextColorRgb = hexToRgb($navbarTextColor);
    $footerBgColorRgb = hexToRgb($footerBgColor);
    $footerBorderColorRgb = hexToRgb($footerBorderColor);
    $footerIconColorRgb = hexToRgb($footerIconColor);
    $articleTextColorRgb = hexToRgb($articleTextColor);
    $articleTitleColorRgb = hexToRgb($articleTitleColor);
    $articleMetaColorRgb = hexToRgb($articleMetaColor);
    $articleBorderColorRgb = hexToRgb($articleBorderColor);
    $articleCardBorderColorRgb = hexToRgb($articleCardBorderColor);
    $articleButtonColorRgb = hexToRgb($articleButtonColor);
    $articleButtonHoverColorRgb = hexToRgb($articleButtonHoverColor);

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
        --article-bg-color: {{ $articleBgColor }};
        --article-text-color: {{ $articleTextColor }};
        --article-text-color-rgb: {{ $articleTextColorRgb }};
        --article-title-color: {{ $articleTitleColor }};
        --article-title-color-rgb: {{ $articleTitleColorRgb }};
        --article-meta-color: {{ $articleMetaColor }};
        --article-meta-color-rgb: {{ $articleMetaColorRgb }};
        --article-border-color: {{ $articleBorderColor }};
        --article-border-color-rgb: {{ $articleBorderColorRgb }};
        --article-card-bg-color: {{ $articleCardBgColor }};
        --article-card-border-color: {{ $articleCardBorderColor }};
        --article-card-border-color-rgb: {{ $articleCardBorderColorRgb }};
        --article-button-color: {{ $articleButtonColor }};
        --article-button-color-rgb: {{ $articleButtonColorRgb }};
        --article-button-hover-color: {{ $articleButtonHoverColor }};
        --article-button-hover-color-rgb: {{ $articleButtonHoverColorRgb }};
        --gradient-1: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $primaryDark }} 100%);
        --gradient-2: linear-gradient(135deg, {{ $secondaryColor }} 0%, {{ $primaryColor }} 100%);
        --gradient-3: linear-gradient(135deg, {{ $secondaryColor }} 0%, {{ $primaryColor }} 100%);
        --bg-dark: linear-gradient(180deg, {{ $primaryDark }} 0%, {{ $primaryColor }} 30%, {{ $secondaryColor }} 60%, #FFFFFF 85%, #FFFFFF 100%);
        --bg-darker: linear-gradient(180deg, {{ $primaryColor }} 0%, {{ $secondaryColor }} 40%, #FFFFFF 75%, #FFFFFF 100%);
        --primary-dark-rgb: {{ $primaryDarkRgb }};
        --primary-color-rgb: {{ $primaryColorRgb }};
        --secondary-color-rgb: {{ $secondaryColorRgb }};
        --shadow-glow: 0 0 30px rgba({{ $primaryColorRgb }}, 0.3);
        --logo-icon-size: {{ $logoIconSize }}px;
        --hero-icon-size: {{ $heroIconSize }}px;
    }
</style>
<script>
    window.siteSettings = {
        logoIconSize: {{ $logoIconSize }},
        heroIconSize: {{ $heroIconSize }}
    };
    console.log('Site Settings loaded:', window.siteSettings);
</script>
