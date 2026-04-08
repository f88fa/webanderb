<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings['site_title'] ?? 'الموقع' }}</title>
    
    <!-- Favicon - استخدام نفس أيقونة الهيرو -->
    @if(!empty($settings['site_icon_file']))
        <link rel="icon" type="image/png" href="{{ image_asset_url($settings['site_icon_file']) }}">
        <link rel="shortcut icon" type="image/png" href="{{ image_asset_url($settings['site_icon_file']) }}">
        <link rel="apple-touch-icon" href="{{ image_asset_url($settings['site_icon_file']) }}">
    @elseif(!empty($settings['site_icon']))
        <!-- إذا لم تكن هناك صورة، نستخدم أيقونة SVG من Font Awesome -->
        <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>{{ $settings['site_icon'] == 'fas fa-rocket' ? '🚀' : '⭐' }}</text></svg>">
    @endif
    
    <link rel="stylesheet" href="{{ asset('assets/css/frontend.css') }}?v={{ $settings['settings_updated_at'] ?? '1' }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    @include('frontend.partials.site-theme-root')
</head>
<body>
    @include('frontend.partials.header')

    <!-- قسم الهيرو - اختيار القالب حسب الإعدادات -->
    @php
        $heroTemplateType = $settings['hero_template_type'] ?? 'default';
    @endphp
    
    @if($heroTemplateType === 'video')
        @include('frontend.hero-templates.video')
    @elseif($heroTemplateType === 'slider')
        @include('frontend.hero-templates.slider')
    @else
        @include('frontend.hero-templates.default')
    @endif

    {{-- القالب القديم (محفوظ للرجوع) --}}
    {{-- <section id="home" class="hero" 
             @if(!empty($settings['hero_background_image']))
             style="background-image: url('{{ image_asset_url($settings['hero_background_image']) }}'); background-size: cover; background-position: center; background-repeat: no-repeat;"
             @endif>
        @if(!empty($settings['hero_background_video']))
            <video class="hero-background-video" 
                   autoplay 
                   muted 
                   loop 
                   playsinline 
                   preload="auto"
                   disablePictureInPicture
                   controlsList="nodownload nofullscreen noremoteplayback"
                   style="opacity: {{ ($settings['hero_background_video_opacity'] ?? 50) / 100 }};">
                <source src="{{ image_asset_url($settings['hero_background_video']) }}" type="video/mp4">
                <source src="{{ image_asset_url($settings['hero_background_video']) }}" type="video/webm">
                <source src="{{ image_asset_url($settings['hero_background_video']) }}" type="video/ogg">
            </video>
        @endif
        @if(!empty($settings['hero_background_image']))
        <div class="hero-background-overlay" style="opacity: {{ (100 - ($settings['hero_background_opacity'] ?? 30)) / 100 }};"></div>
        @endif
        @if(!empty($settings['hero_background_video']))
        <div class="hero-background-overlay" style="opacity: {{ (100 - ($settings['hero_background_video_opacity'] ?? 50)) / 100 }};"></div>
        @endif
        <div class="stars"></div>
        <div class="stars2"></div>
        <div class="stars3"></div>
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">
                    {{ $settings['site_title'] ?? 'لوحة التحكم' }}
                </h1>
                <p class="hero-description" id="typing-description" data-text="{{ $settings['site_description'] ?? 'لوحة تحكم احترافية' }}">
                    <span id="typing-text"></span><span class="typing-cursor">|</span>
                </p>
                <div class="hero-buttons">
                    <a href="#about" class="btn btn-primary">
                        <span>اكتشف المزيد</span>
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <a href="#news" class="btn btn-secondary">
                        <span>الأخبار</span>
                        <i class="fas fa-newspaper"></i>
                    </a>
                </div>
                
                <!-- أيقونات التواصل الاجتماعي -->
                @if(isset($settings['social_facebook']) || isset($settings['social_twitter']) || isset($settings['social_instagram']) || 
                    isset($settings['social_linkedin']) || isset($settings['social_youtube']) || isset($settings['social_whatsapp']) || 
                    isset($settings['social_telegram']))
                <div class="hero-social-links">
                    @if(!empty($settings['social_facebook']))
                    <a href="{{ $settings['social_facebook'] }}" target="_blank" rel="noopener noreferrer" class="social-link" data-platform="facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    @endif
                    @if(!empty($settings['social_twitter']))
                    <a href="{{ $settings['social_twitter'] }}" target="_blank" rel="noopener noreferrer" class="social-link" data-platform="twitter">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="width: 1em; height: 1em; vertical-align: -0.125em;">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </a>
                    @endif
                    @if(!empty($settings['social_instagram']))
                    <a href="{{ $settings['social_instagram'] }}" target="_blank" rel="noopener noreferrer" class="social-link" data-platform="instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    @endif
                    @if(!empty($settings['social_linkedin']))
                    <a href="{{ $settings['social_linkedin'] }}" target="_blank" rel="noopener noreferrer" class="social-link" data-platform="linkedin">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    @endif
                    @if(!empty($settings['social_youtube']))
                    <a href="{{ $settings['social_youtube'] }}" target="_blank" rel="noopener noreferrer" class="social-link" data-platform="youtube">
                        <i class="fab fa-youtube"></i>
                    </a>
                    @endif
                    @if(!empty($settings['social_whatsapp']))
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['social_whatsapp']) }}" target="_blank" rel="noopener noreferrer" class="social-link" data-platform="whatsapp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    @endif
                    @if(!empty($settings['social_telegram']))
                    <a href="{{ $settings['social_telegram'] }}" target="_blank" rel="noopener noreferrer" class="social-link" data-platform="telegram">
                        <i class="fab fa-telegram-plane"></i>
                    </a>
                    @endif
                </div>
                @endif
            </div>
            <div class="hero-image">
                <div class="astro-planet">
                    <div class="planet">
                        @if(!empty($settings['site_icon_file']))
                            <img src="{{ image_asset_url($settings['site_icon_file']) }}" alt="أيقونة الموقع" class="planet-icon-image">
                        @else
                            <i class="{{ $settings['site_icon'] ?? 'fas fa-rocket' }}"></i>
                        @endif
                    </div>
                    <div class="orbit"></div>
                    <div class="orbit2"></div>
                </div>
            </div>
        </div>
        <div class="scroll-indicator">
            <i class="fas fa-chevron-down"></i>
        </div>
    </section> --}}

    <!-- الأقسام الديناميكية -->
    @if(isset($sectionOrder) && $sectionOrder->count() > 0)
        @php
            // إعادة ترتيب الأقسام لضمان ظهور الأخبار بعد الخدمات مباشرة
            $sections = $sectionOrder->all();
            $servicesIndex = null;
            $newsSection = null;
            $newsIndex = null;
            
            // البحث عن مواضع الخدمات والأخبار
            foreach ($sections as $index => $section) {
                if ($section->section_key == 'services') {
                    $servicesIndex = $index;
                }
                if ($section->section_key == 'news') {
                    $newsIndex = $index;
                    $newsSection = $section;
                }
            }
            
            // إذا كان هناك خدمات وأخبار، نعيد ترتيبها
            if ($servicesIndex !== null && $newsIndex !== null && $newsIndex != $servicesIndex + 1) {
                // إزالة الأخبار من موضعها الحالي
                unset($sections[$newsIndex]);
                
                // إعادة ترتيب المصفوفة
                $sections = array_values($sections);
                
                // إعادة تحديد موضع الخدمات بعد إعادة الترتيب
                $newServicesIndex = null;
                foreach ($sections as $index => $section) {
                    if ($section->section_key == 'services') {
                        $newServicesIndex = $index;
                        break;
                    }
                }
                
                // إدراج الأخبار بعد الخدمات مباشرة
                if ($newServicesIndex !== null && $newsSection) {
                    array_splice($sections, $newServicesIndex + 1, 0, [$newsSection]);
                }
            }
        @endphp
        @foreach($sections as $section)
            @php $sectionKey = $section->section_key ?? $section['section_key'] ?? ''; @endphp
            @if($sectionKey == 'about')
                @include('frontend.sections.about')
            @elseif($sectionKey == 'vision_mission')
                @include('frontend.sections.vision-mission')
            @elseif(str_starts_with($sectionKey, 'banner_section_'))
                @php
                    $bannerId = (int) str_replace('banner_section_', '', $sectionKey);
                    $banner = isset($bannerSections) ? $bannerSections->firstWhere('id', $bannerId) : null;
                @endphp
                @if($banner && (!isset($settings['section_banner_sections_visible']) || $settings['section_banner_sections_visible'] == '1'))
                    @include('frontend.sections.banner-item', ['banner' => $banner])
                @endif
            @elseif($sectionKey == 'services')
                @include('frontend.sections.services')
            @elseif($sectionKey == 'news')
                @include('frontend.sections.news')
            @elseif($sectionKey == 'projects')
                @include('frontend.sections.projects')
            @elseif($sectionKey == 'media')
                @include('frontend.sections.media')
            @elseif($sectionKey == 'testimonials')
                @include('frontend.sections.testimonials')
            @elseif($sectionKey == 'partners')
                @include('frontend.sections.partners')
            @elseif($sectionKey == 'reports')
                @include('frontend.sections.reports')
            @endif
        @endforeach
    @else
        {{-- Fallback to default order --}}
        @include('frontend.sections.about')
        @include('frontend.sections.vision-mission')
        @include('frontend.sections.banner-sections')
        @include('frontend.sections.services')
        @include('frontend.sections.news')
        @include('frontend.sections.projects')
        @include('frontend.sections.media')
        @include('frontend.sections.testimonials')
        @include('frontend.sections.partners')
        @include('frontend.sections.reports')
    @endif

    <!-- قسم اتصل بنا -->
    @php
        $contactSecStyle = '--sec-bg: '.($settings['section_contact_bg_color'] ?? '#FFFFFF').'; --sec-text: '.($settings['section_contact_text_color'] ?? '#0F3D2E').'; --sec-title: '.($settings['section_contact_title_color'] ?? '#5FB38E').'; --sec-icon: '.($settings['section_contact_icon_color'] ?? '#5FB38E').'; --sec-card-bg: '.($settings['section_contact_card_bg_color'] ?? '#FFFFFF').'; --sec-card-title: '.($settings['section_contact_card_title_color'] ?? '#5FB38E').'; --sec-hover-text: '.($settings['section_contact_hover_text_color'] ?? '#5FB38E').';';
    @endphp
    <section id="contact" class="contact-section" style="{{ $contactSecStyle }}">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">اتصل بنا</span>
                <h2 class="section-title">تواصل معنا</h2>
                <p class="section-description">نحن هنا للإجابة على استفساراتك</p>
            </div>
            
            <div class="contact-info">
                @if(!empty($settings['contact_email']))
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-details">
                            <h4>البريد الإلكتروني</h4>
                            <a href="mailto:{{ $settings['contact_email'] }}">
                                {{ $settings['contact_email'] }}
                            </a>
                        </div>
                    </div>
                @endif
                
                @if(!empty($settings['contact_phone']))
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-details">
                            <h4>رقم الهاتف</h4>
                            <a href="tel:{{ $settings['contact_phone'] }}">
                                {{ $settings['contact_phone'] }}
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- التذييل -->
    @include('frontend.partials.footer')

    <!-- الأزرار المتحركة -->
    @if(isset($settings['floating_whatsapp_enabled']) && $settings['floating_whatsapp_enabled'] == '1' && !empty($settings['floating_whatsapp_number']))
    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['floating_whatsapp_number']) }}" 
       target="_blank" 
       rel="noopener noreferrer"
       class="floating-button floating-whatsapp"
       title="تواصل معنا عبر الواتساب">
        <i class="fab fa-whatsapp"></i>
        <span class="floating-button-text">واتساب</span>
    </a>
    @endif

    @if(isset($settings['floating_donate_enabled']) && $settings['floating_donate_enabled'] == '1' && !empty($settings['floating_donate_link']))
    <a href="{{ $settings['floating_donate_link'] }}" 
       target="_blank" 
       rel="noopener noreferrer"
       class="floating-button floating-donate"
       title="{{ $settings['floating_donate_text'] ?? 'تبرع الآن' }}">
        <i class="fas fa-heart"></i>
        <span class="floating-button-text">{{ $settings['floating_donate_text'] ?? 'تبرع الآن' }}</span>
    </a>
    @endif

    <script src="{{ asset('assets/js/frontend.js') }}?v={{ $settings['settings_updated_at'] ?? '1' }}"></script>
</body>
</html>

