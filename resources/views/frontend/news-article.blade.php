<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $news->title }} - {{ $settings['site_name'] ?? 'الموقع' }}</title>
    
    <!-- Favicon - استخدام نفس أيقونة الهيرو -->
    @if(!empty($settings['site_icon_file']))
        <link rel="icon" type="image/png" href="{{ image_asset_url($settings['site_icon_file']) }}">
        <link rel="shortcut icon" type="image/png" href="{{ image_asset_url($settings['site_icon_file']) }}">
        <link rel="apple-touch-icon" href="{{ image_asset_url($settings['site_icon_file']) }}">
    @endif
    <meta name="description" content="{{ mb_substr(strip_tags($news->content), 0, 160) }}">
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
        // Article colors - افتراضي: خلفية بيضاء، كل النصوص والأزرار والعناصر أسود
        $articleBgColor = $settings['article_bg_color'] ?? '#FFFFFF';
        $articleTextColor = $settings['article_text_color'] ?? '#000000';
        $articleTitleColor = $settings['article_title_color'] ?? '#000000';
        $articleMetaColor = $settings['article_meta_color'] ?? '#000000';
        $articleBorderColor = $settings['article_border_color'] ?? '#000000';
        $articleCardBgColor = $settings['article_card_bg_color'] ?? '#FFFFFF';
        $articleCardBorderColor = $settings['article_card_border_color'] ?? '#000000';
        $articleButtonColor = $settings['article_button_color'] ?? '#000000';
        $articleButtonHoverColor = $settings['article_button_hover_color'] ?? '#333333';
        
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
        // Article RGB
        $articleTextColorRgb = hexToRgb($articleTextColor);
        $articleTitleColorRgb = hexToRgb($articleTitleColor);
        $articleMetaColorRgb = hexToRgb($articleMetaColor);
        $articleBorderColorRgb = hexToRgb($articleBorderColor);
        $articleCardBorderColorRgb = hexToRgb($articleCardBorderColor);
        $articleButtonColorRgb = hexToRgb($articleButtonColor);
        $articleButtonHoverColorRgb = hexToRgb($articleButtonHoverColor);
        
        // Convert opacity from 0-100 to 0-1
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
            --gradient-1: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $primaryDark }} 100%);
            --gradient-2: linear-gradient(135deg, {{ $secondaryColor }} 0%, {{ $primaryColor }} 100%);
            --gradient-3: linear-gradient(135deg, {{ $secondaryColor }} 0%, {{ $primaryColor }} 100%);
            --shadow-glow: 0 0 30px rgba({{ $primaryColorRgb }}, 0.3);
            --primary-dark-rgb: {{ $primaryDarkRgb }};
            --primary-color-rgb: {{ $primaryColorRgb }};
            --secondary-color-rgb: {{ $secondaryColorRgb }};
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
        }
        
        .news-article-page {
            min-height: 100vh;
            background: var(--article-bg-color, #FFFFFF);
            padding-top: 80px;
        }
        
        /* توسيط محتوى صفحة الخبر */
        .news-article-page .container {
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
            width: 100%;
            box-sizing: border-box;
        }
        
        .article-header {
            background: linear-gradient(135deg, rgba(var(--primary-color-rgb, 95, 179, 142), 0.1) 0%, rgba(var(--primary-dark-rgb, 31, 107, 79), 0.1) 100%);
            padding: 4rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .article-header .container {
            text-align: center;
        }
        
        .article-breadcrumb {
            margin-bottom: 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .article-breadcrumb a {
            color: var(--article-button-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .article-breadcrumb a:hover {
            color: var(--article-button-hover-color);
        }
        
        .article-breadcrumb i {
            margin: 0 0.5rem;
            color: var(--text-secondary, rgba(255, 255, 255, 0.5)) !important;
        }
        
        .article-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--article-title-color, #000000) !important;
            margin-bottom: 1.5rem;
            line-height: 1.3;
        }
        
        .article-meta {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
            color: var(--article-meta-color, #000000) !important;
            font-size: 1rem;
        }
        
        .article-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .article-meta-item i {
            color: var(--article-button-color);
        }
        
        .article-featured-image {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            border-radius: 20px;
            margin: 3rem 0;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            border: 2px solid rgba(255, 255, 255, 0.1);
        }
        
        .article-content-wrapper {
            max-width: 900px;
            margin: 0 auto;
            padding: 4rem 2rem;
        }
        
        .article-content {
            background: var(--article-card-bg-color, #FFFFFF);
            backdrop-filter: blur(10px);
            border-radius: 25px;
            border: 1px solid rgba(var(--article-card-border-color-rgb, 31, 107, 79), 0.2);
            padding: 3rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            line-height: 2;
            font-size: 1.15rem;
            color: var(--article-text-color, #000000) !important;
        }
        
        .article-content p {
            margin-bottom: 1.5rem;
            color: inherit;
        }
        
        .article-content h2,
        .article-content h3,
        .article-content h4 {
            color: var(--article-title-color, #000000) !important;
            margin-top: 2rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }
        
        .article-content h2 {
            font-size: 2rem;
        }
        
        .article-content h3 {
            font-size: 1.5rem;
        }
        
        .article-content ul,
        .article-content ol {
            margin: 1.5rem 0;
            padding-right: 2rem;
        }
        
        .article-content li {
            margin-bottom: 0.75rem;
        }
        
        .article-content img {
            max-width: 100%;
            height: auto;
            border-radius: 15px;
            margin: 2rem 0;
        }
        
        .article-content blockquote {
            border-right: 4px solid var(--article-border-color);
            padding-right: 1.5rem;
            margin: 2rem 0;
            font-style: italic;
            color: var(--article-text-color) !important;
            background: rgba(var(--article-button-color-rgb), 0.1);
            padding: 1.5rem;
            border-radius: 10px;
        }
        
        /* روابط وأزرار داخل المحتوى تتكيف مع ألوان الموقع */
        .article-content a {
            color: var(--article-button-color) !important;
            text-decoration: none;
            border-bottom: 1px solid rgba(var(--article-button-color-rgb), 0.4);
            transition: color 0.3s ease, border-color 0.3s ease, background 0.3s ease;
        }
        .article-content a:hover {
            color: var(--article-button-hover-color) !important;
            border-bottom-color: var(--article-button-hover-color);
        }
        .article-content .btn,
        .article-content button,
        .article-content input[type="submit"],
        .article-content input[type="button"] {
            background: var(--article-button-color) !important;
            border-color: var(--article-button-color) !important;
            color: #fff !important;
            transition: all 0.3s ease;
        }
        .article-content .btn:hover,
        .article-content button:hover,
        .article-content input[type="submit"]:hover,
        .article-content input[type="button"]:hover {
            background: var(--article-button-hover-color) !important;
            border-color: var(--article-button-hover-color) !important;
            color: #fff !important;
        }
        
        .article-footer {
            margin-top: 4rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .article-share {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .article-share-label {
            color: var(--article-meta-color, #000000);
            font-weight: 600;
        }
        
        .article-share-buttons {
            display: flex;
            gap: 0.75rem;
        }
        
        .share-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(var(--article-button-color-rgb), 0.15);
            border: 1px solid rgba(var(--article-button-color-rgb), 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--article-button-color);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .share-btn:hover {
            background: var(--article-button-color);
            border-color: var(--article-button-color);
            color: #fff;
            transform: translateY(-3px);
        }
        
        .back-to-news {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--article-button-color);
            text-decoration: none;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            background: rgba(var(--article-button-color-rgb), 0.12);
            border-radius: 25px;
            border: 1px solid rgba(var(--article-button-color-rgb), 0.35);
            transition: all 0.3s ease;
        }
        
        .back-to-news:hover {
            background: var(--article-button-hover-color);
            color: #fff;
            transform: translateX(-5px);
        }
        
        .related-news-section {
            background: rgba(255, 255, 255, 0.03);
            padding: 4rem 0;
            margin-top: 4rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .related-news-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary) !important;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .related-news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .related-news-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.4s ease;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        
        .related-news-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(var(--article-button-color-rgb), 0.25);
            border-color: var(--article-button-color);
        }
        
        .related-news-card-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .related-news-card-content {
            padding: 1.5rem;
        }
        
        .related-news-card-date {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .related-news-card-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-primary) !important;
            margin: 0;
            line-height: 1.4;
        }
        
        /* تنسيق صفحة الخبر على الموبايل */
        @media (max-width: 768px) {
            .news-article-page {
                padding-top: 70px;
            }
            
            .news-article-page .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            
            .article-header {
                padding: 2rem 0;
            }
            
            .article-header .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            
            .article-breadcrumb {
                font-size: 0.85rem;
                gap: 0.35rem;
                margin-bottom: 1.25rem;
            }
            
            .article-breadcrumb i {
                margin: 0 0.25rem;
            }
            
            .article-title {
                font-size: 1.5rem;
                line-height: 1.35;
                margin-bottom: 1rem;
                padding: 0 0.25rem;
            }
            
            .article-meta {
                gap: 1rem;
                font-size: 0.9rem;
            }
            
            .article-featured-image {
                margin: 1.5rem 0;
                border-radius: 12px;
                max-height: 280px;
            }
            
            .article-content-wrapper {
                padding: 1.5rem 0.5rem;
            }
            
            .article-content {
                padding: 1.5rem 1rem;
                font-size: 1rem;
                line-height: 1.85;
                border-radius: 16px;
            }
            
            .article-content h2 {
                font-size: 1.5rem;
                margin-top: 1.5rem;
            }
            
            .article-content h3 {
                font-size: 1.25rem;
            }
            
            .article-content ul,
            .article-content ol {
                padding-right: 1.5rem;
                margin: 1rem 0;
            }
            
            .article-content img {
                margin: 1rem 0;
                border-radius: 10px;
            }
            
            .article-content blockquote {
                padding: 1rem;
                margin: 1rem 0;
            }
            
            .article-footer {
                flex-direction: column;
                align-items: stretch;
                gap: 1rem;
                margin-top: 2.5rem;
                padding-top: 1.5rem;
            }
            
            .article-share {
                flex-wrap: wrap;
            }
            
            .article-share-label {
                width: 100%;
            }
            
            .share-btn {
                width: 40px;
                height: 40px;
            }
            
            .back-to-news {
                justify-content: center;
                padding: 0.65rem 1rem;
                font-size: 0.95rem;
            }
            
            .related-news-section {
                padding: 2.5rem 0;
                margin-top: 2.5rem;
            }
            
            .related-news-title {
                font-size: 1.5rem;
                margin-bottom: 1.5rem;
                padding: 0 0.5rem;
            }
            
            .related-news-grid {
                grid-template-columns: 1fr;
                padding: 0 0.5rem;
                gap: 1.25rem;
            }
            
            .related-news-card-content {
                padding: 1rem;
            }
            
            .related-news-card-title {
                font-size: 1.05rem;
            }
        }
        
        /* شاشات صغيرة جداً */
        @media (max-width: 480px) {
            .article-title {
                font-size: 1.3rem;
            }
            
            .article-meta {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .article-content {
                padding: 1.25rem 0.85rem;
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body>
    @include('frontend.partials.header')

    <div class="news-article-page">
        <!-- Article Header -->
        <div class="article-header">
            <div class="container">
                <div class="article-breadcrumb">
                    <a href="{{ url('/') }}"><i class="fas fa-home"></i> الرئيسية</a>
                    <i class="fas fa-chevron-left"></i>
                    <a href="{{ url('/#news') }}">الأخبار</a>
                    <i class="fas fa-chevron-left"></i>
                    <span style="color: var(--text-secondary);">قراءة الخبر</span>
                </div>
                
                <h1 class="article-title">{{ $news->title }}</h1>
                
                <div class="article-meta">
                    <div class="article-meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>{{ $news->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="article-meta-item">
                        <i class="fas fa-clock"></i>
                        <span>{{ $news->created_at->format('h:i A') }}</span>
                    </div>
                    <div class="article-meta-item">
                        <i class="fas fa-user"></i>
                        <span>الإدارة</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Article Content -->
        <div class="container">
            @if($news->image)
                <img src="{{ image_asset_url($news->image) }}" 
                     alt="{{ $news->title }}" 
                     class="article-featured-image">
            @endif
            
            <div class="article-content-wrapper">
                <div class="article-content">
                    {!! $news->content !!}
                </div>
                
                <div class="article-footer">
                    <a href="{{ url('/#news') }}" class="back-to-news">
                        <i class="fas fa-arrow-right"></i>
                        <span>العودة إلى الأخبار</span>
                    </a>
                    
                    <div class="article-share">
                        <span class="article-share-label">شارك الخبر:</span>
                        <div class="article-share-buttons">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" 
                               target="_blank" 
                               class="share-btn" 
                               title="شارك على فيسبوك">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($news->title) }}" 
                               target="_blank" 
                               class="share-btn" 
                               title="شارك على X">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="width: 1em; height: 1em; vertical-align: -0.125em;">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($news->title . ' ' . url()->current()) }}" 
                               target="_blank" 
                               class="share-btn" 
                               title="شارك على واتساب">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" 
                               target="_blank" 
                               class="share-btn" 
                               title="شارك على لينكد إن">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related News -->
        @if($relatedNews->count() > 0)
        <div class="related-news-section">
            <div class="container">
                <h2 class="related-news-title">أخبار ذات صلة</h2>
                <div class="related-news-grid">
                    @foreach($relatedNews as $related)
                        <a href="{{ route('frontend.news.article', $related->id) }}" class="related-news-card">
                            @if($related->image)
                                <img src="{{ image_asset_url($related->image) }}" 
                                     alt="{{ $related->title }}" 
                                     class="related-news-card-image">
                            @endif
                            <div class="related-news-card-content">
                                <div class="related-news-card-date">
                                    <i class="fas fa-calendar"></i>
                                    <span>{{ $related->created_at->format('Y-m-d') }}</span>
                                </div>
                                <h3 class="related-news-card-title">{{ $related->title }}</h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    @include('frontend.partials.footer')

    <script src="{{ asset('assets/js/frontend.js') }}?v={{ $settings['settings_updated_at'] ?? '1' }}"></script>
</body>
</html>

