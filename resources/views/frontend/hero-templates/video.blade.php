<!-- قالب الهيرو بالفيديو فقط -->
<section id="home" class="hero hero-template-video">
    @if(!empty($settings['hero_background_video']))
        @php $videoOpacity = ($settings['hero_background_video_opacity'] ?? 50) / 100; @endphp
        <video class="hero-background-video hero-video-fullscreen" 
               autoplay 
               muted 
               loop 
               playsinline 
               preload="auto"
               disablePictureInPicture
               controlsList="nodownload nofullscreen noremoteplayback"
               data-hero-opacity="{{ $videoOpacity }}"
               style="opacity: {{ $videoOpacity }}; width: 100%; height: 100%; object-fit: cover; filter: none; -webkit-filter: none;"
               poster="">
            <source src="{{ image_asset_url($settings['hero_background_video']) }}" type="video/mp4">
            <source src="{{ image_asset_url($settings['hero_background_video']) }}" type="video/webm">
            <source src="{{ image_asset_url($settings['hero_background_video']) }}" type="video/ogg">
        </video>
    @else
        <!-- فيديو افتراضي إذا لم يتم رفع فيديو -->
        <div class="hero-video-placeholder">
            <i class="fas fa-video"></i>
            <p>يرجى رفع فيديو للهيرو</p>
        </div>
    @endif
    
    <!-- نص اسم الموقع -->
    <div class="hero-video-title-wrapper">
        @php
            $showBackground = ($settings['hero_video_title_background'] ?? '1');
            $hasBackground = ($showBackground == '1' || $showBackground == 1 || $showBackground === true);
        @endphp
        <h1 class="hero-video-title {{ !$hasBackground ? 'no-background' : '' }}">
            {{ $settings['site_title'] ?? 'لوحة التحكم' }}
        </h1>
    </div>

    <!-- زر التواصل -->
    @if(($settings['hero_video_show_contact_button'] ?? '0') == '1')
    <div class="hero-video-contact-wrapper">
        <a href="#contact" class="hero-video-contact-button">
            <i class="fas fa-phone-alt"></i>
            <span>تواصل معنا</span>
        </a>
    </div>
    @endif
</section>

