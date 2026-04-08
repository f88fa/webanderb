<!-- قالب الهيرو الأساسي - إضافة hero-no-bg فقط عند عدم وجود صورة ولا فيديو لظهور الفيديو/الصورة والشفافية -->
<section id="home" class="hero hero-template-default {{ (empty($settings['hero_background_image']) && empty($settings['hero_background_video'])) ? 'hero-no-bg' : '' }}" 
         @if(!empty($settings['hero_background_image']))
         style="background-image: url('{{ image_asset_url($settings['hero_background_image']) }}'); background-size: cover; background-position: center; background-repeat: no-repeat;"
         @endif>
    @if(!empty($settings['hero_background_video']))
        @php $videoOpacity = ($settings['hero_background_video_opacity'] ?? 50) / 100; @endphp
        <video class="hero-background-video" 
               autoplay 
               muted 
               loop 
               playsinline 
               preload="auto"
               disablePictureInPicture
               controlsList="nodownload nofullscreen noremoteplayback"
               data-hero-opacity="{{ $videoOpacity }}"
               style="opacity: {{ $videoOpacity }};">
            <source src="{{ image_asset_url($settings['hero_background_video']) }}" type="video/mp4">
            <source src="{{ image_asset_url($settings['hero_background_video']) }}" type="video/webm">
            <source src="{{ image_asset_url($settings['hero_background_video']) }}" type="video/ogg">
        </video>
    @endif
    @if(!empty($settings['hero_background_image']))
        @php $imageOverlayOpacity = (100 - ($settings['hero_background_opacity'] ?? 30)) / 100; @endphp
        <div class="hero-background-overlay" data-hero-opacity="{{ $imageOverlayOpacity }}" style="opacity: {{ $imageOverlayOpacity }};"></div>
    @endif
    @if(!empty($settings['hero_background_video']))
        @php $videoOverlayOpacity = (100 - ($settings['hero_background_video_opacity'] ?? 50)) / 100; @endphp
        <div class="hero-background-overlay" data-hero-opacity="{{ $videoOverlayOpacity }}" style="opacity: {{ $videoOverlayOpacity }};"></div>
    @endif
    @if(empty($settings['hero_background_image']) && empty($settings['hero_background_video']))
    <div class="hero-background-overlay" style="opacity: 1;"></div>
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
            <div class="astro-planet" id="hero-planet">
                <div class="planet" id="hero-planet-icon">
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
</section>

